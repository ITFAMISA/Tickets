<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// LOGS EN TIEMPO REAL - Configuración
$log_file = __DIR__ . '/api_debug.log';

function debug_log($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $formatted_message = "[{$timestamp}] {$message}" . PHP_EOL;
    
    // Escribir a archivo Y mostrar en error_log
    file_put_contents($log_file, $formatted_message, FILE_APPEND | LOCK_EX);
    error_log($message);
}

debug_log("🚀 API PHP iniciado - Versión simple");

// Configuración de la base de datos
$db_config = [
    'host' => 'localhost',
    'port' => 3306,
    'username' => 'it',
    'password' => 'fami123.',
    'database' => 'sistema_tickets_it'
];

// Configuración de Firebase Cloud Messaging v1
$project_id = "itmobil-f7e3d";
$service_account_file = __DIR__ . "/service-account-key.json";

// Configuración de archivos
$upload_config = [
    'max_file_size' => 100 * 1024 * 1024, // 100MB
    'allowed_types' => [
        // Imágenes
        'jpg', 'jpeg', 'png', 'gif', 'webp',
        // Documentos
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt',
        // Videos
        'mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'
    ],
    'upload_path' => __DIR__ . '/uploads/tickets/'
];

debug_log("📁 Buscando service account en: " . $service_account_file);

function get_db_connection() {
    global $db_config;
    try {
        debug_log("🔌 Intentando conectar a base de datos...");
        $dsn = "mysql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $db_config['username'], $db_config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        debug_log("✅ Conexión a BD exitosa");
        return $pdo;
    } catch (PDOException $e) {
        debug_log("❌ Error conectando a DB: " . $e->getMessage());
        return null;
    }
}

function send_json_response($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data);
    debug_log("📤 Respuesta enviada: HTTP {$status_code}");
    exit();
}

function get_fcm_tokens() {
    try {
        $pdo = get_db_connection();
        if (!$pdo) return [];
        
        // Crear tabla si no existe
        $pdo->exec("CREATE TABLE IF NOT EXISTS fcm_tokens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            token VARCHAR(500) UNIQUE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_used TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Crear tabla de archivos si no existe
        $pdo->exec("CREATE TABLE IF NOT EXISTS ticket_attachments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ticket_id INT NOT NULL,
            filename VARCHAR(255) NOT NULL,
            original_name VARCHAR(255) NOT NULL,
            file_size INT NOT NULL,
            mime_type VARCHAR(100) NOT NULL,
            upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX(ticket_id)
        )");
        
        $stmt = $pdo->query("SELECT token FROM fcm_tokens ORDER BY last_used DESC");
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        debug_log("💾 Tokens obtenidos de BD: " . count($result));
        return $result;
        
    } catch (Exception $e) {
        debug_log("❌ Error obteniendo tokens: " . $e->getMessage());
        return [];
    }
}

function save_fcm_token($token) {
    try {
        $pdo = get_db_connection();
        if (!$pdo) return false;
        
        $stmt = $pdo->prepare("INSERT INTO fcm_tokens (token) VALUES (?) ON DUPLICATE KEY UPDATE last_used = NOW()");
        $result = $stmt->execute([$token]);
        
        debug_log("💾 Token guardado en BD: " . ($result ? "✅" : "❌"));
        return $result;
        
    } catch (Exception $e) {
        debug_log("❌ Error guardando token: " . $e->getMessage());
        return false;
    }
}

function get_access_token_simple() {
    global $service_account_file;
    
    try {
        if (!file_exists($service_account_file)) {
            debug_log("❌ Archivo service account no encontrado: " . $service_account_file);
            return null;
        }
        
        debug_log("🔍 Leyendo service account...");
        $service_account = json_decode(file_get_contents($service_account_file), true);
        
        if (!$service_account) {
            debug_log("❌ No se pudo parsear service account JSON");
            return null;
        }
        
        // Crear JWT manualmente
        $now = time();
        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $payload = json_encode([
            'iss' => $service_account['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ]);
        
        $header_encoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $payload_encoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature_input = $header_encoded . '.' . $payload_encoded;
        
        // Firmar con clave privada
        $private_key = openssl_pkey_get_private($service_account['private_key']);
        if (!$private_key) {
            debug_log("❌ Error cargando clave privada");
            return null;
        }
        
        openssl_sign($signature_input, $signature, $private_key, OPENSSL_ALGO_SHA256);
        $signature_encoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        $jwt = $signature_input . '.' . $signature_encoded;
        
        debug_log("🔐 JWT creado, intercambiando por access token...");
        
        // Intercambiar JWT por access token usando file_get_contents
        $post_data = http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]);
        
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $post_data,
                'timeout' => 30
            ]
        ]);
        
        $response = file_get_contents('https://oauth2.googleapis.com/token', false, $context);
        $http_code = 200; // file_get_contents no da código HTTP directo
        
        if ($response === false) {
            debug_log("❌ Error en file_get_contents para OAuth");
            return null;
        }
        
        debug_log("📨 OAuth response recibido");
        
        if ($http_code == 200) {
            $token_data = json_decode($response, true);
            if (isset($token_data['access_token'])) {
                debug_log("✅ Access token obtenido exitosamente");
                return $token_data['access_token'];
            }
        }
        
        debug_log("❌ Error obteniendo access token: " . $response);
        return null;
        
    } catch (Exception $e) {
        debug_log("❌ Error en get_access_token_simple: " . $e->getMessage());
        return null;
    }
}

