<?php
// login.php
require_once 'config/database.php';
iniciarSesion();

if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT id, nombre, email, rol, password FROM usuarios WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($password, $row['password'])) {
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['rol'] = $row['rol'];
            
            // Actualizar último acceso
            $updateQuery = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(1, $row['id']);
            $updateStmt->execute();
            
            echo json_encode(['success' => true, 'rol' => $row['rol']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Tickets IT - Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --color-red: #d32f2f;
            --color-red-dark: #a31616;
            --color-red-light: #ff6f61;
            --color-black: #000000;
            --color-charcoal: #0d0f12;
            --color-graphite: #181a1f;
            --color-ink: #202228;
            --color-white: #ffffff;
            --color-gray-50: #f8f8f8;
            --color-gray-100: #ededed;
            --color-gray-300: #d6d6d6;
            --color-gray-500: #8b8b8b;
            --shadow-soft: 0 12px 32px rgba(0, 0, 0, 0.15);
            --shadow-medium: 0 18px 44px rgba(0, 0, 0, 0.18);
            --shadow-strong: 0 24px 60px rgba(211, 47, 47, 0.35);
            --radius-md: 16px;
            --radius-lg: 22px;
            --transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
            --primary-color: var(--color-red);
            --primary-dark: var(--color-red-dark);
            --primary-light: var(--color-red-light);
            --bg-light: var(--color-white);
            --text-dark: var(--color-ink);
            --text-light: var(--color-gray-500);
            --border-color: rgba(0, 0, 0, 0.08);
            --success-color: var(--color-red);
            --warning-color: #ff8f70;
            --danger-color: var(--color-red);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(155deg, var(--color-charcoal) 0%, var(--color-black) 55%, var(--color-gray-50) 140%);
            color: var(--color-ink);
            min-height: 100vh;
            line-height: 1.7;
            overflow-x: hidden;
            position: relative;
        }

        body::before,
        body::after {
            content: '';
            position: fixed;
            width: 620px;
            height: 620px;
            background: radial-gradient(circle, rgba(211, 47, 47, 0.22) 0%, transparent 70%);
            z-index: -1;
            pointer-events: none;
            animation: glow 12s ease-in-out infinite;
        }

        body::before {
            top: -220px;
            right: -200px;
        }

        body::after {
            bottom: -260px;
            left: -180px;
            animation-delay: 4s;
        }

        ::selection {
            background: var(--color-red);
            color: var(--color-white);
        }

        a {
            color: var(--color-red);
            transition: var(--transition);
            text-decoration: none;
        }

        a:hover {
            color: var(--color-red-dark);
        }

        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            color: var(--color-ink);
        }

        p, span, label {
            color: var(--color-gray-500);
        }

        .container {
            width: min(1180px, 92vw);
            margin: 0 auto;
        }

        .container-fluid {
            width: min(1280px, 96vw);
            margin: 0 auto;
        }

        .row {
            --bs-gutter-x: 2.5rem;
            --bs-gutter-y: 2.5rem;
        }

        .header-main {
            background: radial-gradient(circle at top left, rgba(211, 47, 47, 0.45), transparent 60%),
                        linear-gradient(135deg, var(--color-black) 0%, var(--color-graphite) 100%);
            color: var(--color-white);
            padding: 4rem 0;
            border-bottom: 4px solid var(--color-red);
            position: relative;
            overflow: hidden;
        }

        .header-main .badge {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .header-main h1 {
            font-size: clamp(2.6rem, 5vw, 3.4rem);
            letter-spacing: -1px;
            text-shadow: 0 14px 35px rgba(0, 0, 0, 0.35);
        }

        .header-main p {
            font-size: clamp(1rem, 2vw, 1.2rem);
            color: rgba(255, 255, 255, 0.72);
        }

        .main-card,
        .card,
        .card-dashboard {
            background: var(--color-white);
            border-radius: var(--radius-md);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: var(--shadow-soft);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .main-card::before,
        .card::before,
        .card-dashboard::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.25), transparent);
            opacity: 0;
            transition: var(--transition);
        }

        .main-card:hover,
        .card:hover,
        .card-dashboard:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-medium);
        }

        .main-card:hover::before,
        .card:hover::before,
        .card-dashboard:hover::before {
            opacity: 1;
        }

        .card-header,
        .card-dashboard .card-header,
        .stat-card {
            background: linear-gradient(135deg, var(--color-black) 0%, var(--color-graphite) 100%);
            color: var(--color-white);
            border: none;
            padding: 1.5rem 1.75rem;
            border-bottom: 3px solid var(--color-red);
        }

        .card-body {
            padding: 1.75rem;
        }

        .btn {
            border-radius: 999px;
            padding: 0.85rem 1.8rem;
            font-weight: 600;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
            border: none;
            transition: var(--transition);
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .btn:hover::after {
            width: 380px;
            height: 380px;
        }

        .btn-primary,
        .btn-danger,
        .btn-floating {
            background: linear-gradient(135deg, var(--color-red) 0%, var(--color-red-dark) 100%);
            color: var(--color-white);
            box-shadow: var(--shadow-strong);
        }

        .btn-primary:hover,
        .btn-danger:hover,
        .btn-floating:hover {
            background: linear-gradient(135deg, var(--color-red-dark) 0%, var(--color-black) 100%);
            transform: translateY(-4px);
        }

        .btn-outline-primary {
            border: 2px solid var(--color-red);
            color: var(--color-red);
            background: transparent;
        }

        .btn-outline-primary:hover,
        .btn-outline-primary.active {
            background: var(--color-red);
            color: var(--color-white);
            box-shadow: var(--shadow-strong);
        }

        .btn-secondary {
            background: var(--color-gray-50);
            color: var(--color-ink);
            border: 2px solid var(--color-gray-100);
        }

        .btn-secondary:hover {
            border-color: var(--color-red);
            color: var(--color-red);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            border-radius: 999px;
            padding: 0.45rem 0.9rem;
            font-size: 0.75rem;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        .bg-danger { background: var(--color-red) !important; color: var(--color-white) !important; }
        .bg-warning { background: linear-gradient(135deg, #ff8f70, #ff6f61) !important; color: var(--color-white) !important; }
        .bg-success { background: linear-gradient(135deg, var(--color-red), var(--color-red-light)) !important; color: var(--color-white) !important; }
        .bg-secondary { background: var(--color-gray-50) !important; color: var(--color-ink) !important; }
        .bg-primary { background: var(--color-red) !important; color: var(--color-white) !important; }
        .bg-light { background: rgba(255, 255, 255, 0.85) !important; color: var(--color-ink) !important; }

        .text-muted { color: var(--color-gray-500) !important; }
        .text-white-80 { color: rgba(255, 255, 255, 0.8); }

        .form-control,
        .form-select {
            border-radius: var(--radius-md);
            border: 2px solid var(--color-gray-100);
            padding: 0.9rem 1rem;
            font-size: 0.95rem;
            transition: var(--transition);
            color: var(--color-ink);
            background-color: var(--color-white);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--color-red);
            box-shadow: 0 0 0 4px rgba(211, 47, 47, 0.12);
            outline: none;
        }

        .input-group-text {
            border-radius: var(--radius-md) 0 0 var(--radius-md);
            background: var(--color-gray-50);
            border: 2px solid var(--color-gray-100);
            border-right: none;
            color: var(--color-gray-500);
            transition: var(--transition);
        }

        .input-group:focus-within .input-group-text {
            background: var(--color-red);
            border-color: var(--color-red);
            color: var(--color-white);
        }

        .tab-button {
            background: var(--color-gray-50) !important;
            border: 2px solid rgba(0, 0, 0, 0.05) !important;
            border-radius: var(--radius-md);
            color: var(--color-ink) !important;
        }

        .tab-button.active {
            background: var(--color-red) !important;
            color: var(--color-white) !important;
            border-color: var(--color-red) !important;
        }

        .tab-button:not(.active):hover {
            border-color: var(--color-red) !important;
            color: var(--color-red) !important;
        }

        .ticket-card,
        .timeline-item,
        .table-hover tbody tr {
            transition: var(--transition);
        }

        .ticket-card {
            border: 2px solid rgba(0, 0, 0, 0.04);
            border-left: 5px solid transparent;
            border-radius: var(--radius-md);
            padding: 1.5rem;
            background: var(--color-white);
        }

        .ticket-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-medium);
            border-color: rgba(211, 47, 47, 0.35);
        }

        .ticket-priority-urgente { border-left-color: var(--color-red); }
        .ticket-priority-alta { border-left-color: #ff8f70; }
        .ticket-priority-media { border-left-color: var(--color-black); }
        .ticket-priority-baja { border-left-color: var(--color-gray-300); }

        .personal-card {
            text-align: center;
            padding: 2rem;
            border: 2px solid rgba(0, 0, 0, 0.05);
            border-radius: var(--radius-md);
            background: var(--color-white);
        }

        .personal-card .fa-user-circle {
            color: var(--color-red);
            font-size: 2.5rem;
            margin-bottom: 1rem;
            filter: drop-shadow(0 8px 24px rgba(211, 47, 47, 0.35));
        }

        .alert {
            border-radius: var(--radius-md);
            border: none;
            padding: 1.5rem;
            border-left: 5px solid var(--color-red);
            background: linear-gradient(135deg, rgba(211, 47, 47, 0.08), rgba(211, 47, 47, 0.15));
            color: var(--color-ink);
        }

        .table {
            border-spacing: 0 12px;
        }

        .table thead th {
            text-transform: uppercase;
            letter-spacing: 0.6px;
            font-size: 0.75rem;
            color: var(--color-gray-500);
            border: none;
            background: rgba(0, 0, 0, 0.03);
            padding: 1rem;
        }

        .table tbody tr {
            background: var(--color-white);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-soft);
        }

        .table tbody tr:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-medium);
        }

        .table tbody td {
            border: none;
            padding: 1.1rem 1rem;
            vertical-align: middle;
            color: var(--color-ink);
        }

        .navbar-custom,
        .navbar {
            background: linear-gradient(135deg, var(--color-black) 0%, var(--color-graphite) 100%);
            border-bottom: 4px solid var(--color-red);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        }

        .navbar-brand,
        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            transition: var(--transition);
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: var(--color-white) !important;
        }

        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.35);
        }

        .navbar-toggler-icon {
            filter: brightness(10);
        }

        .btn-floating {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            position: fixed;
            bottom: 32px;
            right: 32px;
            z-index: 1020;
        }

        .section { display: none; }
        .section.active { display: block; animation: fadeUp 0.6s ease forwards; }

        .modal.fade .modal-dialog {
            transform: translateY(30px);
            transition: var(--transition);
        }

        .modal.show .modal-dialog {
            transform: translateY(0);
        }

        .modal-content {
            border-radius: var(--radius-lg);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: var(--shadow-medium);
        }

        .modal-header {
            border-bottom: 3px solid var(--color-red);
            background: linear-gradient(135deg, var(--color-black), var(--color-graphite));
            color: var(--color-white);
        }

        .modal-footer {
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            background: var(--color-gray-50);
        }

        .btn-close {
            filter: brightness(10);
        }

        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0.45rem;
            top: 0.5rem;
            bottom: 0.5rem;
            width: 2px;
            background: linear-gradient(180deg, rgba(211, 47, 47, 0.45), transparent);
        }

        .timeline-marker {
            width: 12px;
            height: 12px;
            background: var(--color-red);
            border-radius: 50%;
            box-shadow: 0 0 0 6px rgba(211, 47, 47, 0.1);
            position: absolute;
            left: -0.35rem;
            top: 0.6rem;
        }

        .status-badge {
            padding: 0.35rem 0.65rem;
            border-radius: 999px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .user-status {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
        }

        .status-disponible { background: #22c55e; }
        .status-ocupado { background: #f59e0b; }
        .status-ausente { background: #ef4444; }

        .ticket-estado-abierto { background-color: rgba(21, 10, 255, 0.08); }
        .ticket-estado-en_proceso { background-color: rgba(255, 193, 7, 0.12); }
        .ticket-estado-resuelto { background-color: rgba(34, 197, 94, 0.12); }
        .ticket-estado-cerrado.satisfactoria { background-color: rgba(34, 197, 94, 0.18); }
        .ticket-estado-cerrado.insatisfactoria { background-color: rgba(239, 68, 68, 0.12); }

        .auth-card {
            background: rgba(255, 255, 255, 0.94);
            border-radius: var(--radius-lg);
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(14px);
            position: relative;
            overflow: hidden;
        }

        .auth-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(211, 47, 47, 0.15), transparent);
            opacity: 0;
            transition: var(--transition);
        }

        .auth-card:hover::before {
            opacity: 1;
        }

        .hero-icon {
            font-size: 3.6rem;
            color: rgba(255, 255, 255, 0.18);
        }

        .fade-in {
            animation: fadeUp 0.5s ease forwards;
        }

        .slide-in-up {
            animation: fadeUp 0.6s ease forwards;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes glow {
            0%, 100% {
                transform: scale(0.95);
                opacity: 0.6;
            }
            50% {
                transform: scale(1.1);
                opacity: 1;
            }
        }

        @media (max-width: 992px) {
            .header-main {
                text-align: center;
                padding: 3rem 0;
            }

            .hero-icon {
                display: none;
            }

            .btn {
                width: 100%;
            }

            .navbar-collapse {
                background: rgba(0, 0, 0, 0.85);
                border-radius: var(--radius-md);
                padding: 1.5rem;
            }

            .btn-floating {
                right: 20px;
                bottom: 20px;
            }
        }

        @media (max-width: 768px) {
            .container,
            .container-fluid {
                width: min(95vw, 100%);
            }

            .header-main {
                padding: 2.5rem 0;
            }

            .card,
            .main-card,
            .card-dashboard {
                padding: 0 1rem;
            }

            .card-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                            <h2 class="fw-bold text-dark">Sistema IT</h2>
                            <p class="text-muted">Gestión de Tickets</p>
                        </div>

                        <div id="mensaje"></div>

                        <form id="loginForm">
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-login">
                                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <strong>Usuarios de prueba:</strong><br>
                                Admin: admin@empresa.com<br>
                                Usuario: user@empresa.com<br>
                                Contraseña: password
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const mensajeDiv = document.getElementById('mensaje');
            
            mensajeDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Iniciando sesión...</div>';
            
            fetch('login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mensajeDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check"></i> Login exitoso. Redirigiendo...</div>';
                    setTimeout(() => window.location.href = 'dashboard.php', 1000);
                } else {
                    mensajeDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ${data.message}</div>`;
                }
            })
            .catch(error => {
                mensajeDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error de conexión</div>';
            });
        });
    </script>
</body>
</html>