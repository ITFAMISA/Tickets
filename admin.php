<?php
// admin.php - Panel de administración para personal IT
require_once 'config/database.php';
iniciarSesion();

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
        $error_message = 'Usuario no encontrado o sin permisos administrativos';
    }
}

$isLoggedIn = isset($_SESSION['admin_id']);

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
            --color-surface: #f4f4f6;
            --color-text: #121212;
            --color-muted: rgba(0, 0, 0, 0.6);
            --color-border: rgba(211, 47, 47, 0.25);
            --color-border-soft: rgba(0, 0, 0, 0.08);
            --color-shadow: 0 32px 80px rgba(0, 0, 0, 0.14);
            --radius-sm: 10px;
            --radius-md: 16px;
            --radius-lg: 22px;
            --transition: all 0.35s ease;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--color-surface);
            color: var(--color-text);
            min-height: 100vh;
        }

        body.modal-open {
            overflow: hidden;
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
            padding: 1.4rem 5vw;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            z-index: 40;
            box-shadow: 0 14px 32px rgba(0, 0, 0, 0.22);
        }

        .app-header__brand {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .brand-icon {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.18);
            display: grid;
            place-items: center;
            font-size: 1.6rem;
        }

        .app-header__title {
            font-size: 1.45rem;
            font-weight: 600;
        }

        .app-header__subtitle {
            font-size: 0.85rem;
            opacity: 0.85;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .user-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.6rem 1.1rem;
            border-radius: 999px;
            border: 1.5px solid rgba(255, 255, 255, 0.45);
            background: rgba(255, 255, 255, 0.1);
            font-weight: 500;
        }

        .logout-link {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.65rem 1.1rem;
            border-radius: 999px;
            border: 1.5px solid rgba(255, 255, 255, 0.45);
            color: var(--color-white);
            transition: var(--transition);
        }

        .logout-link:hover {
            border-color: var(--color-white);
            background: rgba(255, 255, 255, 0.18);
        }

        .app-main {
            padding: 3rem 0 4rem;
        }

        .content-shell {
            width: min(1260px, 92vw);
            margin: 0 auto;
            display: grid;
            gap: 2.5rem;
        }

        .page-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 1.5rem;
        }

        .page-header__title h1 {
            font-size: 2rem;
            font-weight: 600;
        }

        .page-header__title p {
            color: var(--color-muted);
            font-size: 0.95rem;
        }

        .page-header__info {
            display: grid;
            gap: 0.35rem;
            font-size: 0.9rem;
            text-align: right;
        }

        .page-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 0.8rem;
        }

        .nav-button {
            border: 1.5px solid var(--color-border-soft);
            background: var(--color-white);
            border-radius: var(--radius-md);
            padding: 0.9rem 1.3rem;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            cursor: pointer;
        }

        .nav-button i { font-size: 1.1rem; color: var(--color-red); }

        .nav-button:hover { border-color: var(--color-red); }

        .nav-button.is-active {
            border-color: var(--color-red);
            color: var(--color-red);
            box-shadow: 0 0 0 4px rgba(211, 47, 47, 0.15);
        }

        .panel {
            background: var(--color-white);
            border-radius: var(--radius-lg);
            border: 1.5px solid var(--color-border);
            box-shadow: var(--color-shadow);
            padding: 2.4rem;
            display: grid;
            gap: 1.75rem;
        }

        .panel__header {
            display: grid;
            gap: 0.5rem;
        }

        .panel__header--inline {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1.25rem;
            flex-wrap: wrap;
        }

        .panel__header h2,
        .panel__header h3 {
            font-size: 1.35rem;
            font-weight: 600;
        }

        .panel__hint {
            font-size: 0.85rem;
            color: var(--color-muted);
        }

        .panel__body {
            display: grid;
            gap: 1.5rem;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.25rem;
        }

        .stat-card {
            border: 1.5px solid rgba(0, 0, 0, 0.08);
            border-radius: var(--radius-md);
            padding: 1.4rem;
            background: var(--color-white);
            display: grid;
            gap: 0.5rem;
            text-align: center;
            transition: var(--transition);
        }

        .stat-card i {
            font-size: 1.8rem;
            color: var(--color-red);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            border-color: var(--color-border);
        }

        .stat-card h4 {
            font-size: 1.6rem;
            font-weight: 600;
        }

        .stat-card span {
            font-size: 0.85rem;
            color: var(--color-muted);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 0.35rem 0.9rem;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid transparent;
            letter-spacing: 0.4px;
        }

        .bg-primary { background: var(--color-red); color: var(--color-white); }
        .bg-danger { background: rgba(211, 47, 47, 0.2); color: var(--color-red); border-color: rgba(211, 47, 47, 0.28); }
        .bg-warning { background: rgba(0, 0, 0, 0.09); color: var(--color-text); border-color: rgba(0, 0, 0, 0.12); }
        .bg-success { background: rgba(211, 47, 47, 0.12); color: var(--color-red); border-color: rgba(211, 47, 47, 0.2); }
        .bg-info { background: rgba(0, 0, 0, 0.05); color: var(--color-text); border-color: rgba(0, 0, 0, 0.12); }
        .bg-secondary { background: rgba(0, 0, 0, 0.05); color: var(--color-text); border-color: rgba(0, 0, 0, 0.12); }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            padding: 0.8rem 1.4rem;
            border-radius: var(--radius-md);
            border: none;
            font-weight: 600;
            font-size: 0.95rem;
            background: transparent;
            color: var(--color-text);
            cursor: pointer;
            transition: var(--transition);
        }

        .btn i { font-size: 1rem; }

        .btn-primary { background: var(--color-red); color: var(--color-white); }
        .btn-primary:hover { background: var(--color-red-strong); transform: translateY(-2px); }

        .btn-outline { border: 1.5px solid var(--color-red); color: var(--color-red); background: var(--color-white); }
        .btn-outline:hover { background: rgba(211, 47, 47, 0.12); }

        .btn-ghost { border: 1.5px solid var(--color-border-soft); color: var(--color-text); }
        .btn-ghost:hover { border-color: var(--color-red); color: var(--color-red); }

        .btn-chip {
            border: 1.5px solid rgba(0, 0, 0, 0.08);
            background: var(--color-white);
            border-radius: 999px;
            padding: 0.55rem 1.2rem;
            font-size: 0.85rem;
        }

        .btn-chip.is-active {
            border-color: var(--color-red);
            background: var(--color-red);
            color: var(--color-white);
        }

        .btn-sm {
            padding: 0.55rem 1rem;
            font-size: 0.82rem;
            border-radius: 20px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            text-transform: uppercase;
            font-size: 0.78rem;
            letter-spacing: 0.4px;
            color: var(--color-muted);
        }

        .data-table thead th {
            text-align: left;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            background: rgba(0, 0, 0, 0.03);
        }

        .data-table tbody tr {
            background: var(--color-white);
            transition: var(--transition);
        }

        .data-table tbody tr:hover {
            background: rgba(211, 47, 47, 0.05);
        }

        .data-table tbody td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-size: 0.92rem;
        }

        .table-actions {
            display: inline-flex;
            gap: 0.4rem;
        }

        .section { display: none; animation: fadeIn 0.45s ease; }
        .section.is-active { display: grid; gap: 2.5rem; }

        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            z-index: 60;
        }

        .modal--visible { display: flex; }

        .modal__dialog {
            width: min(880px, 95vw);
            background: var(--color-white);
            border-radius: var(--radius-lg);
            border: 1.5px solid var(--color-border);
            box-shadow: var(--color-shadow);
            display: flex;
            flex-direction: column;
            max-height: 90vh;
        }

        .modal__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.6rem 2rem;
            background: rgba(211, 47, 47, 0.08);
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }

        .modal__body {
            padding: 2rem;
            overflow-y: auto;
            display: grid;
            gap: 1.5rem;
        }

        .modal__footer {
            padding: 1.4rem 2rem;
            display: flex;
            justify-content: flex-end;
            gap: 0.8rem;
            border-top: 1px solid rgba(0, 0, 0, 0.08);
        }

        .btn-close {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: none;
            background: rgba(0, 0, 0, 0.12);
            color: var(--color-text);
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-close:hover {
            background: rgba(211, 47, 47, 0.3);
            color: var(--color-white);
        }

        .detail-columns { display: grid; gap: 1.25rem; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); }
        .detail-card { border: 1px solid rgba(0, 0, 0, 0.08); border-radius: var(--radius-md); padding: 1.25rem; display: grid; gap: 0.65rem; }
        .detail-card h6 { font-size: 1rem; font-weight: 600; }
        .detail-grid { display: grid; gap: 0.6rem; }
        .detail-grid span { font-size: 0.85rem; color: var(--color-muted); display: block; }

        .toast-stack {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            display: grid;
            gap: 0.75rem;
            z-index: 80;
            pointer-events: none;
        }

        .toast {
            min-width: 260px;
            max-width: 340px;
            background: var(--color-white);
            border: 1.5px solid rgba(0, 0, 0, 0.08);
            border-left: 5px solid var(--color-red);
            border-radius: var(--radius-md);
            padding: 0.95rem 1.2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            box-shadow: var(--color-shadow);
            animation: slideDown 0.4s ease;
            pointer-events: auto;
        }

        .toast--success { border-left-color: rgba(0, 0, 0, 0.45); }
        .toast--warning { border-left-color: rgba(211, 47, 47, 0.35); }
        .toast--danger { border-left-color: var(--color-red); }

        .toast__message { font-size: 0.9rem; }

        .toast__close {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: none;
            background: rgba(0, 0, 0, 0.12);
            color: var(--color-text);
            cursor: pointer;
            transition: var(--transition);
        }

        .toast__close:hover {
            background: rgba(211, 47, 47, 0.25);
            color: var(--color-white);
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--color-muted);
            display: grid;
            gap: 0.5rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 880px) {
            .panel { padding: 2rem; }
            .page-header__info { text-align: left; }
        }

        @media (max-width: 640px) {
            .app-header { padding: 1.1rem 1.6rem; }
            .brand-icon { width: 48px; height: 48px; font-size: 1.3rem; }
            .panel { padding: 1.75rem; }
            .modal__header,
            .modal__body,
            .modal__footer { padding-inline: 1.5rem; }
        }

        /* Login styles */
        .login-wrapper {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 3rem 1.5rem;
            background: var(--color-surface);
        }

        .auth-card {
            width: min(420px, 100%);
            background: var(--color-white);
            border-radius: var(--radius-lg);
            border: 1.5px solid var(--color-border);
            padding: 3rem 2.75rem;
            box-shadow: var(--color-shadow);
            display: grid;
            gap: 1.8rem;
        }

        .auth-header {
            text-align: center;
            display: grid;
            gap: 0.6rem;
        }

        .auth-header i {
            font-size: 2.6rem;
            color: var(--color-red);
        }

        .form-grid { display: grid; gap: 1.25rem; }

        .form-field { display: grid; gap: 0.55rem; }

        label { font-size: 0.9rem; font-weight: 500; }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.85rem 1rem;
            border-radius: var(--radius-md);
            border: 1.5px solid var(--color-border-soft);
            font-size: 0.95rem;
            font-family: inherit;
            transition: var(--transition);
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--color-red);
            box-shadow: 0 0 0 4px rgba(211, 47, 47, 0.15);
        }

        .login-actions { display: grid; gap: 1rem; }
    </style>
