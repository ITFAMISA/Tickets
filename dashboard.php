<?php
// dashboard.php
require_once 'config/database.php';
verificarSesion();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Soporte IT - Panel</title>
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
            --color-surface: #f5f5f6;
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

        .nav-button i {
            font-size: 1.1rem;
            color: var(--color-red);
        }

        .nav-button:hover {
            border-color: var(--color-red);
        }

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

        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.1rem;
        }

        .person-card {
            border: 1.5px solid rgba(0, 0, 0, 0.08);
            border-radius: var(--radius-md);
            background: var(--color-white);
            padding: 1.4rem;
            display: grid;
            gap: 0.55rem;
            text-align: center;
            transition: var(--transition);
        }

        .person-card:hover {
            border-color: var(--color-border);
        }

        .person-card__icon {
            font-size: 2.4rem;
            color: var(--color-red);
        }

        .person-card__note {
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
            border-radius: var(--radius-md);
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
            width: min(780px, 95vw);
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

        .form-grid {
            display: grid;
            gap: 1.25rem;
        }

        .form-grid--two {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
        }

        .form-field {
            display: grid;
            gap: 0.5rem;
        }

        label {
            font-size: 0.9rem;
            font-weight: 500;
        }

        input[type="text"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            border: 1.5px solid var(--color-border-soft);
            border-radius: var(--radius-md);
            padding: 0.8rem 1rem;
            font-size: 0.95rem;
            font-family: inherit;
            background: var(--color-white);
            color: var(--color-text);
            transition: var(--transition);
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: var(--color-red);
            box-shadow: 0 0 0 4px rgba(211, 47, 47, 0.15);
        }

        textarea {
            resize: vertical;
        }

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

        .toast__message {
            font-size: 0.9rem;
        }

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

        .btn-floating {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 58px;
            height: 58px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            box-shadow: var(--color-shadow);
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
            .panel {
                padding: 2rem;
            }

            .page-header__info {
                text-align: left;
            }
        }

        @media (max-width: 640px) {
            .app-header {
                padding: 1.1rem 1.6rem;
            }

            .brand-icon {
                width: 48px;
                height: 48px;
                font-size: 1.3rem;
            }

            .panel {
                padding: 1.75rem;
            }

            .modal__header,
            .modal__body,
            .modal__footer {
                padding-inline: 1.5rem;
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
                <div class="app-header__subtitle">Panel operativo y seguimiento</div>
            </div>
        </div>
        <div class="header-actions">
            <span class="user-chip">
                <i class="fas fa-user-circle"></i>
                <?= htmlspecialchars($_SESSION['nombre']) ?> · <?= strtoupper($_SESSION['rol']) ?>
            </span>
            <a class="logout-link" href="logout.php">
                <i class="fas fa-sign-out-alt"></i>
                Cerrar sesión
            </a>
        </div>
    </header>

    <main class="app-main">
        <div class="content-shell">
            <div class="page-header">
                <div class="page-header__title">
                    <h1>Panel de soporte</h1>
                    <p>Control total de tickets, métricas y personal operativo.</p>
                </div>
                <div class="page-header__info">
                    <span>Actualizado en tiempo real</span>
                    <span>Correo: <?= htmlspecialchars($_SESSION['email']) ?></span>
                </div>
            </div>

            <nav class="page-nav" id="mainNav">
                <button class="nav-button is-active" data-section="dashboard" onclick="mostrarSeccion('dashboard', this)">
                    <i class="fas fa-gauge"></i>Dashboard
                </button>
                <?php if ($_SESSION['rol'] === 'solicitante'): ?>
                    <button class="nav-button" data-section="mis-tickets" onclick="mostrarSeccion('mis-tickets', this)">
                        <i class="fas fa-list"></i>Mis tickets
                    </button>
                <?php endif; ?>
                <?php if ($_SESSION['rol'] === 'it'): ?>
                    <button class="nav-button" data-section="todos-tickets" onclick="mostrarSeccion('todos-tickets', this)">
                        <i class="fas fa-ticket"></i>Todos los tickets
                    </button>
                    <button class="nav-button" data-section="reportes" onclick="mostrarSeccion('reportes', this)">
                        <i class="fas fa-chart-bar"></i>Reportes
                    </button>
                <?php endif; ?>
            </nav>

            <section id="dashboard" class="section is-active">
                <section class="panel">
                    <div class="panel__header panel__header--inline">
                        <h2>Visión general</h2>
                        <span class="panel__hint">Indicadores principales del sistema</span>
                    </div>
                    <div class="panel__body">
                        <div class="stat-grid" id="statsCards"></div>
                    </div>
                </section>

                <?php if ($_SESSION['rol'] === 'solicitante'): ?>
                <section class="panel">
                    <div class="panel__header panel__header--inline">
                        <h3>Disponibilidad del personal IT</h3>
                        <span class="panel__hint">Actualizado cada 30 segundos</span>
                    </div>
                    <div class="panel__body">
                        <div class="status-grid" id="personalIT"></div>
                    </div>
                </section>
                <?php endif; ?>

                <section class="panel">
                    <div class="panel__header panel__header--inline">
                        <h3>Tickets recientes</h3>
                        <div class="chip-group" style="display:flex; flex-wrap:wrap; gap:0.6rem;">
                            <button class="btn btn-chip is-active" onclick="filtrarTickets('todos', event)">Todos</button>
                            <button class="btn btn-chip" onclick="filtrarTickets('abierto', event)">Abiertos</button>
                            <button class="btn btn-chip" onclick="filtrarTickets('en_proceso', event)">En proceso</button>
                            <button class="btn btn-chip" onclick="filtrarTickets('resuelto', event)">Resueltos</button>
                        </div>
                    </div>
                    <div class="panel__body">
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Título</th>
                                        <th>Prioridad</th>
                                        <th>Estado</th>
                                        <th>Solicitante</th>
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

            <?php if ($_SESSION['rol'] === 'solicitante'): ?>
            <section id="mis-tickets" class="section">
                <section class="panel">
                    <div class="panel__header panel__header--inline">
                        <h2>Mis tickets</h2>
                        <button class="btn btn-primary" onclick="mostrarModalNuevoTicket()">
                            <i class="fas fa-plus"></i>Nuevo ticket
                        </button>
                    </div>
                    <div class="panel__body">
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Título</th>
                                        <th>Prioridad</th>
                                        <th>Estado</th>
                                        <th>Asignado</th>
                                        <th>Fecha</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="misTicketsTable"></tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </section>
            <?php endif; ?>

            <?php if ($_SESSION['rol'] === 'it'): ?>
            <section id="todos-tickets" class="section">
                <section class="panel">
                    <div class="panel__header panel__header--inline">
                        <h2>Todos los tickets</h2>
                        <div class="chip-group" style="display:flex; flex-wrap:wrap; gap:0.6rem;">
                            <button class="btn btn-chip is-active" onclick="filtrarTodosTickets('todos', event)">Todos</button>
                            <button class="btn btn-chip" onclick="filtrarTodosTickets('abierto', event)">Abiertos</button>
                            <button class="btn btn-chip" onclick="filtrarTodosTickets('en_proceso', event)">En proceso</button>
                            <button class="btn btn-chip" onclick="filtrarTodosTickets('resuelto', event)">Resueltos</button>
                        </div>
                    </div>
                    <div class="panel__body">
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Título</th>
                                        <th>Prioridad</th>
                                        <th>Estado</th>
                                        <th>Solicitante</th>
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
                        <span class="panel__hint">Genera archivos Excel personalizados</span>
                    </div>
                    <div class="panel__body">
                        <div class="status-grid" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
                            <article class="person-card" style="gap:0.8rem; text-align:left;">
                                <div>
                                    <h4 style="font-size:1.1rem;">Reporte general</h4>
                                    <p class="person-card__note">Exporta todos los tickets en formato Excel.</p>
                                </div>
                                <button class="btn btn-primary" onclick="generarReporteExcel('general')">
                                    <i class="fas fa-download"></i>Descargar
                                </button>
                            </article>
                            <article class="person-card" style="gap:0.8rem; text-align:left;">
                                <div>
                                    <h4 style="font-size:1.1rem;">Por estado</h4>
                                    <p class="person-card__note">Selecciona un estado específico para el reporte.</p>
                                </div>
                                <select id="filtroEstado">
                                    <option value="todos">Todos los estados</option>
                                    <option value="abierto">Abiertos</option>
                                    <option value="en_proceso">En proceso</option>
                                    <option value="resuelto">Resueltos</option>
                                    <option value="cerrado">Cerrados</option>
                                </select>
                                <button class="btn btn-ghost" onclick="generarReporteExcel('estado')">
                                    <i class="fas fa-download"></i>Descargar
                                </button>
                            </article>
                            <article class="person-card" style="gap:0.8rem; text-align:left;">
                                <div>
                                    <h4 style="font-size:1.1rem;">Por fecha</h4>
                                    <p class="person-card__note">Define un rango de fechas para filtrar los resultados.</p>
                                </div>
                                <input type="date" id="fechaInicio">
                                <input type="date" id="fechaFin">
                                <button class="btn btn-outline" onclick="generarReporteExcel('fecha')">
                                    <i class="fas fa-download"></i>Descargar
                                </button>
                            </article>
                        </div>
                    </div>
                </section>
            </section>
            <?php endif; ?>
        </div>
    </main>

    <div class="modal" id="modalNuevoTicket" aria-hidden="true">
        <div class="modal__dialog">
            <header class="modal__header">
                <h5><i class="fas fa-plus"></i>Nuevo ticket</h5>
                <button type="button" class="btn-close" data-modal-close aria-label="Cerrar"><i class="fas fa-xmark"></i></button>
            </header>
            <div class="modal__body">
                <form id="formNuevoTicket" class="form-grid form-grid--two">
                    <div class="form-field" style="grid-column: 1 / -1;">
                        <label for="titulo">Título *</label>
                        <input type="text" id="titulo" required>
                    </div>
                    <div class="form-field">
                        <label for="prioridad">Prioridad</label>
                        <select id="prioridad">
                            <option value="baja">Baja</option>
                            <option value="media" selected>Media</option>
                            <option value="alta">Alta</option>
                            <option value="urgente">Urgente</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="categoria">Categoría</label>
                        <select id="categoria">
                            <option value="Hardware">Hardware</option>
                            <option value="Software">Software</option>
                            <option value="Red">Red / Conectividad</option>
                            <option value="Email">Email</option>
                            <option value="Impresoras">Impresoras</option>
                            <option value="Accesos">Accesos / Permisos</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="form-field" style="grid-column: 1 / -1;">
                        <label for="descripcion">Descripción *</label>
                        <textarea id="descripcion" rows="4" required placeholder="Explica el incidente con el mayor detalle posible"></textarea>
                    </div>
                </form>
            </div>
            <footer class="modal__footer">
                <button type="button" class="btn btn-ghost" data-modal-close>Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="crearTicket()">
                    <i class="fas fa-save"></i>Crear ticket
                </button>
            </footer>
        </div>
    </div>

    <div class="modal" id="modalVerTicket" aria-hidden="true">
        <div class="modal__dialog" style="width:min(880px, 96vw);">
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
                <h5><i class="fas fa-wrench"></i>Resolver ticket</h5>
                <button type="button" class="btn-close" data-modal-close aria-label="Cerrar"><i class="fas fa-xmark"></i></button>
            </header>
            <div class="modal__body">
                <div class="form-field">
                    <label for="resolucionTexto">Detalle de la resolución *</label>
                    <textarea id="resolucionTexto" rows="5" placeholder="Describe cómo se resolvió el requerimiento"></textarea>
                </div>
            </div>
            <footer class="modal__footer">
                <button type="button" class="btn btn-ghost" data-modal-close>Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="confirmarResolucion()">
                    <i class="fas fa-check"></i>Marcar como resuelto
                </button>
            </footer>
        </div>
    </div>

    <?php if ($_SESSION['rol'] === 'solicitante'): ?>
    <button class="btn btn-primary btn-floating" onclick="mostrarModalNuevoTicket()">
        <i class="fas fa-plus"></i>
    </button>
    <?php endif; ?>

    <div class="toast-stack" id="toastStack"></div>

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

        const toastStack = document.getElementById('toastStack');
        const sections = document.querySelectorAll('.section');
        const navButtons = document.querySelectorAll('.nav-button');

        let usuarioActual = {
            id: <?= (int) $_SESSION['usuario_id'] ?>,
            nombre: '<?= addslashes($_SESSION['nombre']) ?>',
            email: '<?= addslashes($_SESSION['email']) ?>',
            rol: '<?= $_SESSION['rol'] ?>'
        };

        let ticketSeleccionado = null;

        document.addEventListener('DOMContentLoaded', () => {
            cargarDashboard();
            configurarFechas();
            <?php if ($_SESSION['rol'] === 'solicitante'): ?>
            mostrarSeccion('dashboard');
            <?php else: ?>
            mostrarSeccion('dashboard');
            <?php endif; ?>
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

            if (sectionId === 'mis-tickets') {
                cargarMisTickets();
            } else if (sectionId === 'todos-tickets') {
                cargarTodosTickets();
            }
        }

        function cargarDashboard() {
            cargarEstadisticas();
            cargarTickets();
            if (usuarioActual.rol === 'solicitante') {
                cargarPersonalIT();
                cargarMisTickets();
            }
        }

        async function cargarEstadisticas() {
            try {
                const response = await fetch('api/tickets.php?action=estadisticas');
                const data = await response.json();

                if (data.success) {
                    renderEstadisticas(data.data);
                }
            } catch (error) {
                console.error('Error al cargar estadísticas:', error);
            }
        }

        function renderEstadisticas(stats) {
            const container = document.getElementById('statsCards');
            if (!stats) {
                container.innerHTML = '';
                return;
            }

            const baseCards = usuarioActual.rol === 'it'
                ? [
                    { titulo: 'Total tickets', valor: stats.total, icono: 'fas fa-ticket', descripcion: 'Tickets registrados' },
                    { titulo: 'Abiertos', valor: stats.abiertos, icono: 'fas fa-folder-open', descripcion: 'Pendientes de atención' },
                    { titulo: 'En proceso', valor: stats.en_proceso, icono: 'fas fa-cog', descripcion: 'Asignados y en curso' },
                    { titulo: 'Resueltos', valor: stats.resueltos, icono: 'fas fa-circle-check', descripcion: 'Pendientes de cierre' },
                    { titulo: 'No resueltos', valor: stats.no_resueltos, icono: 'fas fa-circle-xmark', descripcion: 'Reportes sin solución' },
                    { titulo: 'Pendientes', valor: stats.pendientes, icono: 'fas fa-clock', descripcion: 'En espera de acción' }
                ]
                : [
                    { titulo: 'Mis tickets', valor: stats.total, icono: 'fas fa-ticket', descripcion: 'Tickets creados por ti' },
                    { titulo: 'Abiertos', valor: stats.abiertos, icono: 'fas fa-folder-open', descripcion: 'Pendientes de respuesta' },
                    { titulo: 'En proceso', valor: stats.en_proceso, icono: 'fas fa-cog', descripcion: 'Atendidos por IT' },
                    { titulo: 'Resueltos', valor: stats.resueltos, icono: 'fas fa-circle-check', descripcion: 'Aguardando tu confirmación' },
                    { titulo: 'No resueltos', valor: stats.no_resueltos, icono: 'fas fa-circle-xmark', descripcion: 'Requieren seguimiento' },
                    { titulo: 'Pendientes', valor: stats.pendientes, icono: 'fas fa-clock', descripcion: 'Solicitudes abiertas' }
                ];

            container.innerHTML = baseCards.map(card => `
                <article class="stat-card">
                    <i class="${card.icono}"></i>
                    <h4>${card.valor}</h4>
                    <span>${card.titulo}</span>
                    <small class="panel__hint">${card.descripcion}</small>
                </article>
            `).join('');
        }

        async function cargarPersonalIT() {
            try {
                const response = await fetch('api/tickets.php?action=personal-it');
                const data = await response.json();

                if (data.success) {
                    mostrarPersonalIT(data.data);
                }
            } catch (error) {
                console.error('Error al cargar personal IT:', error);
            }
        }

        function mostrarPersonalIT(personal) {
            const container = document.getElementById('personalIT');
            if (!personal || !personal.length) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-user-slash" style="font-size:2rem;"></i>
                        <p>No hay personal disponible en este momento.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = personal.map(persona => `
                <article class="person-card">
                    <div class="person-card__icon"><i class="fas fa-user-circle"></i></div>
                    <h5>${persona.nombre}</h5>
                    <span class="badge bg-${persona.estado === 'disponible' ? 'success' : persona.estado === 'ocupado' ? 'warning' : 'danger'}">
                        ${persona.estado.charAt(0).toUpperCase() + persona.estado.slice(1)}
                    </span>
                    ${persona.tickets_activos > 0 ? `<span class="person-card__note">${persona.tickets_activos} ticket(s) activo(s)</span>` : ''}
                </article>
            `).join('');
        }

        async function cargarTickets() {
            try {
                const response = await fetch('api/tickets.php');
                const data = await response.json();

                if (data.success) {
                    renderTickets(data.data);
                }
            } catch (error) {
                console.error('Error al cargar tickets:', error);
            }
        }

        function renderTickets(tickets) {
            const tableBody = document.getElementById('ticketsTable');
            if (!tickets || !tickets.length) {
                tableBody.innerHTML = `
                    <tr><td colspan="8"><div class="empty-state">No se encontraron tickets recientes.</div></td></tr>
                `;
                return;
            }

            tableBody.innerHTML = tickets.map(ticket => `
                <tr class="ticket-priority-${ticket.prioridad}" data-estado="${ticket.estado}">
                    <td><strong>#${ticket.id}</strong></td>
                    <td>${ticket.titulo}</td>
                    <td><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)}">${ticket.prioridad.toUpperCase()}</span></td>
                    <td><span class="badge bg-${obtenerColorEstado(ticket.estado)}">${ticket.estado.replace('_', ' ').toUpperCase()}</span></td>
                    <td>${ticket.solicitante_nombre}</td>
                    <td>${ticket.asignado_nombre || '<span class="panel__hint">Sin asignar</span>'}</td>
                    <td>${formatearFecha(ticket.fecha_creacion)}</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn btn-outline btn-sm" onclick="verTicket(${ticket.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${usuarioActual.rol === 'it' && ticket.estado === 'abierto' ? `
                                <button class="btn btn-ghost btn-sm" onclick="tomarTicket(${ticket.id})">
                                    <i class="fas fa-hand"></i>
                                </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        async function cargarMisTickets() {
            if (usuarioActual.rol !== 'solicitante') return;
            try {
                const response = await fetch('api/tickets.php?action=mis-tickets');
                const data = await response.json();

                if (data.success) {
                    mostrarMisTickets(data.data);
                }
            } catch (error) {
                console.error('Error al cargar mis tickets:', error);
            }
        }

        function mostrarMisTickets(tickets) {
            const tableBody = document.getElementById('misTicketsTable');
            if (!tickets || !tickets.length) {
                tableBody.innerHTML = `
                    <tr><td colspan="7"><div class="empty-state">Aún no has creado tickets.</div></td></tr>
                `;
                return;
            }

            tableBody.innerHTML = tickets.map(ticket => `
                <tr class="ticket-priority-${ticket.prioridad}" data-estado="${ticket.estado}">
                    <td><strong>#${ticket.id}</strong></td>
                    <td>${ticket.titulo}</td>
                    <td><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)}">${ticket.prioridad.toUpperCase()}</span></td>
                    <td><span class="badge bg-${obtenerColorEstado(ticket.estado)}">${ticket.estado.replace('_', ' ').toUpperCase()}</span></td>
                    <td>${ticket.asignado_nombre || '<span class="panel__hint">Sin asignar</span>'}</td>
                    <td>${formatearFecha(ticket.fecha_creacion)}</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn btn-outline btn-sm" onclick="verTicket(${ticket.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        async function cargarTodosTickets() {
            if (usuarioActual.rol !== 'it') return;
            try {
                const response = await fetch('api/tickets.php?action=todos');
                const data = await response.json();

                if (data.success) {
                    mostrarTodosTickets(data.data);
                }
            } catch (error) {
                console.error('Error al cargar todos los tickets:', error);
            }
        }

        function mostrarTodosTickets(tickets) {
            const tableBody = document.getElementById('todosTicketsTable');
            if (!tickets || !tickets.length) {
                tableBody.innerHTML = `
                    <tr><td colspan="8"><div class="empty-state">No se encontraron tickets registrados.</div></td></tr>
                `;
                return;
            }

            tableBody.innerHTML = tickets.map(ticket => `
                <tr class="ticket-priority-${ticket.prioridad}" data-estado="${ticket.estado}">
                    <td><strong>#${ticket.id}</strong></td>
                    <td>${ticket.titulo}</td>
                    <td><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)}">${ticket.prioridad.toUpperCase()}</span></td>
                    <td><span class="badge bg-${obtenerColorEstado(ticket.estado)}">${ticket.estado.replace('_', ' ').toUpperCase()}</span></td>
                    <td>${ticket.solicitante_nombre}</td>
                    <td>${ticket.asignado_nombre || '<span class="panel__hint">Sin asignar</span>'}</td>
                    <td>${formatearFecha(ticket.fecha_creacion)}</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn btn-outline btn-sm" onclick="verTicket(${ticket.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${ticket.estado === 'abierto' ? `
                                <button class="btn btn-ghost btn-sm" onclick="tomarTicket(${ticket.id})">
                                    <i class="fas fa-hand"></i>
                                </button>
                            ` : ''}
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

        function mostrarModalNuevoTicket() {
            new bootstrap.Modal(document.getElementById('modalNuevoTicket')).show();
        }

        async function crearTicket() {
            const titulo = document.getElementById('titulo').value.trim();
            const descripcion = document.getElementById('descripcion').value.trim();
            const prioridad = document.getElementById('prioridad').value;
            const categoria = document.getElementById('categoria').value;

            if (!titulo || !descripcion) {
                mostrarMensaje('Por favor completa los campos obligatorios', 'warning');
                return;
            }

            try {
                const response = await fetch('api/tickets.php?action=crear', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ titulo, descripcion, prioridad, categoria })
                });

                const data = await response.json();

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalNuevoTicket')).hide();
                    document.getElementById('formNuevoTicket').reset();
                    cargarDashboard();
                    mostrarMensaje('Ticket creado exitosamente', 'success');
                } else {
                    mostrarMensaje(data.message || 'No fue posible crear el ticket', 'danger');
                }
            } catch (error) {
                mostrarMensaje('Error de conexión', 'danger');
            }
        }

        async function verTicket(ticketId) {
            try {
                const response = await fetch(`api/tickets.php?action=detalle&id=${ticketId}`);
                const data = await response.json();

                if (data.success) {
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

            const history = [];
            history.push({ label: 'Creado', value: formatearFecha(ticket.fecha_creacion) });
            if (ticket.asignado_nombre) {
                history.push({ label: 'Asignado', value: `A cargo de ${ticket.asignado_nombre}` });
            }
            if (ticket.fecha_resolucion) {
                history.push({ label: 'Resuelto', value: formatearFecha(ticket.fecha_resolucion) });
            }
            if (ticket.estado === 'cerrado' && ticket.satisfaccion) {
                history.push({ label: 'Cierre', value: `Satisfacción: ${ticket.satisfaccion.toUpperCase()}` });
            }

            body.innerHTML = `
                <div class="detail-layout" style="display:grid; gap:1.5rem;">
                    <div class="detail-columns" style="display:grid; gap:1.25rem; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
                        <section class="person-card" style="text-align:left; gap:0.75rem;">
                            <h5>Resumen del ticket</h5>
                            <div class="detail-grid" style="display:grid; gap:0.65rem;">
                                <div><span class="panel__hint">Descripción</span><strong>${ticket.descripcion}</strong></div>
                                <div><span class="panel__hint">Categoría</span><strong>${ticket.categoria}</strong></div>
                                <div><span class="panel__hint">Prioridad</span><strong><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)}">${ticket.prioridad.toUpperCase()}</span></strong></div>
                                <div><span class="panel__hint">Estado</span><strong><span class="badge bg-${obtenerColorEstado(ticket.estado)}">${ticket.estado.replace('_', ' ').toUpperCase()}</span></strong></div>
                            </div>
                        </section>
                        <section class="person-card" style="text-align:left; gap:0.75rem;">
                            <h5>Seguimiento</h5>
                            <div class="detail-grid" style="display:grid; gap:0.65rem;">
                                <div><span class="panel__hint">Solicitante</span><strong>${ticket.solicitante_nombre}</strong></div>
                                <div><span class="panel__hint">Asignado a</span><strong>${ticket.asignado_nombre || 'Sin asignar'}</strong></div>
                                <div><span class="panel__hint">Creado el</span><strong>${formatearFecha(ticket.fecha_creacion)}</strong></div>
                                ${ticket.fecha_resolucion ? `<div><span class="panel__hint">Resuelto el</span><strong>${formatearFecha(ticket.fecha_resolucion)}</strong></div>` : ''}
                                ${ticket.resolucion ? `<div><span class="panel__hint">Resolución</span><strong>${ticket.resolucion}</strong></div>` : ''}
                            </div>
                        </section>
                    </div>
                    ${history.length ? `
                        <section class="person-card" style="text-align:left; gap:0.45rem;">
                            <h5>Historial</h5>
                            <div class="detail-grid" style="display:grid; gap:0.6rem;">
                                ${history.map(item => `<div><span class="panel__hint">${item.label}</span><strong>${item.value}</strong></div>`).join('')}
                            </div>
                        </section>
                    ` : ''}
                </div>
            `;

            let footerButtons = `<button type="button" class="btn btn-ghost" data-modal-close>Cerrar</button>`;

            if (usuarioActual.rol === 'it') {
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
            } else if (usuarioActual.rol === 'solicitante' && ticket.solicitante_id == usuarioActual.id && ticket.estado === 'resuelto') {
                footerButtons = `
                    <button type="button" class="btn btn-primary" onclick="cerrarTicket(${ticket.id}, 'satisfactoria')"><i class="fas fa-thumbs-up"></i>Satisfactoria</button>
                    <button type="button" class="btn btn-ghost" onclick="cerrarTicket(${ticket.id}, 'insatisfactoria')"><i class="fas fa-thumbs-down"></i>Insatisfactoria</button>
                    <button type="button" class="btn btn-ghost" data-modal-close>Cerrar</button>
                `;
            }

            footer.innerHTML = footerButtons;
            new bootstrap.Modal(modalElement).show();
        }

        async function tomarTicket(ticketId) {
            try {
                const response = await fetch(`api/tickets.php?action=tomar&id=${ticketId}`, { method: 'POST' });
                const data = await response.json();

                if (data.success) {
                    cargarDashboard();
                    bootstrap.Modal.getInstance(document.getElementById('modalVerTicket')).hide();
                    mostrarMensaje('Ticket tomado exitosamente', 'success');
                } else {
                    mostrarMensaje(data.message || 'No fue posible tomar el ticket', 'danger');
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
                const response = await fetch(`api/tickets.php?action=resolver&id=${ticketSeleccionado}`, {
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

        async function cerrarTicket(ticketId, satisfaccion) {
            try {
                const response = await fetch(`api/tickets.php?action=cerrar&id=${ticketId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ satisfaccion, comentarios: '' })
                });

                const data = await response.json();

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalVerTicket')).hide();
                    cargarDashboard();
                    mostrarMensaje('Ticket cerrado correctamente', 'success');
                } else {
                    mostrarMensaje(data.message || 'No fue posible cerrar el ticket', 'danger');
                }
            } catch (error) {
                mostrarMensaje('Error de conexión', 'danger');
            }
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
                    mostrarMensaje('Selecciona ambas fechas para generar el reporte', 'warning');
                    return;
                }

                url += `&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
            }

            const link = document.createElement('a');
            link.href = url;
            link.download = '';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            mostrarMensaje('Preparando reporte, la descarga comenzará en breve', 'success');
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
    </script>
</body>
</html>
