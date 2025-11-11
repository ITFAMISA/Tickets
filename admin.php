<?php
// admin.php - Panel de administración para personal IT
require_once 'config/database.php';
iniciarSesion();

// Verificar login de administrador
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admin_login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT id, nombre, email, rol, password FROM usuarios WHERE email = ? AND rol = 'it'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_nombre'] = $row['nombre'];
            $_SESSION['admin_email'] = $row['email'];
            
            // Actualizar último acceso
            $updateQuery = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(1, $row['id']);
            $updateStmt->execute();
            
            header("Location: admin.php");
            exit();
        } else {
            $error_message = 'Credenciales incorrectas';
        }
    } else {
        $error_message = 'Usuario no encontrado o no tiene permisos de administrador';
    }
}

// Verificar si ya está logueado como admin
$isLoggedIn = isset($_SESSION['admin_id']);

// Logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_nombre']);
    unset($_SESSION['admin_email']);
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Sistema IT</title>
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
    <?php if (!$isLoggedIn): ?>
    <!-- Login Form -->
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="row justify-content-center w-100">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                            <h2 class="fw-bold text-dark">Administración IT</h2>
                            <p class="text-muted">Panel de Control</p>
                        </div>

                        <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> <?= $error_message ?>
                        </div>
                        <?php endif; ?>

                        <form method="POST">
                            <input type="hidden" name="admin_login" value="1">
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" name="password" placeholder="Contraseña" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Acceder
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Sistema
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Admin Panel -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-shield-alt me-2"></i>
                <strong>Panel IT</strong>
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        <span><?= htmlspecialchars($_SESSION['admin_nombre']) ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?logout=1"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Estadísticas -->
        <div class="row mb-4" id="statsCards">
            <!-- Se llenan dinámicamente -->
        </div>
        
        <!-- Navegación por pestañas -->
        <ul class="nav nav-tabs mb-4" id="mainTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" type="button" role="tab">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="todos-tickets-tab" data-bs-toggle="tab" data-bs-target="#todos-tickets" type="button" role="tab">
                    <i class="fas fa-ticket-alt me-2"></i>Todos los Tickets
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="reportes-tab" data-bs-toggle="tab" data-bs-target="#reportes" type="button" role="tab">
                    <i class="fas fa-chart-bar me-2"></i>Reportes
                </button>
            </li>
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="mainTabsContent">
            <!-- Dashboard -->
            <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-dashboard">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-ticket-alt me-2"></i>Tickets Recientes</h5>
                                <div class="d-flex gap-2">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-outline-primary btn-sm active" onclick="filtrarTickets('todos')">Todos</button>
                                        <button class="btn btn-outline-primary btn-sm" onclick="filtrarTickets('abierto')">Abiertos</button>
                                        <button class="btn btn-outline-primary btn-sm" onclick="filtrarTickets('en_proceso')">En Proceso</button>
                                        <button class="btn btn-outline-primary btn-sm" onclick="filtrarTickets('resuelto')">Resueltos</button>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-outline-secondary btn-sm active" onclick="filtrarTicketsPorMes('todos')" id="btnFiltroMesTodos">
                                            <i class="fas fa-list me-1"></i>Todos
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm" onclick="filtrarTicketsPorMes('mes_actual')" id="btnFiltroMesActual">
                                            <i class="fas fa-calendar-day me-1"></i>Este Mes
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Título</th>
                                                <th>Prioridad</th>
                                                <th>Estado</th>
                                                <th>Solicitante</th>
                                                <th>Ficha</th>
                                                <th>Asignado</th>
                                                <th>Fecha</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ticketsTable">
                                            <!-- Se llena dinámicamente -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Todos los Tickets -->
            <div class="tab-pane fade" id="todos-tickets" role="tabpanel">
                <div class="card card-dashboard">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Gestión de Tickets</h5>
                        <div class="d-flex gap-2">
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-primary active" onclick="filtrarTodosTickets('todos')">Todos</button>
                                <button class="btn btn-outline-primary" onclick="filtrarTodosTickets('abierto')">Abiertos</button>
                                <button class="btn btn-outline-primary" onclick="filtrarTodosTickets('en_proceso')">En Proceso</button>
                                <button class="btn btn-outline-primary" onclick="filtrarTodosTickets('resuelto')">Resueltos</button>
                            </div>
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-secondary active" onclick="filtrarTodosTicketsPorMes('todos')" id="btnFiltroMesTodosTodos">
                                    <i class="fas fa-list me-1"></i>Todos
                                </button>
                                <button class="btn btn-outline-secondary" onclick="filtrarTodosTicketsPorMes('mes_actual')" id="btnFiltroMesTodosMesActual">
                                    <i class="fas fa-calendar-day me-1"></i>Este Mes
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Título</th>
                                        <th>Prioridad</th>
                                        <th>Estado</th>
                                        <th>Solicitante</th>
                                        <th>Ficha</th>
                                        <th>Asignado</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="todosTicketsTable">
                                    <!-- Se llena dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reportes -->
            <div class="tab-pane fade" id="reportes" role="tabpanel">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card card-dashboard">
                            <div class="card-body text-center">
                                <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
                                <h5>Reporte General</h5>
                                <p class="text-muted">Exportar todos los tickets a Excel</p>
                                <button class="btn btn-success" onclick="generarReporteExcel('general')">
                                    <i class="fas fa-download me-2"></i>Descargar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card card-dashboard">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-pie fa-3x text-info mb-3"></i>
                                <h5>Por Estado</h5>
                                <p class="text-muted">Reporte filtrado por estado</p>
                                <select class="form-select mb-3" id="filtroEstado">
                                    <option value="todos">Todos los estados</option>
                                    <option value="abierto">Abiertos</option>
                                    <option value="en_proceso">En Proceso</option>
                                    <option value="resuelto">Resueltos</option>
                                    <option value="cerrado">Cerrados</option>
                                </select>
                                <button class="btn btn-info" onclick="generarReporteExcel('estado')">
                                    <i class="fas fa-download me-2"></i>Descargar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card card-dashboard">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar fa-3x text-warning mb-3"></i>
                                <h5>Por Fecha</h5>
                                <p class="text-muted">Reporte por rango de fechas</p>
                                <input type="date" class="form-control mb-2" id="fechaInicio">
                                <input type="date" class="form-control mb-3" id="fechaFin">
                                <button class="btn btn-warning" onclick="generarReporteExcel('fecha')">
                                    <i class="fas fa-download me-2"></i>Descargar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ver Ticket -->
    <div class="modal fade" id="modalVerTicket" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title" id="modalVerTicketTitle"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalVerTicketBody">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer" id="modalVerTicketFooter">
                    <!-- Botones dinámicos -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Resolver Ticket -->
    <div class="modal fade" id="modalResolverTicket" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title"><i class="fas fa-check me-2"></i>Resolver Ticket</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Descripción de la resolución</label>
                        <textarea class="form-control" id="resolucionTexto" rows="4" placeholder="Describe cómo se resolvió el problema..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="confirmarResolucion()">
                        <i class="fas fa-check me-2"></i>Marcar como Resuelto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        let usuarioActual = {
            id: <?= $_SESSION['admin_id'] ?>,
            nombre: '<?= addslashes($_SESSION['admin_nombre']) ?>',
            email: '<?= addslashes($_SESSION['admin_email']) ?>',
            rol: 'it'
        };

        let ticketSeleccionado = null;
        let ticketsDashboard = []; // Almacenar tickets del dashboard
        let ticketsCompletos = []; // Almacenar todos los tickets

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            cargarDashboard();
            configurarFechas();
        });

        function cargarDashboard() {
            cargarEstadisticas();
            cargarTickets();
        }

        async function cargarEstadisticas() {
            try {
                const response = await fetch('api/tickets_admin.php?action=estadisticas');
                const data = await response.json();
                
                if (data.success) {
                    mostrarEstadisticas(data.data);
                }
            } catch (error) {
                console.error('Error al cargar estadísticas:', error);
            }
        }

        function mostrarEstadisticas(stats) {
            const container = document.getElementById('statsCards');
            const statCards = [
                { titulo: 'Total Tickets', valor: stats.total, icono: 'fas fa-ticket-alt', color: 'primary' },
                { titulo: 'Abiertos', valor: stats.abiertos, icono: 'fas fa-folder-open', color: 'danger' },
                { titulo: 'En Proceso', valor: stats.en_proceso, icono: 'fas fa-cog', color: 'warning' },
                { titulo: 'Resueltos', valor: stats.resueltos, icono: 'fas fa-check-circle', color: 'success' },
                { titulo: 'No Resueltos', valor: stats.no_resueltos, icono: 'fas fa-times-circle', color: 'danger' },
                { titulo: 'Pendientes', valor: stats.pendientes, icono: 'fas fa-clock', color: 'info' }
            ];
            
            container.innerHTML = statCards.map(stat => `
                <div class="col-md-2 mb-4">
                    <div class="card stat-card">
                        <div class="card-body text-center">
                            <i class="${stat.icono} fa-2x mb-3"></i>
                            <h3 class="fw-bold">${stat.valor}</h3>
                            <p class="mb-0">${stat.titulo}</p>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        async function cargarTickets() {
            try {
                const response = await fetch('api/tickets_admin.php');
                const data = await response.json();
                
                if (data.success) {
                    mostrarTickets(data.data);
                }
            } catch (error) {
                console.error('Error al cargar tickets:', error);
            }
        }

        function mostrarTickets(tickets) {
            ticketsDashboard = tickets; // Guardar tickets para filtrado posterior
            const tableBody = document.getElementById('ticketsTable');

            tableBody.innerHTML = tickets.map(ticket => `
                <tr class="ticket-priority-${ticket.prioridad}" data-estado="${ticket.estado}" data-fecha="${ticket.fecha_creacion}">
                    <td>${ticket.titulo}</td>
                    <td><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)} status-badge">${ticket.prioridad.toUpperCase()}</span></td>
                    <td><span class="badge bg-${obtenerColorEstado(ticket.estado)} status-badge">${ticket.estado.replace('_', ' ').toUpperCase()}</span></td>
                    <td>${ticket.solicitante_nombre}</td>
                    <td>${ticket.numero_ficha}</td>
                    <td>${ticket.asignado_nombre || '<span class="text-muted">Sin asignar</span>'}</td>
                    <td>${formatearFecha(ticket.fecha_creacion)}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="verTicket(${ticket.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${ticket.estado === 'abierto' ?
                            `<button class="btn btn-sm btn-outline-success ms-1" onclick="tomarTicket(${ticket.id})">
                                <i class="fas fa-hand-paper"></i>
                            </button>` : ''}
                    </td>
                </tr>
            `).join('');
        }

        // Event listener para cambio de pestaña
        document.getElementById('todos-tickets-tab').addEventListener('shown.bs.tab', function() {
            cargarTodosTickets();
        });

        async function cargarTodosTickets() {
            try {
                const response = await fetch('api/tickets_admin.php?action=todos');
                const data = await response.json();
                
                if (data.success) {
                    mostrarTodosTickets(data.data);
                }
            } catch (error) {
                console.error('Error al cargar todos los tickets:', error);
            }
        }

        function mostrarTodosTickets(tickets) {
            ticketsCompletos = tickets; // Guardar tickets para filtrado posterior
            const tableBody = document.getElementById('todosTicketsTable');

            tableBody.innerHTML = tickets.map(ticket => `
                <tr class="ticket-priority-${ticket.prioridad}" data-estado="${ticket.estado}" data-fecha="${ticket.fecha_creacion}">
                    <td><strong>#${ticket.id}</strong></td>
                    <td>${ticket.titulo}</td>
                    <td><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)} status-badge">${ticket.prioridad.toUpperCase()}</span></td>
                    <td><span class="badge bg-${obtenerColorEstado(ticket.estado)} status-badge">${ticket.estado.replace('_', ' ').toUpperCase()}</span></td>
                    <td>${ticket.solicitante_nombre}</td>
                    <td>${ticket.numero_ficha}</td>
                    <td>${ticket.asignado_nombre || '<span class="text-muted">Sin asignar</span>'}</td>
                    <td>${formatearFecha(ticket.fecha_creacion)}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="verTicket(${ticket.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${ticket.estado === 'abierto' ?
                            `<button class="btn btn-sm btn-outline-success ms-1" onclick="tomarTicket(${ticket.id})">
                                <i class="fas fa-hand-paper"></i>
                            </button>` : ''}
                    </td>
                </tr>
            `).join('');
        }

        async function verTicket(ticketId) {
            try {
                const response = await fetch(`api/tickets_admin.php?action=detalle&id=${ticketId}`);
                const data = await response.json();
                
                if (data.success) {
                    // Obtener archivos adjuntos
                    try {
                        const archivosResponse = await fetch(`api/tickets_publico.php?action=detalle&id=${ticketId}`);
                        const archivosData = await archivosResponse.json();
                        
                        if (archivosData.success && archivosData.data.archivos) {
                            data.data.archivos = archivosData.data.archivos;
                        }
                    } catch (error) {
                        console.log('No se pudieron cargar los archivos adjuntos:', error);
                        data.data.archivos = [];
                    }
                    
                    mostrarDetalleTicket(data.data);
                } else {
                    mostrarMensaje(data.message || 'Error al cargar el ticket', 'danger');
                }
            } catch (error) {
                mostrarMensaje('Error de conexión', 'danger');
            }
        }

        function mostrarDetalleTicket(ticket) {
            const modal = document.getElementById('modalVerTicket');
            const title = document.getElementById('modalVerTicketTitle');
            const body = document.getElementById('modalVerTicketBody');
            const footer = document.getElementById('modalVerTicketFooter');
            
            title.innerHTML = `<i class="fas fa-ticket-alt me-2"></i>Ticket #${ticket.id} - ${ticket.titulo}`;
            
            // Preparar archivos adjuntos si existen
            let archivosHtml = '';
            if (ticket.archivos && ticket.archivos.length > 0) {
                archivosHtml = `
                    <div class="col-12 mt-3">
                        <h6><i class="fas fa-paperclip me-2"></i>Archivos Adjuntos</h6>
                        <div class="row">
                            ${ticket.archivos.map((archivo, index) => {
                                const esImagen = ['jpg', 'jpeg', 'png', 'gif'].includes(archivo.tipo_archivo.toLowerCase());
                                
                                if (esImagen) {
                                    return `
                                        <div class="col-md-3 mb-3">
                                            <div class="card">
                                                <img src="uploads/${archivo.ruta_archivo}" 
                                                     class="card-img-top" 
                                                     style="height: 150px; object-fit: cover; cursor: pointer;"
                                                     onclick="verImagenCompleta('uploads/${archivo.ruta_archivo}', '${archivo.nombre_archivo}')"
                                                     title="Clic para ver tamaño completo">
                                                <div class="card-body p-2">
                                                    <small class="text-muted">${archivo.nombre_archivo}</small>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                } else {
                                    return `
                                        <div class="col-md-3 mb-3">
                                            <div class="card text-center">
                                                <div class="card-body">
                                                    <i class="fas fa-file-alt fa-3x text-primary mb-2"></i>
                                                    <h6 class="card-title">${archivo.nombre_archivo}</h6>
                                                    <a href="uploads/${archivo.ruta_archivo}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download me-1"></i>Descargar
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                }
                            }).join('')}
                        </div>
                    </div>
                `;
            }
            
            body.innerHTML = `
                <div class="row">
                    <div class="col-md-8">
                        <h6><i class="fas fa-info-circle me-2"></i>Detalles del Ticket</h6>
                        <table class="table table-borderless">
                            <tr><td><strong>Título:</strong></td><td>${ticket.titulo}</td></tr>
                            <tr><td><strong>Descripción:</strong></td><td>${ticket.descripcion}</td></tr>
                            <tr><td><strong>Categoría:</strong></td><td>${ticket.categoria}</td></tr>
                            <tr><td><strong>Prioridad:</strong></td><td><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)}">${ticket.prioridad.toUpperCase()}</span></td></tr>
                            <tr><td><strong>Estado:</strong></td><td><span class="badge bg-${obtenerColorEstado(ticket.estado)}">${ticket.estado.replace('_', ' ').toUpperCase()}</span></td></tr>
                            <tr><td><strong>Solicitante:</strong></td><td>${ticket.solicitante_nombre}</td></tr>
                            <tr><td><strong>Número de Ficha:</strong></td><td>${ticket.numero_ficha}</td></tr>
                            <tr><td><strong>Asignado a:</strong></td><td>${ticket.asignado_nombre || '<span class="text-muted">Sin asignar</span>'}</td></tr>
                            <tr><td><strong>Fecha creación:</strong></td><td>${formatearFecha(ticket.fecha_creacion)}</td></tr>
                            ${ticket.fecha_resolucion ? `<tr><td><strong>Fecha resolución:</strong></td><td>${formatearFecha(ticket.fecha_resolucion)}</td></tr>` : ''}
                            ${ticket.resolucion ? `<tr><td><strong>Resolución:</strong></td><td>${ticket.resolucion}</td></tr>` : ''}
                        </table>
                    </div>
                    <div class="col-md-4">
                        <h6><i class="fas fa-clock me-2"></i>Historial</h6>
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Ticket Creado</h6>
                                    <p class="timeline-text">${formatearFecha(ticket.fecha_creacion)}</p>
                                </div>
                            </div>
                            ${ticket.asignado_nombre ? `
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-warning"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Asignado</h6>
                                        <p class="timeline-text">Tomado por: ${ticket.asignado_nombre}</p>
                                    </div>
                                </div>
                            ` : ''}
                            ${ticket.estado === 'resuelto' || ticket.estado === 'cerrado' ? `
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Resuelto</h6>
                                        <p class="timeline-text">${formatearFecha(ticket.fecha_resolucion)}</p>
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                    ${archivosHtml}
                </div>
            `;
            
            // Configurar botones del footer según el estado
            let footerButtons = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>';
            
            if (ticket.estado === 'abierto') {
                footerButtons = `
                    <button type="button" class="btn btn-success" onclick="tomarTicket(${ticket.id})">
                        <i class="fas fa-hand-paper me-2"></i>Tomar Ticket
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                `;
            } else if (ticket.estado === 'en_proceso' && ticket.asignado_a == usuarioActual.id) {
                footerButtons = `
                    <button type="button" class="btn btn-success" onclick="mostrarModalResolver(${ticket.id})">
                        <i class="fas fa-check me-2"></i>Marcar como Resuelto
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                `;
            }
            
            footer.innerHTML = footerButtons;
            new bootstrap.Modal(modal).show();
        }

        // Función para ver imagen en tamaño completo
        function verImagenCompleta(rutaImagen, nombreArchivo) {
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.innerHTML = `
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-image me-2"></i>${nombreArchivo}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="${rutaImagen}" class="img-fluid" style="max-height: 70vh;">
                        </div>
                        <div class="modal-footer">
                            <a href="${rutaImagen}" download="${nombreArchivo}" class="btn btn-primary">
                                <i class="fas fa-download me-2"></i>Descargar
                            </a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
            
            // Remover modal del DOM cuando se cierre
            modal.addEventListener('hidden.bs.modal', function() {
                document.body.removeChild(modal);
            });
        }

        async function tomarTicket(ticketId) {
            try {
                const response = await fetch(`api/tickets_admin.php?action=tomar&id=${ticketId}`, {
                    method: 'POST'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    cargarDashboard();
                    bootstrap.Modal.getInstance(document.getElementById('modalVerTicket'))?.hide();
                    mostrarMensaje('Ticket tomado exitosamente', 'success');
                } else {
                    mostrarMensaje(data.message || 'Error al tomar el ticket', 'danger');
                }
            } catch (error) {
                mostrarMensaje('Error de conexión', 'danger');
            }
        }

        function mostrarModalResolver(ticketId) {
            ticketSeleccionado = ticketId;
            bootstrap.Modal.getInstance(document.getElementById('modalVerTicket')).hide();
            new bootstrap.Modal(document.getElementById('modalResolverTicket')).show();
        }

        async function confirmarResolucion() {
            if (!ticketSeleccionado) return;
            
            const resolucion = document.getElementById('resolucionTexto').value;
            
            try {
                const response = await fetch(`api/tickets_admin.php?action=resolver&id=${ticketSeleccionado}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        resolucion: resolucion
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalResolverTicket')).hide();
                    document.getElementById('resolucionTexto').value = '';
                    cargarDashboard();
                    mostrarMensaje('Ticket marcado como resuelto', 'success');
                } else {
                    mostrarMensaje(data.message || 'Error al resolver el ticket', 'danger');
                }
            } catch (error) {
                mostrarMensaje('Error de conexión', 'danger');
            }
        }

        function filtrarTickets(estado) {
            event.target.parentNode.querySelectorAll('.btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            const rows = document.querySelectorAll('#ticketsTable tr');
            rows.forEach(row => {
                if (estado === 'todos') {
                    row.style.display = '';
                } else {
                    const estadoTicket = row.getAttribute('data-estado');
                    row.style.display = estadoTicket === estado ? '' : 'none';
                }
            });
        }

        function filtrarTodosTickets(estado) {
            event.target.parentNode.querySelectorAll('.btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            const rows = document.querySelectorAll('#todosTicketsTable tr');
            rows.forEach(row => {
                if (estado === 'todos') {
                    row.style.display = '';
                } else {
                    const estadoTicket = row.getAttribute('data-estado');
                    row.style.display = estadoTicket === estado ? '' : 'none';
                }
            });
        }

        function filtrarTicketsPorMes(filtro) {
            // Actualizar botones activos
            document.getElementById('btnFiltroMesTodos').classList.remove('active');
            document.getElementById('btnFiltroMesActual').classList.remove('active');

            if (filtro === 'todos') {
                document.getElementById('btnFiltroMesTodos').classList.add('active');
            } else {
                document.getElementById('btnFiltroMesActual').classList.add('active');
            }

            const ahora = new Date();
            const mesActual = ahora.getMonth();
            const añoActual = ahora.getFullYear();

            let ticketsFiltrados = ticketsDashboard;

            if (filtro === 'mes_actual') {
                ticketsFiltrados = ticketsDashboard.filter(ticket => {
                    const fechaTicket = new Date(ticket.fecha_creacion);
                    return fechaTicket.getMonth() === mesActual && fechaTicket.getFullYear() === añoActual;
                });
            }

            // Mostrar tickets filtrados
            const tableBody = document.getElementById('ticketsTable');
            tableBody.innerHTML = ticketsFiltrados.map(ticket => `
                <tr class="ticket-priority-${ticket.prioridad}" data-estado="${ticket.estado}" data-fecha="${ticket.fecha_creacion}">
                    <td>${ticket.titulo}</td>
                    <td><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)} status-badge">${ticket.prioridad.toUpperCase()}</span></td>
                    <td><span class="badge bg-${obtenerColorEstado(ticket.estado)} status-badge">${ticket.estado.replace('_', ' ').toUpperCase()}</span></td>
                    <td>${ticket.solicitante_nombre}</td>
                    <td>${ticket.numero_ficha}</td>
                    <td>${ticket.asignado_nombre || '<span class="text-muted">Sin asignar</span>'}</td>
                    <td>${formatearFecha(ticket.fecha_creacion)}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="verTicket(${ticket.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${ticket.estado === 'abierto' ?
                            `<button class="btn btn-sm btn-outline-success ms-1" onclick="tomarTicket(${ticket.id})">
                                <i class="fas fa-hand-paper"></i>
                            </button>` : ''}
                    </td>
                </tr>
            `).join('');
        }

        function filtrarTodosTicketsPorMes(filtro) {
            // Actualizar botones activos
            document.getElementById('btnFiltroMesTodosTodos').classList.remove('active');
            document.getElementById('btnFiltroMesTodosMesActual').classList.remove('active');

            if (filtro === 'todos') {
                document.getElementById('btnFiltroMesTodosTodos').classList.add('active');
            } else {
                document.getElementById('btnFiltroMesTodosMesActual').classList.add('active');
            }

            const ahora = new Date();
            const mesActual = ahora.getMonth();
            const añoActual = ahora.getFullYear();

            let ticketsFiltrados = ticketsCompletos;

            if (filtro === 'mes_actual') {
                ticketsFiltrados = ticketsCompletos.filter(ticket => {
                    const fechaTicket = new Date(ticket.fecha_creacion);
                    return fechaTicket.getMonth() === mesActual && fechaTicket.getFullYear() === añoActual;
                });
            }

            // Mostrar tickets filtrados
            const tableBody = document.getElementById('todosTicketsTable');
            tableBody.innerHTML = ticketsFiltrados.map(ticket => `
                <tr class="ticket-priority-${ticket.prioridad}" data-estado="${ticket.estado}" data-fecha="${ticket.fecha_creacion}">
                    <td><strong>#${ticket.id}</strong></td>
                    <td>${ticket.titulo}</td>
                    <td><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)} status-badge">${ticket.prioridad.toUpperCase()}</span></td>
                    <td><span class="badge bg-${obtenerColorEstado(ticket.estado)} status-badge">${ticket.estado.replace('_', ' ').toUpperCase()}</span></td>
                    <td>${ticket.solicitante_nombre}</td>
                    <td>${ticket.numero_ficha}</td>
                    <td>${ticket.asignado_nombre || '<span class="text-muted">Sin asignar</span>'}</td>
                    <td>${formatearFecha(ticket.fecha_creacion)}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="verTicket(${ticket.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${ticket.estado === 'abierto' ?
                            `<button class="btn btn-sm btn-outline-success ms-1" onclick="tomarTicket(${ticket.id})">
                                <i class="fas fa-hand-paper"></i>
                            </button>` : ''}
                    </td>
                </tr>
            `).join('');
        }

        function generarReporteExcel(tipo) {
            let url = 'api/reportes.php?tipo=' + tipo;
            
            if (tipo === 'estado') {
                const estado = document.getElementById('filtroEstado').value;
                url += '&estado=' + estado;
            } else if (tipo === 'fecha') {
                const fechaInicio = document.getElementById('fechaInicio').value;
                const fechaFin = document.getElementById('fechaFin').value;
                
                if (!fechaInicio || !fechaFin) {
                    mostrarMensaje('Por favor selecciona ambas fechas', 'warning');
                    return;
                }
                
                url += '&fecha_inicio=' + fechaInicio + '&fecha_fin=' + fechaFin;
            }
            
            const link = document.createElement('a');
            link.href = url;
            link.download = '';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            mostrarMensaje('Generando reporte Excel...', 'info');
        }

        function mostrarMensaje(mensaje, tipo) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${tipo} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        function formatearFecha(fecha) {
            if (!fecha) return '-';
            return new Date(fecha).toLocaleString('es-ES');
        }

        function obtenerColorPrioridad(prioridad) {
            switch(prioridad) {
                case 'urgente': return 'danger';
                case 'alta': return 'warning';
                case 'media': return 'info';
                case 'baja': return 'secondary';
                default: return 'secondary';
            }
        }

        function obtenerColorEstado(estado) {
            switch(estado) {
                case 'abierto': return 'primary';
                case 'en_proceso': return 'warning';
                case 'resuelto': return 'success';
                case 'cerrado': return 'secondary';
                default: return 'secondary';
            }
        }

        function configurarFechas() {
            const hoy = new Date().toISOString().split('T')[0];
            const hace30dias = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
            
            const fechaInicio = document.getElementById('fechaInicio');
            const fechaFin = document.getElementById('fechaFin');
            
            if (fechaInicio && fechaFin) {
                fechaInicio.value = hace30dias;
                fechaFin.value = hoy;
            }
        }

        // Actualizar datos automáticamente cada 30 segundos
        setInterval(() => {
            if (document.querySelector('#dashboard-tab').classList.contains('active')) {
                cargarDashboard();
            } else if (document.querySelector('#todos-tickets-tab').classList.contains('active')) {
                cargarTodosTickets();
            }
        }, 30000);
    </script>
    <?php endif; ?>
</body>
</html>