</head>
<body>
    <?php if (!$isLoggedIn): ?>
    <div class="login-wrapper">
        <section class="auth-card">
            <div class="auth-header">
                <i class="fas fa-shield-alt"></i>
                <h2>Administración IT</h2>
                <p class="panel__hint">Accede con tus credenciales de soporte</p>
            </div>

            <?php if (isset($error_message)): ?>
            <div class="toast toast--danger" style="position: static;">
                <span class="toast__message"><i class="fas fa-triangle-exclamation"></i> <?= $error_message ?></span>
                <button type="button" class="toast__close" onclick="this.closest('.toast').remove()"><i class="fas fa-xmark"></i></button>
            </div>
            <?php endif; ?>

            <form method="POST" class="form-grid">
                <input type="hidden" name="admin_login" value="1">
                <div class="form-field">
                    <label for="email">Correo institucional</label>
                    <input type="email" id="email" name="email" placeholder="admin@empresa.com" required>
                </div>
                <div class="form-field">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
                <div class="login-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i>Acceder al panel
                    </button>
                    <a href="index.php" class="btn btn-ghost" style="text-align:center;">
                        <i class="fas fa-arrow-left"></i>Volver al sistema
                    </a>
                </div>
            </form>
        </section>
    </div>
    <?php else: ?>

    <header class="app-header">
        <div class="app-header__brand">
            <div class="brand-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div>
                <div class="app-header__title">Panel administrativo</div>
                <div class="app-header__subtitle">Supervisión completa del ecosistema IT</div>
            </div>
        </div>
        <div class="header-actions">
            <span class="user-chip">
                <i class="fas fa-user-tie"></i>
                <?= htmlspecialchars($_SESSION['admin_nombre']) ?>
            </span>
            <a class="logout-link" href="?logout=1">
                <i class="fas fa-sign-out-alt"></i>Salir
            </a>
        </div>
    </header>

    <main class="app-main">
        <div class="content-shell">
            <div class="page-header">
                <div class="page-header__title">
                    <h1>Control operacional</h1>
                    <p>Gestiona tickets, reportes y desempeño del equipo IT desde un solo lugar.</p>
                </div>
                <div class="page-header__info">
                    <span>Sesión activa: <?= htmlspecialchars($_SESSION['admin_email']) ?></span>
                    <span>Rol: Administrador IT</span>
                </div>
            </div>

            <nav class="page-nav">
                <button class="nav-button is-active" data-section="dashboard" onclick="mostrarSeccion('dashboard', this)">
                    <i class="fas fa-gauge"></i>Dashboard
                </button>
                <button class="nav-button" data-section="todos-tickets" onclick="mostrarSeccion('todos-tickets', this)">
                    <i class="fas fa-ticket"></i>Todos los tickets
                </button>
                <button class="nav-button" data-section="reportes" onclick="mostrarSeccion('reportes', this)">
                    <i class="fas fa-chart-bar"></i>Reportes
                </button>
            </nav>

            <section id="dashboard" class="section is-active">
                <section class="panel">
                    <div class="panel__header">
                        <h2>Resumen general</h2>
                        <span class="panel__hint">Indicadores clave del soporte</span>
                    </div>
                    <div class="panel__body">
                        <div class="stat-grid" id="statsCards"></div>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel__header panel__header--inline">
                        <h3>Tickets recientes</h3>
                        <div class="panel__hint">Filtra por estado y periodo</div>
                    </div>
                    <div class="panel__body">
                        <div style="display:flex; flex-wrap:wrap; gap:0.6rem; justify-content:space-between;">
                            <div class="chip-group" style="display:flex; flex-wrap:wrap; gap:0.6rem;">
                                <button class="btn btn-chip is-active" onclick="filtrarTickets('todos', event)">Todos</button>
                                <button class="btn btn-chip" onclick="filtrarTickets('abierto', event)">Abiertos</button>
                                <button class="btn btn-chip" onclick="filtrarTickets('en_proceso', event)">En proceso</button>
                                <button class="btn btn-chip" onclick="filtrarTickets('resuelto', event)">Resueltos</button>
                            </div>
                            <div class="chip-group" style="display:flex; gap:0.6rem;">
                                <button class="btn btn-chip is-active" id="btnFiltroMesTodos" onclick="filtrarTicketsPorMes('todos', event)">Todos</button>
                                <button class="btn btn-chip" id="btnFiltroMesActual" onclick="filtrarTicketsPorMes('mes_actual', event)">Este mes</button>
                            </div>
                        </div>
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th>Prioridad</th>
                                        <th>Estado</th>
                                        <th>Solicitante</th>
                                        <th>Ficha</th>
                                        <th>Asignado</th>
                                        <th>Fecha</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="ticketsTable"></tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </section>

            <section id="todos-tickets" class="section">
                <section class="panel">
                    <div class="panel__header panel__header--inline">
                        <h2>Gestión total de tickets</h2>
                        <div class="panel__hint">Organiza y toma acciones masivas</div>
                    </div>
                    <div class="panel__body">
                        <div style="display:flex; flex-wrap:wrap; gap:0.6rem; justify-content:space-between;">
                            <div class="chip-group" style="display:flex; flex-wrap:wrap; gap:0.6rem;">
                                <button class="btn btn-chip is-active" onclick="filtrarTodosTickets('todos', event)">Todos</button>
                                <button class="btn btn-chip" onclick="filtrarTodosTickets('abierto', event)">Abiertos</button>
                                <button class="btn btn-chip" onclick="filtrarTodosTickets('en_proceso', event)">En proceso</button>
                                <button class="btn btn-chip" onclick="filtrarTodosTickets('resuelto', event)">Resueltos</button>
                            </div>
                            <div class="chip-group" style="display:flex; gap:0.6rem;">
                                <button class="btn btn-chip is-active" id="btnFiltroMesTodosTodos" onclick="filtrarTodosTicketsPorMes('todos', event)">Todos</button>
                                <button class="btn btn-chip" id="btnFiltroMesTodosMesActual" onclick="filtrarTodosTicketsPorMes('mes_actual', event)">Este mes</button>
                            </div>
                        </div>
                        <div class="table-wrapper">
                            <table class="data-table">
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
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="todosTicketsTable"></tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </section>

            <section id="reportes" class="section">
                <section class="panel">
                    <div class="panel__header">
                        <h2>Reportes y exportaciones</h2>
                        <span class="panel__hint">Genera archivos Excel listos para análisis</span>
                    </div>
                    <div class="panel__body">
                        <div class="stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
                            <article class="stat-card" style="gap:0.85rem;">
                                <i class="fas fa-file-excel"></i>
                                <h4>Reporte general</h4>
                                <span>Todos los tickets sin filtros.</span>
                                <button class="btn btn-primary" onclick="generarReporteExcel('general')"><i class="fas fa-download"></i>Descargar</button>
                            </article>
                            <article class="stat-card" style="gap:0.85rem;">
                                <i class="fas fa-chart-pie"></i>
                                <h4>Por estado</h4>
                                <span>Selecciona un estado específico.</span>
                                <select id="filtroEstado">
                                    <option value="todos">Todos los estados</option>
                                    <option value="abierto">Abiertos</option>
                                    <option value="en_proceso">En proceso</option>
                                    <option value="resuelto">Resueltos</option>
                                    <option value="cerrado">Cerrados</option>
                                </select>
                                <button class="btn btn-ghost" onclick="generarReporteExcel('estado')"><i class="fas fa-download"></i>Descargar</button>
                            </article>
                            <article class="stat-card" style="gap:0.85rem;">
                                <i class="fas fa-calendar"></i>
                                <h4>Por fecha</h4>
                                <span>Define el rango de fechas.</span>
                                <input type="date" id="fechaInicio">
                                <input type="date" id="fechaFin">
                                <button class="btn btn-outline" onclick="generarReporteExcel('fecha')"><i class="fas fa-download"></i>Descargar</button>
                            </article>
                        </div>
                    </div>
                </section>
            </section>
        </div>
    </main>

    <div class="modal" id="modalVerTicket" aria-hidden="true">
        <div class="modal__dialog">
            <header class="modal__header">
                <h5 id="modalVerTicketTitle"></h5>
                <button type="button" class="btn-close" data-modal-close aria-label="Cerrar"><i class="fas fa-xmark"></i></button>
            </header>
            <div class="modal__body" id="modalVerTicketBody"></div>
            <footer class="modal__footer" id="modalVerTicketFooter"></footer>
        </div>
    </div>

    <div class="modal" id="modalResolverTicket" aria-hidden="true">
        <div class="modal__dialog" style="width:min(520px, 92vw);">
            <header class="modal__header">
                <h5><i class="fas fa-check"></i>Resolver ticket</h5>
                <button type="button" class="btn-close" data-modal-close aria-label="Cerrar"><i class="fas fa-xmark"></i></button>
            </header>
            <div class="modal__body">
                <div class="form-field">
                    <label for="resolucionTexto">Detalle de la resolución *</label>
                    <textarea id="resolucionTexto" rows="4" placeholder="Registra la acción correctiva"></textarea>
                </div>
            </div>
            <footer class="modal__footer">
                <button type="button" class="btn btn-ghost" data-modal-close>Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="confirmarResolucion()"><i class="fas fa-check"></i>Marcar como resuelto</button>
            </footer>
        </div>
    </div>

    <div class="toast-stack" id="toastStack"></div>

    <?php endif; ?>

    <script>
        (function() {
            const modalRegistry = new WeakMap();

            class Modal {
                constructor(element) {
                    if (modalRegistry.has(element)) {
                        return modalRegistry.get(element);
                    }

                    this.element = element;
                    this.dialog = element.querySelector('.modal__dialog');
                    this.bindEvents();
                    modalRegistry.set(element, this);
                }

                bindEvents() {
                    this.element.addEventListener('click', (event) => {
                        if (event.target === this.element) {
                            this.hide();
                        }
                    });
                    this.element.querySelectorAll('[data-modal-close]').forEach(btn => {
                        btn.addEventListener('click', () => this.hide());
                    });
                }

                show() {
                    this.element.classList.add('modal--visible');
                    this.element.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('modal-open');
                }

                hide() {
                    this.element.classList.remove('modal--visible');
                    this.element.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('modal-open');
                }

                static getInstance(element) {
                    return modalRegistry.get(element) || new Modal(element);
                }
            }

            window.bootstrap = window.bootstrap || {};
            window.bootstrap.Modal = Modal;
        })();

        <?php if ($isLoggedIn): ?>
        const toastStack = document.getElementById('toastStack');
        const sections = document.querySelectorAll('.section');
        const navButtons = document.querySelectorAll('.nav-button');

        let usuarioActual = {
            id: <?= (int) $_SESSION['admin_id'] ?>,
            nombre: '<?= addslashes($_SESSION['admin_nombre']) ?>',
            email: '<?= addslashes($_SESSION['admin_email']) ?>',
            rol: 'it'
        };

        let ticketSeleccionado = null;
        let ticketsDashboard = [];
        let ticketsCompletos = [];

        document.addEventListener('DOMContentLoaded', () => {
            cargarDashboard();
            configurarFechas();
        });

        function mostrarMensaje(mensaje, tipo = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast toast--${tipo}`;
            toast.innerHTML = `
                <span class="toast__message">${mensaje}</span>
                <button type="button" class="toast__close" aria-label="Cerrar"><i class="fas fa-xmark"></i></button>
            `;

            const cerrar = () => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-10px)';
                setTimeout(() => toast.remove(), 250);
            };

            toast.querySelector('.toast__close').addEventListener('click', cerrar);
            toastStack.appendChild(toast);
            setTimeout(cerrar, 5000);
        }

        function mostrarSeccion(sectionId, trigger) {
            sections.forEach(section => {
                section.classList.toggle('is-active', section.id === sectionId);
            });
            navButtons.forEach(btn => {
                btn.classList.toggle('is-active', btn.dataset.section === sectionId);
            });

            if (sectionId === 'todos-tickets') {
                cargarTodosTickets();
            }
        }

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
            const cards = [
                { titulo: 'Total tickets', valor: stats.total, icono: 'fas fa-ticket' },
                { titulo: 'Abiertos', valor: stats.abiertos, icono: 'fas fa-folder-open' },
                { titulo: 'En proceso', valor: stats.en_proceso, icono: 'fas fa-cog' },
                { titulo: 'Resueltos', valor: stats.resueltos, icono: 'fas fa-circle-check' },
                { titulo: 'No resueltos', valor: stats.no_resueltos, icono: 'fas fa-circle-xmark' },
                { titulo: 'Pendientes', valor: stats.pendientes, icono: 'fas fa-clock' }
            ];

            container.innerHTML = cards.map(card => `
                <article class="stat-card">
                    <i class="${card.icono}"></i>
                    <h4>${card.valor}</h4>
                    <span>${card.titulo}</span>
                </article>
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
            ticketsDashboard = tickets || [];
            const tableBody = document.getElementById('ticketsTable');

            if (!ticketsDashboard.length) {
                tableBody.innerHTML = `<tr><td colspan="8"><div class="empty-state">No se registran tickets recientes.</div></td></tr>`;
                return;
            }

            tableBody.innerHTML = ticketsDashboard.map(ticket => `
                <tr class="ticket-priority-${ticket.prioridad}" data-estado="${ticket.estado}" data-fecha="${ticket.fecha_creacion}">
                    <td>${ticket.titulo}</td>
                    <td><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)}">${ticket.prioridad.toUpperCase()}</span></td>
                    <td><span class="badge bg-${obtenerColorEstado(ticket.estado)}">${ticket.estado.replace('_', ' ').toUpperCase()}</span></td>
                    <td>${ticket.solicitante_nombre}</td>
                    <td>${ticket.numero_ficha}</td>
                    <td>${ticket.asignado_nombre || '<span class="panel__hint">Sin asignar</span>'}</td>
                    <td>${formatearFecha(ticket.fecha_creacion)}</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn btn-outline btn-sm" onclick="verTicket(${ticket.id})"><i class="fas fa-eye"></i></button>
                            ${ticket.estado === 'abierto' ? `<button class="btn btn-ghost btn-sm" onclick="tomarTicket(${ticket.id})"><i class="fas fa-hand"></i></button>` : ''}
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function filtrarTickets(estado, event) {
            if (event) {
                const chips = event.currentTarget.parentElement.querySelectorAll('.btn-chip');
                chips.forEach(chip => chip.classList.remove('is-active'));
                event.currentTarget.classList.add('is-active');
            }

            const rows = document.querySelectorAll('#ticketsTable tr');
            rows.forEach(row => {
                if (!estado || estado === 'todos') {
                    row.style.display = '';
                } else {
                    row.style.display = row.getAttribute('data-estado') === estado ? '' : 'none';
                }
            });
        }

        function filtrarTicketsPorMes(filtro, event) {
            if (event) {
                const chips = event.currentTarget.parentElement.querySelectorAll('.btn-chip');
                chips.forEach(chip => chip.classList.remove('is-active'));
                event.currentTarget.classList.add('is-active');
            }

            const ahora = new Date();
            const mesActual = ahora.getMonth();
            const añoActual = ahora.getFullYear();

            const rows = document.querySelectorAll('#ticketsTable tr');
            rows.forEach(row => {
                if (!filtro || filtro === 'todos') {
                    row.style.display = '';
                } else {
                    const fecha = new Date(row.getAttribute('data-fecha'));
                    const mismoMes = fecha.getMonth() === mesActual && fecha.getFullYear() === añoActual;
                    row.style.display = filtro === 'mes_actual' && !mismoMes ? 'none' : '';
                }
            });
        }

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
            ticketsCompletos = tickets || [];
            const tableBody = document.getElementById('todosTicketsTable');

            if (!ticketsCompletos.length) {
                tableBody.innerHTML = `<tr><td colspan="9"><div class="empty-state">No hay tickets registrados.</div></td></tr>`;
                return;
            }

            tableBody.innerHTML = ticketsCompletos.map(ticket => `
                <tr class="ticket-priority-${ticket.prioridad}" data-estado="${ticket.estado}" data-fecha="${ticket.fecha_creacion}">
                    <td><strong>#${ticket.id}</strong></td>
                    <td>${ticket.titulo}</td>
                    <td><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)}">${ticket.prioridad.toUpperCase()}</span></td>
                    <td><span class="badge bg-${obtenerColorEstado(ticket.estado)}">${ticket.estado.replace('_', ' ').toUpperCase()}</span></td>
                    <td>${ticket.solicitante_nombre}</td>
                    <td>${ticket.numero_ficha}</td>
                    <td>${ticket.asignado_nombre || '<span class="panel__hint">Sin asignar</span>'}</td>
                    <td>${formatearFecha(ticket.fecha_creacion)}</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn btn-outline btn-sm" onclick="verTicket(${ticket.id})"><i class="fas fa-eye"></i></button>
                            ${ticket.estado === 'abierto' ? `<button class="btn btn-ghost btn-sm" onclick="tomarTicket(${ticket.id})"><i class="fas fa-hand"></i></button>` : ''}
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function filtrarTodosTickets(estado, event) {
            if (event) {
                const chips = event.currentTarget.parentElement.querySelectorAll('.btn-chip');
                chips.forEach(chip => chip.classList.remove('is-active'));
                event.currentTarget.classList.add('is-active');
            }

            const rows = document.querySelectorAll('#todosTicketsTable tr');
            rows.forEach(row => {
                if (!estado || estado === 'todos') {
                    row.style.display = '';
                } else {
                    row.style.display = row.getAttribute('data-estado') === estado ? '' : 'none';
                }
            });
        }

        function filtrarTodosTicketsPorMes(filtro, event) {
            if (event) {
                const chips = event.currentTarget.parentElement.querySelectorAll('.btn-chip');
                chips.forEach(chip => chip.classList.remove('is-active'));
                event.currentTarget.classList.add('is-active');
            }

            const ahora = new Date();
            const mesActual = ahora.getMonth();
            const añoActual = ahora.getFullYear();

            const rows = document.querySelectorAll('#todosTicketsTable tr');
            rows.forEach(row => {
                if (!filtro || filtro === 'todos') {
                    row.style.display = '';
                } else {
                    const fecha = new Date(row.getAttribute('data-fecha'));
                    const mismoMes = fecha.getMonth() === mesActual && fecha.getFullYear() === añoActual;
                    row.style.display = filtro === 'mes_actual' && !mismoMes ? 'none' : '';
                }
            });
        }

        async function verTicket(ticketId) {
            try {
                const response = await fetch(`api/tickets_admin.php?action=detalle&id=${ticketId}`);
                const data = await response.json();

                if (data.success) {
                    try {
                        const archivosResponse = await fetch(`api/tickets_publico.php?action=detalle&id=${ticketId}`);
                        const archivosData = await archivosResponse.json();
                        if (archivosData.success && archivosData.data.archivos) {
                            data.data.archivos = archivosData.data.archivos;
                        }
                    } catch (err) {
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
            const modalElement = document.getElementById('modalVerTicket');
            const title = document.getElementById('modalVerTicketTitle');
            const body = document.getElementById('modalVerTicketBody');
            const footer = document.getElementById('modalVerTicketFooter');

            title.innerHTML = `<i class="fas fa-ticket"></i> Ticket #${ticket.id} · ${ticket.titulo}`;

            const archivos = ticket.archivos || [];
            const attachments = archivos.length ? `
                <section class="detail-card">
                    <h6><i class="fas fa-paperclip"></i> Archivos adjuntos</h6>
                    <div class="detail-grid" style="display:grid; gap:0.6rem;">
                        ${archivos.map(archivo => {
                            const esImagen = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(archivo.tipo_archivo.toLowerCase());
                            return esImagen
                                ? `<div><button class="btn btn-outline btn-sm" onclick="verImagenCompleta('uploads/${archivo.ruta_archivo}', '${archivo.nombre_archivo}')"><i class="fas fa-image"></i>${archivo.nombre_archivo}</button></div>`
                                : `<div><a class="btn btn-outline btn-sm" href="uploads/${archivo.ruta_archivo}" target="_blank"><i class="fas fa-file"></i>${archivo.nombre_archivo}</a></div>`;
                        }).join('')}
                    </div>
                </section>
            ` : '';

            body.innerHTML = `
                <div class="detail-columns">
                    <section class="detail-card">
                        <h6>Resumen</h6>
                        <div class="detail-grid">
                            <div><span class="panel__hint">Descripción</span><strong>${ticket.descripcion}</strong></div>
                            <div><span class="panel__hint">Categoría</span><strong>${ticket.categoria}</strong></div>
                            <div><span class="panel__hint">Prioridad</span><strong><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)}">${ticket.prioridad.toUpperCase()}</span></strong></div>
                            <div><span class="panel__hint">Estado</span><strong><span class="badge bg-${obtenerColorEstado(ticket.estado)}">${ticket.estado.replace('_', ' ').toUpperCase()}</span></strong></div>
                        </div>
                    </section>
                    <section class="detail-card">
                        <h6>Seguimiento</h6>
                        <div class="detail-grid">
                            <div><span class="panel__hint">Solicitante</span><strong>${ticket.solicitante_nombre}</strong></div>
                            <div><span class="panel__hint">Ficha</span><strong>${ticket.numero_ficha}</strong></div>
                            <div><span class="panel__hint">Asignado a</span><strong>${ticket.asignado_nombre || 'Sin asignar'}</strong></div>
                            <div><span class="panel__hint">Creado el</span><strong>${formatearFecha(ticket.fecha_creacion)}</strong></div>
                            ${ticket.fecha_resolucion ? `<div><span class="panel__hint">Resuelto el</span><strong>${formatearFecha(ticket.fecha_resolucion)}</strong></div>` : ''}
                            ${ticket.resolucion ? `<div><span class="panel__hint">Resolución</span><strong>${ticket.resolucion}</strong></div>` : ''}
                        </div>
                    </section>
                    <section class="detail-card">
                        <h6>Historial</h6>
                        <div class="detail-grid">
                            <div><span class="panel__hint">Ticket creado</span><strong>${formatearFecha(ticket.fecha_creacion)}</strong></div>
                            ${ticket.asignado_nombre ? `<div><span class="panel__hint">Asignado</span><strong>${ticket.asignado_nombre}</strong></div>` : ''}
                            ${ticket.fecha_resolucion ? `<div><span class="panel__hint">Resuelto</span><strong>${formatearFecha(ticket.fecha_resolucion)}</strong></div>` : ''}
                        </div>
                    </section>
                    ${attachments}
                </div>
            `;

            let footerButtons = `<button type="button" class="btn btn-ghost" data-modal-close>Cerrar</button>`;

            if (ticket.estado === 'abierto') {
                footerButtons = `
                    <button type="button" class="btn btn-primary" onclick="tomarTicket(${ticket.id})"><i class="fas fa-hand"></i>Tomar ticket</button>
                    <button type="button" class="btn btn-ghost" data-modal-close>Cerrar</button>
                `;
            } else if (ticket.estado === 'en_proceso' && ticket.asignado_a == usuarioActual.id) {
                footerButtons = `
                    <button type="button" class="btn btn-primary" onclick="mostrarModalResolver(${ticket.id})"><i class="fas fa-check"></i>Marcar como resuelto</button>
                    <button type="button" class="btn btn-ghost" data-modal-close>Cerrar</button>
                `;
            }

            footer.innerHTML = footerButtons;
            new bootstrap.Modal(modalElement).show();
        }

        function verImagenCompleta(rutaImagen, nombreArchivo) {
            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.innerHTML = `
                <div class="modal__dialog" style="width:min(720px, 94vw);">
                    <header class="modal__header">
                        <h5><i class="fas fa-image"></i>${nombreArchivo}</h5>
                        <button type="button" class="btn-close" data-modal-close aria-label="Cerrar"><i class="fas fa-xmark"></i></button>
                    </header>
                    <div class="modal__body" style="justify-items:center;">
                        <img src="${rutaImagen}" alt="${nombreArchivo}" style="max-width:100%; max-height:70vh; border-radius: var(--radius-md);">
                    </div>
                    <footer class="modal__footer">
                        <a class="btn btn-outline" href="${rutaImagen}" download="${nombreArchivo}"><i class="fas fa-download"></i>Descargar</a>
                        <button type="button" class="btn btn-ghost" data-modal-close>Cerrar</button>
                    </footer>
                </div>
            `;
            document.body.appendChild(modal);
            const instance = new bootstrap.Modal(modal);
            modal.addEventListener('transitionend', () => {
                if (!modal.classList.contains('modal--visible')) {
                    modal.remove();
                }
            });
            instance.show();
        }

        async function tomarTicket(ticketId) {
            try {
                const response = await fetch(`api/tickets_admin.php?action=tomar&id=${ticketId}`, { method: 'POST' });
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
            const resolucion = document.getElementById('resolucionTexto').value.trim();

            if (!resolucion) {
                mostrarMensaje('Describe la resolución aplicada', 'warning');
                return;
            }

            try {
                const response = await fetch(`api/tickets_admin.php?action=resolver&id=${ticketSeleccionado}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ resolucion })
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

        function generarReporteExcel(tipo) {
            let url = 'api/reportes_admin.php?tipo=' + tipo;

            if (tipo === 'estado') {
                const estado = document.getElementById('filtroEstado').value;
                url += '&estado=' + estado;
            } else if (tipo === 'fecha') {
                const inicio = document.getElementById('fechaInicio').value;
                const fin = document.getElementById('fechaFin').value;

                if (!inicio || !fin) {
                    mostrarMensaje('Selecciona ambas fechas para generar el reporte', 'warning');
                    return;
                }

                url += `&fecha_inicio=${inicio}&fecha_fin=${fin}`;
            }

            const link = document.createElement('a');
            link.href = url;
            link.download = '';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            mostrarMensaje('Generando reporte, la descarga iniciará enseguida', 'success');
        }

        function configurarFechas() {
            const hoy = new Date().toISOString().split('T')[0];
            const hace30dias = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

            const inicio = document.getElementById('fechaInicio');
            const fin = document.getElementById('fechaFin');
            if (inicio && fin) {
                inicio.value = hace30dias;
                fin.value = hoy;
            }
        }

        function obtenerColorPrioridad(prioridad) {
            switch (prioridad) {
                case 'urgente': return 'danger';
                case 'alta': return 'warning';
                case 'media': return 'info';
                default: return 'secondary';
            }
        }

        function obtenerColorEstado(estado) {
            switch (estado) {
                case 'abierto': return 'primary';
                case 'en_proceso': return 'warning';
                case 'resuelto': return 'success';
                case 'cerrado': return 'secondary';
                default: return 'secondary';
            }
        }

        function formatearFecha(fecha) {
            if (!fecha) return '-';
            return new Date(fecha).toLocaleString('es-ES');
        }

        setInterval(() => {
            cargarDashboard();
        }, 30000);
        <?php endif; ?>
    </script>
</body>
</html>