function send_fcm_notification($title, $body, $ticket_data = []) {
    global $project_id;
    
    debug_log("🔔 ==> INICIANDO ENVÍO DE NOTIFICACIÓN <==");
    debug_log("🔍 Título: " . $title);
    debug_log("🔍 Body: " . $body);
    debug_log("🔍 Project ID: " . $project_id);
    
    // Verificar service account
    global $service_account_file;
    if (!file_exists($service_account_file)) {
        debug_log("❌ CRÍTICO: Service account no existe en: " . $service_account_file);
        return;
    }
    debug_log("✅ Service account encontrado");
    
    $tokens = get_fcm_tokens();
    debug_log("🔍 Tokens obtenidos de BD: " . count($tokens));
    
    if (empty($tokens)) {
        debug_log("⚠️ No hay tokens FCM registrados en BD - Continuando sin notificación");
        return;
    }
    
    debug_log("🔐 Obteniendo access token...");
    $access_token = get_access_token_simple();
    if (!$access_token) {
        debug_log("❌ No se pudo obtener access token - ABORTANDO notificación");
        return false; // Cambiar return para indicar falla
    }
    
    $fcm_url = "https://fcm.googleapis.com/v1/projects/{$project_id}/messages:send";
    $headers = [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json',
    ];
    
    foreach ($tokens as $index => $token) {
        debug_log("📱 Procesando token #{$index}: " . substr($token, 0, 20) . "...");
        
        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $ticket_data,
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'icon' => 'ic_launcher',
                        'sound' => 'default',
                        'channel_id' => 'tickets_channel'
                    ]
                ]
            ]
        ];
        
        debug_log("🚀 Enviando notificación...");
        
        $json_payload = json_encode($payload);
        debug_log("🔍 FCM URL: " . $fcm_url);
        debug_log("🔍 Headers: " . implode(", ", $headers));
        debug_log("🔍 Payload: " . $json_payload);
        
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers) . "\r\n",
                'content' => $json_payload,
                'timeout' => 30,
                'ignore_errors' => true  // Importante: capturar respuesta aunque haya error HTTP
            ]
        ]);
        
        $response = file_get_contents($fcm_url, false, $context);
        
        // Obtener código de respuesta HTTP
        $response_code = 'unknown';
        if (isset($http_response_header)) {
            $response_code = $http_response_header[0];
        }
        
        debug_log("📊 Response Code: " . $response_code);
        debug_log("📄 Response Body: " . ($response !== false ? $response : 'Error en file_get_contents'));
        
        if ($response !== false) {
            $response_data = json_decode($response, true);
            if ($response_data && !isset($response_data['error'])) {
                debug_log("✅ Notificación enviada exitosamente: " . $title);
            } else {
                debug_log("❌ Error en respuesta FCM: " . $response);
                // Debug adicional para ver el error específico
                if ($response_data && isset($response_data['error'])) {
                    debug_log("❌ Error FCM Details: " . json_encode($response_data['error']));
                }
            }
        } else {
            debug_log("❌ Error enviando notificación con file_get_contents");
        }
    }
    
    debug_log("🏁 ==> FIN DEL ENVÍO DE NOTIFICACIÓN <==");
}

