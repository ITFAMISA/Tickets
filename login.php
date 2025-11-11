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
    <title>Centro de Soporte IT - Acceso</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --color-red: #d32f2f;
            --color-red-strong: #b71c1c;
            --color-white: #ffffff;
            --color-surface: #f6f6f7;
            --color-text: #121212;
            --color-muted: rgba(0, 0, 0, 0.6);
            --color-line: rgba(211, 47, 47, 0.2);
            --shadow-soft: 0 28px 80px rgba(0, 0, 0, 0.12);
            --radius-md: 16px;
            --radius-lg: 22px;
            --transition: all 0.35s ease;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--color-surface);
            color: var(--color-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .app-header {
            position: sticky;
            top: 0;
            inset-inline: 0;
            background: var(--color-red);
            color: var(--color-white);
            padding: 1.25rem 5vw;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            z-index: 20;
        }

        .app-header__brand {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .brand-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.18);
            display: grid;
            place-items: center;
            font-size: 1.6rem;
        }

        .app-header__title {
            font-size: 1.4rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .app-header__subtitle {
            font-size: 0.85rem;
            opacity: 0.8;
        }

        .auth-wrapper {
            flex: 1;
            display: grid;
            place-items: center;
            padding: 4rem 1.5rem;
        }

        .auth-panel {
            width: min(420px, 100%);
            background: var(--color-white);
            border-radius: var(--radius-lg);
            border: 1px solid var(--color-line);
            box-shadow: var(--shadow-soft);
            padding: 3rem 2.75rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
            animation: fadeIn 0.6s ease forwards;
        }

        .auth-panel__heading {
            text-align: center;
            display: grid;
            gap: 0.75rem;
        }

        .auth-panel__heading h2 {
            font-size: 1.8rem;
            font-weight: 600;
        }

        .auth-panel__heading p {
            font-size: 0.95rem;
            color: var(--color-muted);
        }

        .form-grid {
            display: grid;
            gap: 1.25rem;
        }

        .form-field {
            display: grid;
            gap: 0.5rem;
        }

        .form-field label {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--color-text);
        }

        .input-control {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--color-white);
            border: 1.5px solid rgba(0, 0, 0, 0.08);
            border-radius: var(--radius-md);
            padding: 0.95rem 1.1rem;
            transition: var(--transition);
        }

        .input-control i {
            color: rgba(0, 0, 0, 0.45);
            font-size: 1rem;
        }

        .input-control:focus-within {
            border-color: var(--color-red);
            box-shadow: 0 0 0 4px rgba(211, 47, 47, 0.15);
        }

        .input-control input {
            width: 100%;
            border: none;
            outline: none;
            font-size: 0.95rem;
            color: var(--color-text);
            font-family: inherit;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            border-radius: var(--radius-md);
            border: none;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            padding: 0.95rem;
            transition: var(--transition);
        }

        .btn-primary {
            background: var(--color-red);
            color: var(--color-white);
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: var(--color-red-strong);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: var(--color-white);
            color: var(--color-red);
            border: 1.5px solid var(--color-red);
        }

        .btn-secondary:hover,
        .btn-secondary:focus {
            background: rgba(211, 47, 47, 0.08);
        }

        .alert-space {
            min-height: 52px;
        }

        .alert {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.85rem 1rem;
            border-radius: var(--radius-md);
            border: 1px solid transparent;
            font-size: 0.9rem;
            animation: slideUp 0.45s ease forwards;
        }

        .alert i {
            font-size: 1.1rem;
        }

        .alert--info {
            background: rgba(211, 47, 47, 0.12);
            border-color: rgba(211, 47, 47, 0.24);
            color: var(--color-red);
        }

        .alert--success {
            background: rgba(0, 0, 0, 0.06);
            border-color: rgba(0, 0, 0, 0.12);
            color: var(--color-text);
        }

        .alert--danger {
            background: rgba(211, 47, 47, 0.18);
            border-color: rgba(211, 47, 47, 0.3);
            color: var(--color-red);
        }

        .auth-footnote {
            text-align: center;
            display: grid;
            gap: 0.5rem;
        }

        .auth-footnote small {
            color: var(--color-muted);
            font-size: 0.85rem;
        }

        .demo-credentials {
            background: rgba(0, 0, 0, 0.04);
            border-radius: var(--radius-md);
            padding: 1rem;
            display: grid;
            gap: 0.35rem;
            border: 1px dashed rgba(0, 0, 0, 0.12);
        }

        footer {
            text-align: center;
            padding: 1.5rem 1rem 2.5rem;
            color: var(--color-muted);
            font-size: 0.85rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(18px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 640px) {
            .app-header {
                padding: 1rem 1.5rem;
            }

            .brand-icon {
                width: 46px;
                height: 46px;
                font-size: 1.3rem;
            }

            .auth-panel {
                padding: 2.5rem 1.75rem;
                gap: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <header class="app-header">
        <div class="app-header__brand">
            <div class="brand-icon">
                <i class="fas fa-headset"></i>
            </div>
            <div>
                <div class="app-header__title">Centro de Soporte IT</div>
                <div class="app-header__subtitle">Acceso administrativo</div>
            </div>
        </div>
        <span class="app-header__subtitle">Operamos en un entorno seguro y centralizado</span>
    </header>

    <main class="auth-wrapper">
        <section class="auth-panel">
            <div class="auth-panel__heading">
                <h2>Bienvenido de vuelta</h2>
                <p>Inicia sesión para administrar y dar seguimiento a los tickets internos.</p>
            </div>

            <div id="mensaje" class="alert-space"></div>

            <form id="loginForm" class="form-grid">
                <div class="form-field">
                    <label for="email">Correo electrónico</label>
                    <div class="input-control">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="admin@empresa.com" required>
                    </div>
                </div>

                <div class="form-field">
                    <label for="password">Contraseña</label>
                    <div class="input-control">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i>
                    Iniciar sesión
                </button>
            </form>

            <div class="auth-footnote">
                <small>Usuarios de prueba:</small>
                <div class="demo-credentials">
                    <span><strong>Administrador</strong>: admin@empresa.com</span>
                    <span><strong>Usuario</strong>: user@empresa.com</span>
                    <span><strong>Contraseña</strong>: password</span>
                </div>
            </div>
        </section>
    </main>

    <footer>
        Centro de Soporte IT &copy; <?php echo date('Y'); ?> - Plataforma interna de gestión
    </footer>

    <script>
        const mensajeDiv = document.getElementById('mensaje');
        const loginForm = document.getElementById('loginForm');

        function renderAlert(message, type, icon) {
            mensajeDiv.innerHTML = `
                <div class="alert alert--${type}">
                    <i class="fas ${icon}"></i>
                    <span>${message}</span>
                </div>
            `;
        }

        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(loginForm);
            renderAlert('Validando credenciales...', 'info', 'fa-spinner fa-spin');

            fetch('login.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderAlert('Acceso concedido. Redirigiendo...', 'success', 'fa-circle-check');
                        setTimeout(() => {
                            window.location.href = 'dashboard.php';
                        }, 900);
                    } else {
                        renderAlert(data.message || 'Credenciales incorrectas', 'danger', 'fa-triangle-exclamation');
                    }
                })
                .catch(() => {
                    renderAlert('No fue posible conectar con el servidor', 'danger', 'fa-triangle-exclamation');
                });
        });
    </script>
</body>
</html>
