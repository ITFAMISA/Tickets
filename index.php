<?php
// index.php - Página principal sin login
require_once 'config/database.php';
iniciarSesion();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Soporte IT</title>
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
            --color-surface-alt: #f0f0f2;
            --color-text: #111111;
            --color-muted: rgba(0, 0, 0, 0.6);
            --color-border: rgba(211, 47, 47, 0.25);
            --color-border-strong: rgba(211, 47, 47, 0.35);
            --color-border-soft: rgba(0, 0, 0, 0.08);
            --shadow-soft: 0 32px 80px rgba(0, 0, 0, 0.12);
            --radius-sm: 10px;
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
            z-index: 40;
            padding: 1.5rem 5vw;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.18);
        }

        .app-header__brand {
            display: flex;
            align-items: center;
            gap: 1.1rem;
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
            letter-spacing: 0.4px;
        }

        .app-header__subtitle {
            font-size: 0.9rem;
            opacity: 0.85;
        }

        .app-main {
            padding: 3rem 0 4rem;
        }

        .content-shell {
            width: min(1180px, 92vw);
            margin: 0 auto;
            display: grid;
            gap: 2.5rem;
        }

        .panel {
            background: var(--color-white);
            border-radius: var(--radius-lg);
            border: 1.5px solid var(--color-border);
            box-shadow: var(--shadow-soft);
            padding: 2.5rem;
            display: grid;
            gap: 1.75rem;
        }

        .panel--center {
            max-width: 520px;
            margin: 0 auto;
        }

        .panel__header {
            display: grid;
            gap: 0.6rem;
        }

        .panel__header h2,
        .panel__header h3,
        .panel__header h4 {
            font-weight: 600;
            font-size: 1.4rem;
        }

        .panel__header--inline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
        }

        .panel__hint {
            font-size: 0.85rem;
            color: var(--color-muted);
        }

        .panel__body {
            display: grid;
            gap: 1.5rem;
        }

        .form-grid {
            display: grid;
            gap: 1.25rem;
        }

        .form-grid--two {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.5rem;
        }

        .form-field {
            display: grid;
            gap: 0.5rem;
        }

        .form-field label {
            font-size: 0.9rem;
            font-weight: 500;
        }

        input[type="text"],
        input[type="email"],
        input[type="file"],
        select,
        textarea {
            width: 100%;
            background: var(--color-white);
            border: 1.5px solid var(--color-border-soft);
            border-radius: var(--radius-md);
            padding: 0.85rem 1rem;
            font-family: inherit;
            font-size: 0.95rem;
            color: var(--color-text);
            transition: var(--transition);
        }

        textarea {
            min-height: 160px;
            resize: vertical;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--color-red);
            box-shadow: 0 0 0 4px rgba(211, 47, 47, 0.15);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            border-radius: var(--radius-md);
            border: none;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.85rem 1.35rem;
            cursor: pointer;
            transition: var(--transition);
            background: transparent;
            color: var(--color-text);
        }

        .btn i {
            font-size: 1rem;
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

        .btn-outline {
            background: var(--color-white);
            border: 1.5px solid var(--color-red);
            color: var(--color-red);
        }

        .btn-outline:hover,
        .btn-outline:focus {
            background: rgba(211, 47, 47, 0.12);
        }

        .btn-ghost {
            background: transparent;
            border: 1.5px solid rgba(0, 0, 0, 0.08);
            color: var(--color-text);
        }

        .btn-ghost:hover,
        .btn-ghost:focus {
            border-color: rgba(211, 47, 47, 0.35);
            color: var(--color-red);
        }

        .btn-chip {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 999px;
            padding-inline: 1.25rem;
            font-size: 0.85rem;
        }

        .btn-chip.is-active {
            background: var(--color-red);
            color: var(--color-white);
        }

        .btn-sm {
            padding: 0.6rem 1rem;
            font-size: 0.85rem;
            border-radius: 999px;
        }

        .cta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.35rem 0.9rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid transparent;
            letter-spacing: 0.4px;
        }

        .badge-soft {
            background: rgba(0, 0, 0, 0.06);
            border-color: rgba(0, 0, 0, 0.08);
            color: var(--color-text);
        }

        .bg-primary {
            background: var(--color-red);
            color: var(--color-white);
            border-color: transparent;
        }

        .bg-danger {
            background: rgba(211, 47, 47, 0.18);
            color: var(--color-red);
            border-color: rgba(211, 47, 47, 0.28);
        }

        .bg-warning {
            background: rgba(0, 0, 0, 0.08);
            color: var(--color-text);
            border-color: rgba(0, 0, 0, 0.12);
        }

        .bg-success {
            background: rgba(211, 47, 47, 0.12);
            color: var(--color-red);
            border-color: rgba(211, 47, 47, 0.22);
        }

        .bg-info {
            background: rgba(0, 0, 0, 0.05);
            color: var(--color-text);
            border-color: rgba(0, 0, 0, 0.1);
        }

        .bg-secondary {
            background: rgba(0, 0, 0, 0.05);
            color: var(--color-text);
            border-color: rgba(0, 0, 0, 0.08);
        }

        .tab-switcher {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }

        .tab-button {
            background: var(--color-white);
            border: 1.5px solid rgba(0, 0, 0, 0.08);
            border-radius: var(--radius-md);
            padding: 1.35rem;
            display: grid;
            gap: 0.35rem;
            text-align: left;
            font-weight: 600;
            transition: var(--transition);
        }

        .tab-button i {
            font-size: 1.4rem;
            color: var(--color-red);
        }

        .tab-button:hover {
            border-color: var(--color-red);
        }

        .tab-button.is-active {
            border-color: var(--color-red);
            color: var(--color-red);
            box-shadow: 0 0 0 4px rgba(211, 47, 47, 0.15);
        }

        .tab-panel {
            display: none;
            animation: fadeIn 0.45s ease;
        }

        .tab-panel.is-active {
            display: block;
        }

        .ticket-list {
            display: grid;
            gap: 1.25rem;
        }

        .ticket-card {
            background: var(--color-white);
            border: 1.5px solid rgba(0, 0, 0, 0.08);
            border-left: 5px solid rgba(211, 47, 47, 0.4);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            display: grid;
            gap: 1rem;
            transition: var(--transition);
        }

        .ticket-card:hover {
            transform: translateY(-4px);
            border-color: var(--color-border-strong);
        }

        .ticket-card__header {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: space-between;
            align-items: flex-start;
        }

        .ticket-card__title {
            font-size: 1rem;
            font-weight: 600;
        }

        .ticket-card__description {
            color: var(--color-muted);
            font-size: 0.9rem;
            margin-top: 0.35rem;
        }

        .ticket-card__meta {
            display: grid;
            gap: 0.65rem;
            text-align: right;
        }

        .ticket-card__date {
            font-size: 0.85rem;
            color: var(--color-muted);
        }

        .ticket-card__tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .ticket-priority-urgente {
            border-left-color: var(--color-red);
        }

        .ticket-priority-alta {
            border-left-color: rgba(211, 47, 47, 0.65);
        }

        .ticket-priority-media {
            border-left-color: rgba(0, 0, 0, 0.25);
        }

        .ticket-priority-baja {
            border-left-color: rgba(0, 0, 0, 0.15);
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.2rem;
        }

        .person-card {
            border: 1.5px solid rgba(0, 0, 0, 0.08);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            background: var(--color-white);
            display: grid;
            gap: 0.65rem;
            text-align: center;
            transition: var(--transition);
        }

        .person-card:hover {
            border-color: var(--color-border);
        }

        .person-card__icon {
            font-size: 2.75rem;
            color: var(--color-red);
        }

        .person-card__note {
            font-size: 0.85rem;
            color: var(--color-muted);
        }

        .welcome-banner {
            background: var(--color-red);
            color: var(--color-white);
            border-radius: var(--radius-lg);
            padding: 2rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
        }

        .welcome-banner__info {
            display: grid;
            gap: 0.4rem;
        }

        .welcome-banner__info h2 {
            font-size: 2rem;
            font-weight: 600;
        }

        .info-banner {
            border: 1.5px solid rgba(211, 47, 47, 0.35);
            border-left: 6px solid var(--color-red);
            background: rgba(211, 47, 47, 0.08);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .info-banner__icon {
            font-size: 1.8rem;
            color: var(--color-red);
        }

        .info-banner__content {
            display: grid;
            gap: 0.35rem;
        }

        .info-banner__content h4 {
            font-size: 1rem;
            font-weight: 600;
        }

        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            z-index: 60;
        }

        .modal--visible {
            display: flex;
        }

        .modal__dialog {
            width: min(780px, 95vw);
            background: var(--color-white);
            border-radius: var(--radius-lg);
            border: 1.5px solid var(--color-border);
            box-shadow: var(--shadow-soft);
            display: flex;
            flex-direction: column;
            max-height: 90vh;
        }

        .modal__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            background: rgba(211, 47, 47, 0.08);
        }

        .modal__header h5 {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .modal__body {
            padding: 2rem;
            overflow-y: auto;
            display: grid;
            gap: 1.5rem;
        }

        .modal__footer {
            padding: 1.25rem 2rem;
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .detail-layout {
            display: grid;
            gap: 1.5rem;
        }

        .detail-columns {
            display: grid;
            gap: 1.25rem;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        }

        .detail-block {
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: var(--radius-md);
            padding: 1.25rem;
            display: grid;
            gap: 1rem;
        }

        .detail-title {
            font-size: 0.95rem;
            font-weight: 600;
        }

        .detail-grid {
            display: grid;
            gap: 0.85rem;
        }

        .detail-row {
            display: grid;
            gap: 0.35rem;
        }

        .detail-row span {
            font-size: 0.85rem;
            color: var(--color-muted);
        }

        .detail-row strong {
            font-weight: 600;
            color: var(--color-text);
        }

        .attachment-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
        }

        .timeline {
            display: grid;
            gap: 0.85rem;
        }

        .timeline-item {
            display: grid;
            gap: 0.25rem;
            border-left: 3px solid rgba(211, 47, 47, 0.25);
            padding-left: 1rem;
        }

        .timeline-item h6 {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .timeline-item span {
            font-size: 0.85rem;
            color: var(--color-muted);
        }

        .btn-close {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: none;
            background: rgba(0, 0, 0, 0.12);
            color: var(--color-text);
            cursor: pointer;
            display: grid;
            place-items: center;
            transition: var(--transition);
        }

        .btn-close:hover {
            background: rgba(211, 47, 47, 0.35);
            color: var(--color-white);
        }

        .toast-stack {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            display: grid;
            gap: 0.75rem;
            z-index: 70;
            pointer-events: none;
        }

        .toast {
            min-width: 260px;
            max-width: 320px;
            background: var(--color-white);
            border: 1.5px solid rgba(0, 0, 0, 0.08);
            border-left: 5px solid var(--color-red);
            border-radius: var(--radius-md);
            padding: 0.95rem 1.2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            box-shadow: var(--shadow-soft);
            animation: slideDown 0.45s ease;
            pointer-events: auto;
        }

        .toast__message {
            font-size: 0.9rem;
        }

        .toast--success {
            border-left-color: rgba(0, 0, 0, 0.45);
        }

        .toast--warning {
            border-left-color: rgba(211, 47, 47, 0.35);
        }

        .toast--danger {
            border-left-color: var(--color-red);
        }

        .toast__close {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: none;
            background: rgba(0, 0, 0, 0.08);
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
            padding: 2rem 1rem;
            color: var(--color-muted);
            display: grid;
            gap: 0.5rem;
        }

        .is-hidden {
            display: none !important;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 920px) {
            .panel {
                padding: 2rem;
            }

            .form-grid--two {
                grid-template-columns: 1fr;
            }

            .panel__header--inline {
                flex-direction: column;
                align-items: flex-start;
            }

            .welcome-banner__info h2 {
                font-size: 1.6rem;
            }

            .tab-switcher {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .app-header {
                padding: 1.15rem 1.5rem;
            }

            .brand-icon {
                width: 48px;
                height: 48px;
                font-size: 1.3rem;
            }

            .panel {
                padding: 1.75rem;
            }

            .modal__dialog {
                padding: 0;
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
                <div class="app-header__subtitle">Gestión empresarial de incidencias</div>
            </div>
        </div>
        <div class="app-header__subtitle">Soporte eficiente, comunicación clara y seguimiento continuo</div>
    </header>

    <main class="app-main">
        <div class="content-shell">
            <section id="identificacionSection" class="panel panel--center">
                <div class="panel__header">
                    <h2>Identifícate para continuar</h2>
                    <p class="panel__hint">Ingresa tu número de ficha y nombre completo para consultar o crear tickets.</p>
                </div>

                <form id="formIdentificacion" class="form-grid" onsubmit="event.preventDefault(); accederSistema();">
                    <div class="form-field">
                        <label for="numeroFicha">Número de ficha</label>
                        <input type="text" id="numeroFicha" placeholder="Ejemplo: 12345" required>
                    </div>
                    <div class="form-field">
                        <label for="nombreCompleto">Nombre completo</label>
                        <input type="text" id="nombreCompleto" placeholder="Nombre y apellido" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i>
                        Acceder al sistema
                    </button>
                </form>

                <div class="cta-grid">
                    <button type="button" class="btn btn-outline" onclick="mostrarFormularioTicket()">
                        <i class="fas fa-plus"></i>
                        Crear nuevo ticket
                    </button>
                    <a href="admin.php" class="btn btn-ghost">
                        <i class="fas fa-cog"></i>
                        Panel administrativo
                    </a>
                </div>
            </section>

            <section id="alertSection" class="info-banner">
                <div class="info-banner__icon">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div class="info-banner__content">
                    <h4>Importante</h4>
                    <p>Utiliza exactamente la misma combinación de nombre y apellido con la que registraste tus tickets para visualizarlos correctamente.</p>
                </div>
                <button type="button" class="btn-close" aria-label="Cerrar aviso" onclick="document.getElementById('alertSection').classList.add('is-hidden');">
                    <i class="fas fa-xmark"></i>
                </button>
            </section>

            <section id="contenidoDinamico" class="dashboard-shell is-hidden">
                <article class="welcome-banner">
                    <div class="welcome-banner__info">
                        <span>Bienvenido</span>
                        <h2 id="usuarioNombre">Usuario</h2>
                    </div>
                    <button class="btn btn-ghost" type="button" onclick="window.location.href='index.php'">
                        <i class="fas fa-sign-out-alt"></i>
                        Cerrar sesión
                    </button>
                </article>

                <section class="panel">
                    <div class="panel__header panel__header--inline">
                        <h3><i class="fas fa-users"></i> Estado del personal IT</h3>
                        <span class="panel__hint">Actualización automática cada 30 segundos</span>
                    </div>
                    <div class="panel__body">
                        <div id="personalIT" class="status-grid"></div>
                    </div>
                </section>

                <div class="tab-switcher">
                    <button class="tab-button is-active" data-tab="tickets" onclick="mostrarTab('tickets', this)">
                        <i class="fas fa-layer-group"></i>
                        <span>Mis tickets</span>
                    </button>
                    <button class="tab-button" data-tab="nuevo" onclick="mostrarTab('nuevo', this)">
                        <i class="fas fa-plus-circle"></i>
                        <span>Nuevo ticket</span>
                    </button>
                </div>

                <section id="tickets" class="panel tab-panel is-active">
                    <div class="panel__header panel__header--inline">
                        <h3><i class="fas fa-clipboard-list"></i> Mis tickets</h3>
                        <div class="filter-group" style="display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
                            <button class="btn btn-chip is-active" onclick="filtrarTicketsPorFecha('todos', event)">Todos</button>
                            <button class="btn btn-chip" onclick="filtrarTicketsPorFecha('mes_actual', event)">Este mes</button>
                            <span class="badge bg-primary" id="totalTickets">0</span>
                        </div>
                    </div>
                    <div class="panel__body">
                        <div id="listaTickets" class="ticket-list"></div>
                    </div>
                </section>

                <section id="nuevo" class="panel tab-panel">
                    <div class="panel__header">
                        <h3><i class="fas fa-ticket"></i> Crear nuevo ticket</h3>
                        <p class="panel__hint">Describe el incidente con la mayor precisión posible y adjunta archivos si es necesario.</p>
                    </div>
                    <div class="panel__body">
                        <form id="formNuevoTicket" class="form-grid form-grid--two">
                            <div class="form-field">
                                <label for="titulo">Título del problema *</label>
                                <input type="text" id="titulo" placeholder="Describe brevemente el problema" required>
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
                            <div class="form-field" style="grid-column: 1 / -1;">
                                <label for="categoria">Categoría</label>
                                <select id="categoria">
                                    <option value="Hardware">Hardware</option>
                                    <option value="Software">Software</option>
                                    <option value="Red">Red / Conectividad</option>
                                    <option value="Email">Email</option>
                                    <option value="Impresoras">Impresoras</option>
                                    <option value="Accesos">Accesos / Permisos</option>
                                    <option value="Desarrollo">Desarrollo</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div class="form-field" style="grid-column: 1 / -1;">
                                <label for="descripcion">Descripción detallada *</label>
                                <textarea id="descripcion" placeholder="Incluye pasos, mensajes de error y cualquier dato relevante" required></textarea>
                            </div>
                            <div class="form-field" style="grid-column: 1 / -1;">
                                <label for="archivos">Archivos adjuntos</label>
                                <input type="file" id="archivos" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt">
                                <span class="panel__hint">Máximo 5MB por archivo · Formatos permitidos: JPG, PNG, PDF, DOC, DOCX, TXT</span>
                            </div>
                            <div class="cta-grid" style="grid-column: 1 / -1;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check"></i>
                                    Registrar ticket
                                </button>
                                <button type="button" class="btn btn-ghost" onclick="mostrarTab('tickets')">
                                    <i class="fas fa-arrow-left"></i>
                                    Volver a mis tickets
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
            </section>
        </div>
    </main>

    <div class="modal" id="modalVerTicket" aria-hidden="true">
        <div class="modal__dialog">
            <header class="modal__header">
                <h5 id="modalTicketTitle"></h5>
                <button type="button" class="btn-close" data-modal-close aria-label="Cerrar">
                    <i class="fas fa-xmark"></i>
                </button>
            </header>
            <div class="modal__body" id="modalTicketBody"></div>
            <footer class="modal__footer" id="modalTicketFooter"></footer>
        </div>
    </div>

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
        const sections = {
            identificacion: document.getElementById('identificacionSection'),
            alerta: document.getElementById('alertSection'),
            dashboard: document.getElementById('contenidoDinamico')
        };
        const usuarioNombre = document.getElementById('usuarioNombre');
        const personalITContainer = document.getElementById('personalIT');
        const listaTickets = document.getElementById('listaTickets');
        const totalTickets = document.getElementById('totalTickets');
        const formNuevoTicket = document.getElementById('formNuevoTicket');
        const numeroFichaInput = document.getElementById('numeroFicha');
        const nombreCompletoInput = document.getElementById('nombreCompleto');

        let usuarioActual = null;
        let todosLosTickets = [];

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

        function activarDashboard() {
            sections.dashboard.classList.remove('is-hidden');
            sections.identificacion.classList.add('is-hidden');
            sections.alerta.classList.add('is-hidden');
        }

        async function verificarNombresSimilares(numeroFicha, nombreCompleto) {
            try {
                const response = await fetch(`api/tickets_publico.php?action=verificar-nombre&numero_ficha=${numeroFicha}&nombre=${encodeURIComponent(nombreCompleto)}`);
                const data = await response.json();

                if (data.success && data.sugerencia) {
                    return data.sugerencia;
                }
                return null;
            } catch (error) {
                console.error('Error verificando nombres:', error);
                return null;
            }
        }

        async function accederSistema() {
            const numeroFicha = numeroFichaInput.value.trim();
            let nombreCompleto = nombreCompletoInput.value.trim();

            if (!numeroFicha || !nombreCompleto) {
                mostrarMensaje('Por favor completa todos los campos requeridos', 'warning');
                return;
            }

            const sugerencia = await verificarNombresSimilares(numeroFicha, nombreCompleto);
            if (sugerencia && sugerencia !== nombreCompleto) {
                if (confirm(`¿Quizás quisiste decir "${sugerencia}"?`)) {
                    nombreCompleto = sugerencia;
                    nombreCompletoInput.value = sugerencia;
                }
            }

            usuarioActual = {
                numeroFicha: numeroFicha,
                nombre: nombreCompleto
            };

            usuarioNombre.innerHTML = `<strong>${nombreCompleto}</strong> · Ficha ${numeroFicha}`;
            activarDashboard();
            mostrarTab('tickets');
            cargarTicketsUsuario();
            cargarPersonalIT();
        }

        function mostrarTab(tabName, trigger) {
            const panel = document.getElementById(tabName);
            const button = trigger || document.querySelector(`[data-tab="${tabName}"]`);

            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('is-active'));
            document.querySelectorAll('.tab-panel').forEach(section => section.classList.remove('is-active'));

            if (panel) {
                panel.classList.add('is-active');
            }
            if (button) {
                button.classList.add('is-active');
            }
        }

        function mostrarFormularioTicket() {
            const numeroFicha = numeroFichaInput.value.trim();
            const nombreCompleto = nombreCompletoInput.value.trim();

            if (!numeroFicha || !nombreCompleto) {
                mostrarMensaje('Ingresa tu número de ficha y nombre antes de crear un ticket', 'warning');
                return;
            }

            usuarioActual = {
                numeroFicha: numeroFicha,
                nombre: nombreCompleto
            };

            usuarioNombre.innerHTML = `<strong>${nombreCompleto}</strong> · Ficha ${numeroFicha}`;
            activarDashboard();
            mostrarTab('nuevo');
            cargarPersonalIT();
        }

        async function cargarPersonalIT() {
            try {
                const response = await fetch('api/tickets_publico.php?action=personal-it');
                const data = await response.json();

                if (data.success) {
                    mostrarPersonalIT(data.data);
                }
            } catch (error) {
                console.error('Error al cargar personal IT:', error);
            }
        }

        function mostrarPersonalIT(personal) {
            if (!personal || personal.length === 0) {
                personalITContainer.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-user-slash" style="font-size:2rem;"></i>
                        <p>No hay personal registrado en este momento.</p>
                    </div>
                `;
                return;
            }

            personalITContainer.innerHTML = personal.map(persona => `
                <article class="person-card">
                    <div class="person-card__icon"><i class="fas fa-user-circle"></i></div>
                    <h6>${persona.nombre}</h6>
                    <span class="badge bg-${obtenerColorEstadoIT(persona.estado)}">
                        ${persona.estado.charAt(0).toUpperCase() + persona.estado.slice(1)}
                    </span>
                    ${persona.tickets_activos > 0 ? `<span class="person-card__note">${persona.tickets_activos} ticket(s) activo(s)</span>` : ''}
                </article>
            `).join('');
        }

        function obtenerColorEstadoIT(estado) {
            switch(estado) {
                case 'disponible':
                    return 'success';
                case 'ocupado':
                    return 'warning';
                case 'ausente':
                    return 'danger';
                default:
                    return 'secondary';
            }
        }

        async function cargarTicketsUsuario() {
            if (!usuarioActual) return;

            try {
                const response = await fetch(`api/tickets_publico.php?action=mis-tickets&numero_ficha=${usuarioActual.numeroFicha}&nombre=${encodeURIComponent(usuarioActual.nombre)}`);
                const data = await response.json();

                if (data.success) {
                    mostrarTickets(data.data);
                } else {
                    mostrarMensaje(data.message || 'No fue posible cargar los tickets', 'danger');
                }
            } catch (error) {
                mostrarMensaje('Error de conexión al cargar los tickets', 'danger');
            }
        }

        function mostrarTickets(tickets) {
            todosLosTickets = tickets || [];
            totalTickets.textContent = todosLosTickets.length;

            if (!todosLosTickets.length) {
                listaTickets.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-inbox" style="font-size:2.2rem;"></i>
                        <p>No tienes tickets registrados todavía.</p>
                    </div>
                `;
                return;
            }

            listaTickets.innerHTML = todosLosTickets.map(ticket => `
                <article class="ticket-card ticket-priority-${ticket.prioridad}" data-fecha="${ticket.fecha_creacion}">
                    <div class="ticket-card__header">
                        <div>
                            <h6 class="ticket-card__title">#${ticket.id} · ${ticket.titulo}</h6>
                            <p class="ticket-card__description">${ticket.descripcion.substring(0, 120)}...</p>
                        </div>
                        <div class="ticket-card__meta">
                            <time class="ticket-card__date">${formatearFecha(ticket.fecha_creacion)}</time>
                            <button class="btn btn-outline btn-sm" onclick="verTicket(${ticket.id})">
                                <i class="fas fa-eye"></i>
                                Ver detalle
                            </button>
                        </div>
                    </div>
                    <div class="ticket-card__tags">
                        <span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)}">${ticket.prioridad.toUpperCase()}</span>
                        <span class="badge bg-${obtenerColorEstado(ticket.estado)}">${ticket.estado.replace('_', ' ').toUpperCase()}</span>
                        <span class="badge badge-soft">${ticket.categoria}</span>
                    </div>
                </article>
            `).join('');
        }

        function filtrarTicketsPorFecha(filtro, event) {
            if (event) {
                const buttons = event.currentTarget.parentElement.querySelectorAll('.btn-chip');
                buttons.forEach(btn => btn.classList.remove('is-active'));
                event.currentTarget.classList.add('is-active');
            }

            if (!todosLosTickets.length) {
                return;
            }

            const ahora = new Date();
            const mesActual = ahora.getMonth();
            const añoActual = ahora.getFullYear();

            let ticketsFiltrados = todosLosTickets;

            if (filtro === 'mes_actual') {
                ticketsFiltrados = todosLosTickets.filter(ticket => {
                    const fechaTicket = new Date(ticket.fecha_creacion);
                    return fechaTicket.getMonth() === mesActual && fechaTicket.getFullYear() === añoActual;
                });
            }

            totalTickets.textContent = ticketsFiltrados.length;

            if (!ticketsFiltrados.length) {
                listaTickets.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-inbox" style="font-size:2.2rem;"></i>
                        <p>No hay tickets registrados en el periodo seleccionado.</p>
                    </div>
                `;
                return;
            }

            listaTickets.innerHTML = ticketsFiltrados.map(ticket => `
                <article class="ticket-card ticket-priority-${ticket.prioridad}" data-fecha="${ticket.fecha_creacion}">
                    <div class="ticket-card__header">
                        <div>
                            <h6 class="ticket-card__title">#${ticket.id} · ${ticket.titulo}</h6>
                            <p class="ticket-card__description">${ticket.descripcion.substring(0, 120)}...</p>
                        </div>
                        <div class="ticket-card__meta">
                            <time class="ticket-card__date">${formatearFecha(ticket.fecha_creacion)}</time>
                            <button class="btn btn-outline btn-sm" onclick="verTicket(${ticket.id})">
                                <i class="fas fa-eye"></i>
                                Ver detalle
                            </button>
                        </div>
                    </div>
                    <div class="ticket-card__tags">
                        <span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)}">${ticket.prioridad.toUpperCase()}</span>
                        <span class="badge bg-${obtenerColorEstado(ticket.estado)}">${ticket.estado.replace('_', ' ').toUpperCase()}</span>
                        <span class="badge badge-soft">${ticket.categoria}</span>
                    </div>
                </article>
            `).join('');
        }

        formNuevoTicket.addEventListener('submit', async function(event) {
            event.preventDefault();

            if (!usuarioActual) {
                mostrarMensaje('Error: no se encontraron datos del usuario activo', 'danger');
                return;
            }

            const formData = new FormData();
            formData.append('numero_ficha', usuarioActual.numeroFicha);
            formData.append('nombre', usuarioActual.nombre);
            formData.append('titulo', document.getElementById('titulo').value);
            formData.append('descripcion', document.getElementById('descripcion').value);
            formData.append('prioridad', document.getElementById('prioridad').value);
            formData.append('categoria', document.getElementById('categoria').value);

            const archivos = document.getElementById('archivos').files;
            for (let i = 0; i < archivos.length; i++) {
                formData.append('archivos[]', archivos[i]);
            }

            try {
                const response = await fetch('api/tickets_publico.php?action=crear', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    mostrarMensaje('Ticket creado exitosamente', 'success');

                    const titulo = document.getElementById('titulo').value;
                    const categoria = document.getElementById('categoria').value;
                    const prioridad = document.getElementById('prioridad').value;
                    const notificationTitle = `Nuevo ticket · ${prioridad.toUpperCase()}`;
                    const notificationBody = `${categoria} · ${usuarioActual.nombre} · ${titulo}`;
                    sendTicketNotification(notificationTitle, notificationBody, {
                        ticket_id: data.ticket_id,
                        action: 'new_ticket'
                    });

                    formNuevoTicket.reset();
                    mostrarTab('tickets');
                    cargarTicketsUsuario();
                } else {
                    mostrarMensaje(data.message || 'Error al crear el ticket', 'danger');
                    console.error('Detalle del error:', data);
                }
            } catch (error) {
                mostrarMensaje('Error de conexión', 'danger');
                console.error('Error:', error);
            }
        });

        async function verTicket(ticketId) {
            try {
                const response = await fetch(`api/tickets_publico.php?action=detalle&id=${ticketId}`);
                const data = await response.json();

                if (data.success) {
                    mostrarDetalleTicket(data.data);
                } else {
                    mostrarMensaje(data.message || 'No fue posible cargar el ticket', 'danger');
                }
            } catch (error) {
                mostrarMensaje('Error de conexión', 'danger');
            }
        }

        function mostrarDetalleTicket(ticket) {
            const modalElement = document.getElementById('modalVerTicket');
            const title = document.getElementById('modalTicketTitle');
            const body = document.getElementById('modalTicketBody');
            const footer = document.getElementById('modalTicketFooter');

            title.textContent = `Ticket #${ticket.id} · ${ticket.titulo}`;

            const history = [];
            history.push({ label: 'Ticket creado', value: formatearFecha(ticket.fecha_creacion) });
            if (ticket.asignado_nombre) {
                history.push({ label: 'Asignado', value: `Atiende: ${ticket.asignado_nombre}` });
            }
            if (ticket.fecha_resolucion) {
                history.push({ label: 'Resuelto', value: formatearFecha(ticket.fecha_resolucion) });
            }
            if (ticket.estado === 'cerrado' && ticket.satisfaccion) {
                history.push({ label: 'Cierre', value: `Satisfacción: ${ticket.satisfaccion.toUpperCase()}` });
            }

            const archivosHtml = ticket.archivos && ticket.archivos.length ? `
                <section class="detail-block">
                    <h6 class="detail-title">Archivos adjuntos</h6>
                    <div class="attachment-group">
                        ${ticket.archivos.map(archivo => `
                            <a class="btn btn-outline btn-sm" href="uploads/${archivo.ruta_archivo}" target="_blank">
                                <i class="fas fa-paperclip"></i>
                                ${archivo.nombre_archivo}
                            </a>
                        `).join('')}
                    </div>
                </section>
            ` : '';

            body.innerHTML = `
                <div class="detail-layout">
                    <div class="detail-columns">
                        <section class="detail-block">
                            <h6 class="detail-title">Resumen del ticket</h6>
                            <div class="detail-grid">
                                <div class="detail-row">
                                    <span>Título</span>
                                    <strong>${ticket.titulo}</strong>
                                </div>
                                <div class="detail-row">
                                    <span>Descripción</span>
                                    <strong>${ticket.descripcion}</strong>
                                </div>
                                <div class="detail-row">
                                    <span>Categoría</span>
                                    <strong>${ticket.categoria}</strong>
                                </div>
                                <div class="detail-row">
                                    <span>Prioridad</span>
                                    <strong><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)}">${ticket.prioridad.toUpperCase()}</span></strong>
                                </div>
                                <div class="detail-row">
                                    <span>Estado</span>
                                    <strong><span class="badge bg-${obtenerColorEstado(ticket.estado)}">${ticket.estado.replace('_', ' ').toUpperCase()}</span></strong>
                                </div>
                            </div>
                        </section>
                        <section class="detail-block">
                            <h6 class="detail-title">Seguimiento</h6>
                            <div class="detail-grid">
                                <div class="detail-row">
                                    <span>Solicitante</span>
                                    <strong>${ticket.solicitante_nombre}</strong>
                                </div>
                                <div class="detail-row">
                                    <span>Número de ficha</span>
                                    <strong>${ticket.numero_ficha}</strong>
                                </div>
                                <div class="detail-row">
                                    <span>Asignado a</span>
                                    <strong>${ticket.asignado_nombre || 'Sin asignar'}</strong>
                                </div>
                                <div class="detail-row">
                                    <span>Fecha de creación</span>
                                    <strong>${formatearFecha(ticket.fecha_creacion)}</strong>
                                </div>
                                ${ticket.fecha_resolucion ? `<div class="detail-row"><span>Fecha de resolución</span><strong>${formatearFecha(ticket.fecha_resolucion)}</strong></div>` : ''}
                                ${ticket.resolucion ? `<div class="detail-row"><span>Resolución</span><strong>${ticket.resolucion}</strong></div>` : ''}
                            </div>
                        </section>
                    </div>
                    ${archivosHtml}
                    <section class="detail-block">
                        <h6 class="detail-title">Historial</h6>
                        <div class="timeline">
                            ${history.map(item => `
                                <div class="timeline-item">
                                    <h6>${item.label}</h6>
                                    <span>${item.value}</span>
                                </div>
                            `).join('')}
                        </div>
                    </section>
                </div>
            `;

            let footerButtons = `
                <button type="button" class="btn btn-ghost" data-modal-close>Cerrar</button>
            `;

            if (ticket.estado === 'resuelto') {
                footerButtons = `
                    <button type="button" class="btn btn-primary" onclick="cerrarTicket(${ticket.id}, 'satisfactoria')">
                        <i class="fas fa-thumbs-up"></i>
                        Satisfactoria
                    </button>
                    <button type="button" class="btn btn-outline" onclick="cerrarTicket(${ticket.id}, 'insatisfactoria')">
                        <i class="fas fa-thumbs-down"></i>
                        Insatisfactoria
                    </button>
                    <button type="button" class="btn btn-ghost" data-modal-close>Cerrar</button>
                `;
            }

            footer.innerHTML = footerButtons;
            new bootstrap.Modal(modalElement).show();
        }

        async function cerrarTicket(ticketId, satisfaccion) {
            try {
                const response = await fetch(`api/tickets_publico.php?action=cerrar&id=${ticketId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ satisfaccion })
                });

                const data = await response.json();

                if (data.success) {
                    mostrarMensaje('Ticket cerrado correctamente', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('modalVerTicket')).hide();
                    cargarTicketsUsuario();
                } else {
                    mostrarMensaje(data.message || 'No fue posible cerrar el ticket', 'danger');
                }
            } catch (error) {
                mostrarMensaje('Error de conexión', 'danger');
            }
        }

        function formatearFecha(fecha) {
            if (!fecha) return '-';
            return new Date(fecha).toLocaleString('es-ES');
        }

        function obtenerColorPrioridad(prioridad) {
            switch(prioridad) {
                case 'urgente':
                    return 'danger';
                case 'alta':
                    return 'warning';
                case 'media':
                    return 'info';
                case 'baja':
                    return 'secondary';
                default:
                    return 'secondary';
            }
        }

        function obtenerColorEstado(estado) {
            switch(estado) {
                case 'abierto':
                    return 'primary';
                case 'en_proceso':
                    return 'warning';
                case 'resuelto':
                    return 'success';
                case 'cerrado':
                    return 'secondary';
                default:
                    return 'secondary';
            }
        }

        async function testNotification() {
            try {
                mostrarMensaje('Enviando notificación de prueba...', 'info');

                const response = await fetch('http://192.168.1.134:5214/web/Ticket/api.php/api/test_notification', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        title: 'Test desde Web',
                        body: 'Notificación de prueba - ' + new Date().toLocaleTimeString()
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    mostrarMensaje('Notificación enviada correctamente', 'success');
                    console.log('Respuesta notificación:', data);
                } else {
                    mostrarMensaje('Error enviando notificación: ' + (data.error || 'Desconocido'), 'danger');
                }
            } catch (error) {
                mostrarMensaje('Error de conexión: ' + error.message, 'danger');
            }
        }

        async function sendTicketNotification(title, body, data = {}) {
            try {
                const response = await fetch('http://192.168.1.134:5214/web/Ticket/api.php/api/test_notification', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ title, body })
                });

                const result = await response.json();

                if (response.ok) {
                    console.log('Notificación enviada', result);
                } else {
                    console.error('Error enviando notificación', result);
                }

                return response.ok;
            } catch (error) {
                console.error('Error enviando notificación', error);
                return false;
            }
        }

        function cargartodo() {
            cargarPersonalIT();
            accederSistema();
        }
    </script>
</body>
</html>