// Router principal
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

debug_log("🌐 REQUEST: {$method} {$request_uri}");

// Mejor parsing de input JSON con múltiples formatos
$raw_input = file_get_contents('php://input');
$input = json_decode($raw_input, true);

// Fallback: Si JSON falla, intentar con POST data
if (json_last_error() !== JSON_ERROR_NONE && !empty($_POST)) {
    debug_log("⚠️ JSON parse failed, using POST data");
    $input = $_POST;
}

// Fallback: Si aún está vacío, intentar FormData
if (empty($input) && $method === 'POST') {
    // Para multipart/form-data (archivos)
    if (!empty($_POST)) {
        $input = $_POST;
        debug_log("📥 Using POST data instead of JSON");
    }
}

// Debug del input
if ($method === 'POST') {
    debug_log("📥 Raw input: " . substr($raw_input, 0, 500) . (strlen($raw_input) > 500 ? '...' : ''));
    debug_log("📥 Parsed input: " . json_encode($input));
    debug_log("📥 JSON error: " . json_last_error_msg());
    debug_log("📥 Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'not set'));
}

// Manejar múltiples posibles rutas base para compatibilidad móvil
$possible_bases = ['/Ticket/api.php', '/web/Ticket/api.php', '/xampp/htdocs/Ticket/api.php'];
foreach ($possible_bases as $base_path) {
    if (strpos($path, $base_path) === 0) {
        $path = substr($path, strlen($base_path));
        break;
    }
}

debug_log("🛣️ Parsed path: " . $path);

switch ($path) {
    case '/api/tickets':
        if ($method === 'GET') {
            get_tickets();
        }
        break;
        
    case '/api/create_ticket':
        if ($method === 'POST') {
            create_ticket($input);
        }
        break;
        
    case '/api/register_token':
        if ($method === 'POST') {
            register_fcm_token($input);
        }
        break;
        
    case '/api/users_it':
        if ($method === 'GET') {
            get_users_it();
        }
        break;
        
    case '/api/assign_ticket':
        if ($method === 'POST') {
            assign_ticket($input);
        }
        break;
        
    case '/api/resolve_ticket':
        if ($method === 'POST') {
            resolve_ticket($input);
        }
        break;
        
    case '/api/close_ticket':
        if ($method === 'POST') {
            close_ticket($input);
        }
        break;
        
    case '/api/update_estado':
        if ($method === 'POST') {
            update_ticket_estado($input);
        }
        break;
        
    case '/api/upload_file':
        if ($method === 'POST') {
            upload_file();
        }
        break;
        
    case (preg_match('/^\/api\/tickets\/(\d+)\/files$/', $path, $matches) ? true : false):
        if ($method === 'GET') {
            get_ticket_files($matches[1]);
        }
        break;
        
    case '/api/stats':
        if ($method === 'GET') {
            get_stats();
        }
        break;
        
    case '/api/debug_fcm':
        if ($method === 'GET') {
            debug_fcm_status();
        }
        break;
        
    case '/api/test_notification':
        if ($method === 'POST') {
            test_notification($input);
        }
        break;
        
    case '/health':
    case '':
        if ($method === 'GET') {
            $tokens = get_fcm_tokens();
            send_json_response([
                'status' => 'ok', 
                'service' => 'ticket-api-php-simple',
                'version' => 'v3-simple',
                'tokens_registered' => count($tokens),
            ]);
        }
        break;
        
    default:
        debug_log("❓ Endpoint no encontrado: " . $path);
        send_json_response(['error' => 'Endpoint no encontrado: ' . $path], 404);
}

function get_tickets() {
    try {
        $pdo = get_db_connection();
        if (!$pdo) {
            send_json_response(['error' => 'No se pudo conectar a la base de datos'], 500);
        }
        
        $query = "SELECT t.*, u.nombre as asignado_nombre FROM tickets t LEFT JOIN usuarios u ON t.asignado_a = u.id WHERE 1=1";
        $params = [];
        
        if (isset($_GET['estado']) && !empty($_GET['estado'])) {
            $query .= " AND t.estado = ?";
            $params[] = $_GET['estado'];
        }
        
        $query .= " ORDER BY t.fecha_creacion DESC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        debug_log("✅ Tickets encontrados: " . count($tickets));
        send_json_response($tickets);
        
    } catch (Exception $e) {
        debug_log("❌ Error en get_tickets: " . $e->getMessage());
        send_json_response(['error' => $e->getMessage()], 500);
    }
}

function create_ticket($data) {
    global $upload_config;
    
    try {
        debug_log("🎫 ==> CREANDO NUEVO TICKET <==");
        debug_log("📥 Datos recibidos: " . json_encode($data));
        
        // Verificar si $data es null o está vacío
        if (empty($data)) {
            debug_log("❌ CRÍTICO: Data está vacío o es null");
            send_json_response(['error' => 'Datos de ticket requeridos'], 400);
            return;
        }
        
        // Mapear posibles nombres de campos (para compatibilidad móvil)
        $field_mapping = [
            'titulo' => ['titulo', 'title'],
            'descripcion' => ['descripcion', 'description'], 
            'categoria' => ['categoria', 'category'],
            'prioridad' => ['prioridad', 'priority'],
            'solicitante_nombre' => ['solicitante_nombre', 'solicitante', 'requester_name', 'user_name']
        ];
        
        // Normalizar datos usando el mapeo
        $normalized_data = [];
        foreach ($field_mapping as $standard_field => $possible_names) {
            foreach ($possible_names as $possible_name) {
                if (isset($data[$possible_name]) && !empty($data[$possible_name])) {
                    $normalized_data[$standard_field] = $data[$possible_name];
                    break;
                }
            }
        }
        
        debug_log("📥 Datos normalizados: " . json_encode($normalized_data));
        
        // Verificar campos requeridos en datos normalizados
        $required_fields = ['titulo', 'descripcion', 'categoria', 'solicitante_nombre'];
        foreach ($required_fields as $field) {
            if (!isset($normalized_data[$field]) || empty($normalized_data[$field])) {
                debug_log("❌ Campo requerido faltante: " . $field);
                debug_log("📥 Campos disponibles: " . implode(', ', array_keys($data)));
                send_json_response(['error' => "Campo requerido faltante: $field. Campos disponibles: " . implode(', ', array_keys($data))], 400);
                return;
            }
        }
        
        // Usar datos normalizados
        $data = $normalized_data;
        
        $pdo = get_db_connection();
        if (!$pdo) {
            send_json_response(['error' => 'No se pudo conectar a la base de datos'], 500);
        }
        
        // Crear el ticket primero
        $query = "INSERT INTO tickets (titulo, descripcion, categoria, prioridad, solicitante_nombre, estado, fecha_creacion) VALUES (?, ?, ?, ?, ?, 'abierto', NOW())";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            $data['titulo'],
            $data['descripcion'],
            $data['categoria'],
            $data['prioridad'] ?? 'media',
            $data['solicitante_nombre']
        ]);
        
        $ticket_id = $pdo->lastInsertId();
        debug_log("✅ Ticket creado exitosamente:");
        debug_log("   📋 ID: " . $ticket_id);
        debug_log("   📌 Título: " . $data['titulo']);
        debug_log("   📂 Categoría: " . $data['categoria']);
        debug_log("   ⚡ Prioridad: " . ($data['prioridad'] ?? 'media'));
        
        $uploaded_files = [];
        
        // Procesar archivos si los hay
        if (isset($_FILES) && !empty($_FILES)) {
            debug_log("📁 Procesando archivos adjuntos...");
            
            // Crear directorio si no existe
            $upload_dir = $upload_config['upload_path'] . date('Y/m/');
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Procesar cada archivo
            foreach ($_FILES as $key => $file) {
                if ($file['error'] === UPLOAD_ERR_OK) {
                    debug_log("📄 Procesando archivo: " . $file['name']);
                    
                    // Validar archivo
                    $validation_errors = validate_file($file);
                    if (!empty($validation_errors)) {
                        debug_log("❌ Error validando archivo: " . implode(', ', $validation_errors));
                        continue; // Saltar este archivo pero continuar con el ticket
                    }
                    
                    // Generar nombre único
                    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $unique_filename = 'ticket_' . $ticket_id . '_' . uniqid() . '.' . $file_extension;
                    $file_path = $upload_dir . $unique_filename;
                    
                    // Mover archivo
                    if (move_uploaded_file($file['tmp_name'], $file_path)) {
                        // Guardar en BD
                        $stmt = $pdo->prepare("INSERT INTO ticket_attachments (ticket_id, filename, original_name, file_size, mime_type) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([
                            $ticket_id,
                            $unique_filename,
                            $file['name'],
                            $file['size'],
                            $file['type']
                        ]);
                        
                        $uploaded_files[] = [
                            'id' => $pdo->lastInsertId(),
                            'filename' => $unique_filename,
                            'original_name' => $file['name'],
                            'size' => $file['size']
                        ];
                        
                        debug_log("✅ Archivo subido: " . $unique_filename);
                    } else {
                        debug_log("❌ Error moviendo archivo: " . $file['name']);
                    }
                }
            }
        }
        
        // Enviar notificación push
        $notification_title = "🎫 Nuevo Ticket - " . strtoupper($data['prioridad'] ?? 'MEDIA');
        $notification_body = "📁 {$data['categoria']} - {$data['solicitante_nombre']} - {$data['titulo']}";
        
        if (count($uploaded_files) > 0) {
            $notification_body .= " (+" . count($uploaded_files) . " archivo" . (count($uploaded_files) > 1 ? "s" : "") . ")";
        }
        
        debug_log("📧 ==> PREPARANDO NOTIFICACIÓN PARA TICKET #{$ticket_id} <==");
        debug_log("📧 Título: " . $notification_title);
        debug_log("📧 Cuerpo: " . $notification_body);
        
        // NO enviar notificación aquí - se enviará desde JavaScript
        debug_log("📧 ==> NOTIFICACIÓN SERÁ ENVIADA VIA JAVASCRIPT <==");
        
        send_json_response([
            'success' => true, 
            'ticket_id' => $ticket_id,
            'uploaded_files' => $uploaded_files,
            'files_count' => count($uploaded_files),
            // Datos para notificación JavaScript
            'notification' => [
                'title' => $notification_title,
                'body' => $notification_body,
                'data' => [
                    'ticket_id' => (string)$ticket_id,
                    'action' => 'new_ticket',
                    'attachments_count' => (string)count($uploaded_files)
                ]
            ]
        ]);
        
    } catch (Exception $e) {
        debug_log("❌ ERROR en create_ticket: " . $e->getMessage());
        send_json_response(['error' => $e->getMessage()], 500);
    }
}

function register_fcm_token($data) {
    try {
        debug_log("🔑 ==> REGISTRANDO TOKEN FCM <==");
        $token = $data['token'] ?? '';
        
        if ($token) {
            $saved = save_fcm_token($token);
            debug_log($saved ? "✅ Token guardado" : "❌ Error guardando token");
        }
        
        send_json_response(['success' => true, 'message' => 'Token registrado']);
        
    } catch (Exception $e) {
        debug_log("❌ Error: " . $e->getMessage());
        send_json_response(['error' => $e->getMessage()], 500);
    }
}

function get_users_it() {
    try {
        debug_log("👥 Obteniendo usuarios IT");
        $pdo = get_db_connection();
        if (!$pdo) {
            send_json_response(['error' => 'No se pudo conectar a la base de datos'], 500);
        }
        
        $stmt = $pdo->query("SELECT * FROM usuarios WHERE rol = 'it'");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        debug_log("✅ Usuarios IT encontrados: " . count($users));
        send_json_response($users);
        
    } catch (Exception $e) {
        debug_log("❌ Error en get_users_it: " . $e->getMessage());
        send_json_response(['error' => $e->getMessage()], 500);
    }
}

function assign_ticket($data) {
    try {
        debug_log("👤 Asignando ticket");
        $pdo = get_db_connection();
        if (!$pdo) {
            send_json_response(['error' => 'No se pudo conectar a la base de datos'], 500);
        }
        
        $query = "UPDATE tickets SET asignado_a = ?, estado = 'en_proceso', fecha_asignacion = NOW() WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['user_id'], $data['ticket_id']]);
        
        debug_log("✅ Ticket asignado exitosamente");
        send_json_response(['success' => true]);
        
    } catch (Exception $e) {
        debug_log("❌ Error en assign_ticket: " . $e->getMessage());
        send_json_response(['error' => $e->getMessage()], 500);
    }
}

function resolve_ticket($data) {
    try {
        debug_log("✅ Resolviendo ticket");
        $pdo = get_db_connection();
        if (!$pdo) {
            send_json_response(['error' => 'No se pudo conectar a la base de datos'], 500);
        }
        
        $query = "UPDATE tickets SET estado = 'resuelto', resolucion = ?, fecha_resolucion = NOW() WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['resolucion'], $data['ticket_id']]);
        
        debug_log("✅ Ticket resuelto exitosamente");
        send_json_response(['success' => true]);
        
    } catch (Exception $e) {
        debug_log("❌ Error en resolve_ticket: " . $e->getMessage());
        send_json_response(['error' => $e->getMessage()], 500);
    }
}

function close_ticket($data) {
    try {
        debug_log("🔒 Cerrando ticket");
        $pdo = get_db_connection();
        if (!$pdo) {
            send_json_response(['error' => 'No se pudo conectar a la base de datos'], 500);
        }
        
        $query = "UPDATE tickets SET estado = 'cerrado', comentarios_cierre = ?, fecha_cierre = NOW() WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$data['comentarios'] ?? '', $data['ticket_id']]);
        
        debug_log("✅ Ticket cerrado exitosamente");
        send_json_response(['success' => true]);
        
    } catch (Exception $e) {
        debug_log("❌ Error en close_ticket: " . $e->getMessage());
        send_json_response(['error' => $e->getMessage()], 500);
    }
}

function update_ticket_estado($data) {
    try {
        debug_log("🔄 Actualizando estado de ticket");
        debug_log("📥 Datos: " . json_encode($data));
        
        $pdo = get_db_connection();
        if (!$pdo) {
            send_json_response(['error' => 'No se pudo conectar a la base de datos'], 500);
        }
        
        $ticket_id = $data['ticket_id'];
        $nuevo_estado = $data['estado'];
        
        // Validar estados permitidos
        $estados_validos = ['abierto', 'asignado', 'en_proceso', 'resuelto', 'cerrado'];
        if (!in_array($nuevo_estado, $estados_validos)) {
            send_json_response(['error' => 'Estado no válido'], 400);
            return;
        }
        
        // Actualizar según el estado
        $query = "UPDATE tickets SET estado = ?";
        $params = [$nuevo_estado];
        
        // Agregar campos específicos según el estado
        switch ($nuevo_estado) {
            case 'asignado':
            case 'en_proceso':
                $query .= ", fecha_asignacion = NOW()";
                break;
            case 'resuelto':
                $query .= ", fecha_resolucion = NOW()";
                break;
            case 'cerrado':
                $query .= ", fecha_cierre = NOW()";
                break;
        }
        
        $query .= " WHERE id = ?";
        $params[] = $ticket_id;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        
        debug_log("✅ Estado actualizado a: " . $nuevo_estado);
        
        // Enviar notificación si es necesario
        if (in_array($nuevo_estado, ['asignado', 'resuelto', 'cerrado'])) {
            $notification_titles = [
                'asignado' => '👤 Ticket Asignado',
                'resuelto' => '✅ Ticket Resuelto', 
                'cerrado' => '🔒 Ticket Cerrado'
            ];
            
            send_fcm_notification(
                $notification_titles[$nuevo_estado],
                "Ticket #{$ticket_id} cambió a: " . strtoupper($nuevo_estado),
                [
                    'ticket_id' => (string)$ticket_id,
                    'action' => 'status_change',
                    'new_status' => $nuevo_estado
                ]
            );
        }
        
        send_json_response(['success' => true, 'estado' => $nuevo_estado]);
        
    } catch (Exception $e) {
        debug_log("❌ Error en update_ticket_estado: " . $e->getMessage());
        send_json_response(['error' => $e->getMessage()], 500);
    }
}

function get_stats() {
    try {
        debug_log("📊 Obteniendo estadísticas");
        $pdo = get_db_connection();
        if (!$pdo) {
            send_json_response(['error' => 'No se pudo conectar a la base de datos'], 500);
        }
        
        $stmt = $pdo->query("SELECT estado, COUNT(*) as total FROM tickets GROUP BY estado");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stats = [];
        foreach ($results as $row) {
            $stats[$row['estado']] = (int)$row['total'];
        }
        
        debug_log("✅ Estadísticas generadas: " . json_encode($stats));
        send_json_response($stats);
        
    } catch (Exception $e) {
        debug_log("❌ Error en get_stats: " . $e->getMessage());
        send_json_response(['error' => $e->getMessage()], 500);
    }
}

function validate_file($file) {
    global $upload_config;
    
    $errors = [];
    
    // Verificar si hay errores en la subida
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Error en la subida del archivo: " . $file['error'];
        return $errors;
    }
    
    // Verificar tamaño
    if ($file['size'] > $upload_config['max_file_size']) {
        $size_mb = round($upload_config['max_file_size'] / (1024 * 1024), 2);
        $errors[] = "El archivo excede el tamaño máximo permitido de {$size_mb}MB";
    }
    
    // Verificar extensión
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $upload_config['allowed_types'])) {
        $errors[] = "Tipo de archivo no permitido. Permitidos: " . implode(', ', $upload_config['allowed_types']);
    }
    
    // Verificar tipo MIME
    $allowed_mimes = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf', 'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain', 'video/mp4', 'video/avi', 'video/quicktime', 'video/webm'
    ];
    
    if (!in_array($file['type'], $allowed_mimes)) {
        $errors[] = "Tipo MIME no válido: " . $file['type'];
    }
    
    return $errors;
}

function upload_file() {
    global $upload_config;
    
    try {
        debug_log("📁 ==> SUBIENDO ARCHIVO <==");
        
        if (!isset($_FILES['file'])) {
            send_json_response(['error' => 'No se encontró archivo para subir'], 400);
        }
        
        $file = $_FILES['file'];
        $ticket_id = $_POST['ticket_id'] ?? null;
        
        if (!$ticket_id) {
            send_json_response(['error' => 'ticket_id es requerido'], 400);
        }
        
        // Validar archivo
        $validation_errors = validate_file($file);
        if (!empty($validation_errors)) {
            send_json_response(['error' => implode(', ', $validation_errors)], 400);
        }
        
        // Crear directorio si no existe
        $upload_dir = $upload_config['upload_path'] . date('Y/m/');
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Generar nombre único
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $unique_filename = 'ticket_' . $ticket_id . '_' . uniqid() . '.' . $file_extension;
        $file_path = $upload_dir . $unique_filename;
        
        // Mover archivo
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            // Guardar en BD
            $pdo = get_db_connection();
            if (!$pdo) {
                send_json_response(['error' => 'No se pudo conectar a la base de datos'], 500);
            }
            
            $stmt = $pdo->prepare("INSERT INTO ticket_attachments (ticket_id, filename, original_name, file_size, mime_type) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $ticket_id,
                $unique_filename,
                $file['name'],
                $file['size'],
                $file['type']
            ]);
            
            $attachment_id = $pdo->lastInsertId();
            
            debug_log("✅ Archivo subido exitosamente: " . $unique_filename);
            send_json_response([
                'success' => true,
                'attachment_id' => $attachment_id,
                'filename' => $unique_filename,
                'original_name' => $file['name'],
                'size' => $file['size']
            ]);
            
        } else {
            send_json_response(['error' => 'Error moviendo el archivo'], 500);
        }
        
    } catch (Exception $e) {
        debug_log("❌ Error en upload_file: " . $e->getMessage());
        send_json_response(['error' => $e->getMessage()], 500);
    }
}

function get_ticket_files($ticket_id) {
    try {
        debug_log("📄 Obteniendo archivos del ticket: " . $ticket_id);
        
        $pdo = get_db_connection();
        if (!$pdo) {
            send_json_response(['error' => 'No se pudo conectar a la base de datos'], 500);
        }
        
        $stmt = $pdo->prepare("SELECT id, filename, original_name, file_size, mime_type, upload_date FROM ticket_attachments WHERE ticket_id = ? ORDER BY upload_date DESC");
        $stmt->execute([$ticket_id]);
        $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Agregar información adicional
        foreach ($files as &$file) {
            $file['size_formatted'] = format_file_size($file['file_size']);
            $file['file_type'] = get_file_type($file['mime_type']);
        }
        
        debug_log("✅ Archivos encontrados: " . count($files));
        send_json_response($files);
        
    } catch (Exception $e) {
        debug_log("❌ Error en get_ticket_files: " . $e->getMessage());
        send_json_response(['error' => $e->getMessage()], 500);
    }
}

function format_file_size($bytes) {
    if ($bytes >= 1073741824) {
        return round($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

function get_file_type($mime_type) {
    if (strpos($mime_type, 'image/') === 0) return 'image';
    if (strpos($mime_type, 'video/') === 0) return 'video';
    if ($mime_type === 'application/pdf') return 'pdf';
    if (strpos($mime_type, 'application/vnd.ms-') === 0 || strpos($mime_type, 'application/vnd.openxmlformats-') === 0) return 'office';
    if ($mime_type === 'text/plain') return 'text';
    return 'other';
}

function debug_fcm_status() {
    global $project_id, $service_account_file;
    
    debug_log("🔍 ==> DIAGNÓSTICO FCM <==");
    
    $status = [
        'project_id' => $project_id,
        'service_account_exists' => file_exists($service_account_file),
        'service_account_path' => $service_account_file,
        'tokens_count' => count(get_fcm_tokens()),
        'php_extensions' => [
            'openssl' => extension_loaded('openssl'),
            'json' => extension_loaded('json'),
            'pdo' => extension_loaded('pdo'),
        ]
    ];
    
    // Verificar service account
    if ($status['service_account_exists']) {
        $service_content = file_get_contents($service_account_file);
        $service_data = json_decode($service_content, true);
        $status['service_account_valid'] = ($service_data !== null);
        $status['client_email'] = $service_data['client_email'] ?? 'N/A';
    }
    
    // Test access token
    $access_token = get_access_token_simple();
    $status['access_token_works'] = ($access_token !== null);
    
    debug_log("🔍 Status FCM: " . json_encode($status));
    send_json_response($status);
}

function test_notification($data) {
    debug_log("🧪 ==> TEST DE NOTIFICACIÓN <==");
    
    $title = $data['title'] ?? '🧪 Test Notification';
    $body = $data['body'] ?? 'Esta es una notificación de prueba';
    
    send_fcm_notification($title, $body, ['test' => 'true']);
    
    send_json_response(['success' => true, 'message' => 'Test notification sent']);
}

debug_log("🏁 Script completado");
?>