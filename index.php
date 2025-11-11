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
    <title>Sistema de Tickets IT</title>
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
   
<!-- Header Principal -->
<div class="header-main">
    <div class="container">
        <div class="row align-items-center py-5">
            <div class="col-lg-6">
                <h1 class="mb-3" style="font-size: 3rem;">
                    <i class="fas fa-headset me-3"></i>Centro de Soporte IT
                </h1>
                <p style="font-size: 1.15rem; opacity: 0.9;">Gestión integral de tickets de soporte técnico. Resuelve incidencias rápido y eficientemente.</p>
                <div class="mt-4">
                    <span class="badge bg-danger me-2 px-3 py-2" style="font-size: 0.85rem;"><i class="fas fa-lightning me-2"></i>Rápido y Confiable</span>
                    <span class="badge bg-light text-dark px-3 py-2" style="font-size: 0.85rem;"><i class="fas fa-shield-alt me-2"></i>Seguro</span>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block text-end">
                <i class="fas fa-headset" style="font-size: 8rem; opacity: 0.15;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Contenido Principal -->
<div class="container py-5">
    <!-- Sección de Identificación -->
    <div class="row justify-content-center mb-5" id="identificacionSection">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card main-card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #d32f2f, #b71c1c); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                            <i class="fas fa-user-circle fa-2x text-white"></i>
                        </div>
                        <h2 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 0.5rem;">Acceso al Sistema</h2>
                        <p style="color: var(--text-light); margin: 0;">Ingresa tus datos para continuar</p>
                    </div>

                    <form id="formIdentificacion">
                        <div class="mb-4">
                            <label class="form-label">Número de Ficha</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                <input type="text" class="form-control" id="numeroFicha" placeholder="Ej: 12345" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Nombre Completo</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="nombreCompleto" placeholder="Ej: Juan Pérez" required>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary w-100 py-3 mb-3" onclick="accederSistema()" style="font-size: 1rem; font-weight: 600;">
                            <i class="fas fa-arrow-right me-2"></i>Acceder al Sistema
                        </button>
                    </form>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 2rem;">
                        <button type="button" class="btn" style="background: #ffffff; color: var(--primary-color); border: 2px solid var(--primary-color); padding: 0.75rem; font-weight: 600;" onclick="mostrarFormularioTicket()">
                            <i class="fas fa-plus me-2"></i>Nuevo Ticket
                        </button>
                        <a href="admin.php" class="btn" style="background: #ffffff; color: var(--text-dark); border: 2px solid var(--border-color); padding: 0.75rem; font-weight: 600; text-decoration: none;">
                            <i class="fas fa-cog me-2"></i>Administración
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notificación Importante -->
    <div class="row justify-content-center mb-5" id="alertSection">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="alert alert-warning alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; border-left: 4px solid #f59e0b; padding: 1.5rem;">
                <div class="d-flex">
                    <div style="margin-right: 1rem;">
                        <i class="fas fa-lightbulb fa-2x" style="color: #f59e0b;"></i>
                    </div>
                    <div>
                        <h6 class="alert-heading" style="font-weight: 700; margin-bottom: 0.5rem;">Importante</h6>
                        <p style="margin: 0; font-size: 0.95rem;">Usa exactamente la misma combinación de <strong>nombre y apellido</strong> con la que creaste tus tickets para verlos.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>

    <!-- Contenido Dinámico - Dashboard -->
    <div id="contenidoDinamico" style="display: none;">
        <!-- Header del Usuario Logueado -->
        <div style="background: linear-gradient(135deg, var(--text-dark) 0%, #2d2d2d 100%); border-radius: 12px; padding: 2rem; color: white; margin-bottom: 2rem;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 style="font-size: 1.8rem; font-weight: 700; margin: 0;">Bienvenido</h2>
                    <p id="usuarioNombre" style="font-size: 1.1rem; margin: 0.5rem 0 0 0; opacity: 0.9;"></p>
                </div>
                <div style="text-align: right;">
                    <button class="btn" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); padding: 0.75rem 1.5rem;" onclick="location.href='index.php'">
                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                    </button>
                </div>
            </div>
        </div>

        <!-- Estado del Personal IT -->
        <div style="margin-bottom: 3rem;">
            <h3 style="font-weight: 700; margin-bottom: 1.5rem;"><i class="fas fa-users me-2"></i>Estado del Personal IT</h3>
            <div class="row" id="personalIT" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
                <!-- Se llena dinámicamente -->
            </div>
        </div>

        <!-- Tabs de Navegación -->
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 2rem;">
            <button class="btn tab-button active" data-tab="tickets" onclick="mostrarTab('tickets', this)" style="text-align: left; background: white; border: 2px solid var(--primary-color); color: var(--primary-color); padding: 1.5rem; border-radius: 12px; font-weight: 700; cursor: pointer;">
                <i class="fas fa-list" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                Mis Tickets
            </button>
            <button class="btn tab-button" data-tab="nuevo" onclick="mostrarTab('nuevo', this)" style="text-align: left; background: white; border: 2px solid var(--border-color); color: var(--text-dark); padding: 1.5rem; border-radius: 12px; font-weight: 700; cursor: pointer;">
                <i class="fas fa-plus" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                Nuevo Ticket
            </button>
        </div>

        <!-- Mis Tickets -->
        <div id="tickets" class="tab-content-main" style="display: block;">
            <div class="card main-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 style="font-weight: 700; margin: 0;"><i class="fas fa-list me-2"></i>Mis Tickets</h3>
                        <div style="display: flex; gap: 0.75rem; align-items: center;">
                            <button class="btn btn-outline-primary btn-sm active" onclick="filtrarTicketsPorFecha('todos')" style="border-radius: 20px; padding: 0.5rem 1rem;">
                                <i class="fas fa-list me-1"></i>Todos
                            </button>
                            <button class="btn btn-outline-primary btn-sm" onclick="filtrarTicketsPorFecha('mes_actual')" style="border-radius: 20px; padding: 0.5rem 1rem;">
                                <i class="fas fa-calendar-day me-1"></i>Este Mes
                            </button>
                            <span class="badge bg-primary px-3 py-2" id="totalTickets" style="font-size: 0.9rem;">0</span>
                        </div>
                    </div>
                    <div id="listaTickets">
                        <!-- Se llena dinámicamente -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Nuevo Ticket -->
        <div id="nuevo" class="tab-content-main" style="display: none;">
            <div class="card main-card">
                <div class="card-body p-4">
                    <h3 style="font-weight: 700; margin-bottom: 1.5rem;"><i class="fas fa-plus me-2"></i>Crear Nuevo Ticket</h3>

                    <form id="formNuevoTicket">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Título del Problema *</label>
                                <input type="text" class="form-control" id="titulo" placeholder="Describe brevemente el problema" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Prioridad</label>
                                <select class="form-select" id="prioridad">
                                    <option value="baja">Baja</option>
                                    <option value="media" selected>Media</option>
                                    <option value="alta">Alta</option>
                                    <option value="urgente">Urgente</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Categoría</label>
                            <select class="form-select" id="categoria">
                                <option value="Hardware">Hardware</option>
                                <option value="Software">Software</option>
                                <option value="Red">Red/Conectividad</option>
                                <option value="Email">Email</option>
                                <option value="Impresoras">Impresoras</option>
                                <option value="Accesos">Accesos/Permisos</option>
                                <option value="Desarrollo">Desarrollo</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Descripción Detallada *</label>
                            <textarea class="form-control" id="descripcion" rows="5" required placeholder="Describe el problema con la mayor detalle posible..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Archivos Adjuntos (opcional)</label>
                            <input type="file" class="form-control" id="archivos" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt">
                            <small style="color: var(--text-light); display: block; margin-top: 0.5rem;">Máximo 5MB por archivo. Formatos permitidos: JPG, PNG, PDF, DOC, DOCX, TXT</small>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <button type="submit" class="btn btn-primary" style="padding: 0.75rem; font-weight: 600;">
                                <i class="fas fa-check-circle me-2"></i>Crear Ticket
                            </button>
                            <button type="button" class="btn" style="background: #ffffff; color: var(--text-dark); border: 1px solid var(--border-color); padding: 0.75rem; font-weight: 600;" onclick="mostrarTab('tickets', document.querySelector('[data-tab=tickets]'))">
                                <i class="fas fa-times-circle me-2"></i>Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ver Ticket -->
    <div class="modal fade" id="modalVerTicket" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTicketTitle"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalTicketBody">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer" id="modalTicketFooter">
                    <!-- Botones dinámicos -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        let usuarioActual = null;

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
            const numeroFicha = document.getElementById('numeroFicha').value.trim();
            const nombreCompleto = document.getElementById('nombreCompleto').value.trim();

            if (!numeroFicha || !nombreCompleto) {
                mostrarMensaje('Por favor completa todos los campos', 'warning');
                return;
            }

            // Verificar nombres similares
            const sugerencia = await verificarNombresSimilares(numeroFicha, nombreCompleto);
            if (sugerencia) {
                if (confirm(`¿Quizás quisiste decir "${sugerencia}"?`)) {
                    document.getElementById('nombreCompleto').value = sugerencia;
                    nombreCompleto = sugerencia;
                }
            }

            usuarioActual = {
                numeroFicha: numeroFicha,
                nombre: nombreCompleto
            };

            // Actualizar el nombre mostrado en el header de bienvenida
            document.getElementById('usuarioNombre').innerHTML = `<strong>${nombreCompleto}</strong> | Ficha: ${numeroFicha}`;

            document.getElementById('contenidoDinamico').style.display = 'block';
            document.getElementById('identificacionSection').style.display = 'none';
            document.getElementById('alertSection').style.display = 'none';

            cargarTicketsUsuario();
            cargarPersonalIT();
        }

        // Función para cambiar entre tabs
        function mostrarTab(tabName, btnElement) {
            // Ocultar todos los tabs
            document.querySelectorAll('.tab-content-main').forEach(el => el.style.display = 'none');

            // Mostrar el tab seleccionado
            document.getElementById(tabName).style.display = 'block';

            // Actualizar estado de botones
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.style.borderColor = 'var(--border-color)';
                btn.style.color = 'var(--text-dark)';
            });

            btnElement.style.borderColor = 'var(--primary-color)';
            btnElement.style.color = 'var(--primary-color)';
        }

        function mostrarFormularioTicket() {
            const numeroFicha = document.getElementById('numeroFicha').value.trim();
            const nombreCompleto = document.getElementById('nombreCompleto').value.trim();

            if (!numeroFicha || !nombreCompleto) {
                mostrarMensaje('Por favor completa tu número de ficha y nombre antes de crear un ticket', 'warning');
                return;
            }

            usuarioActual = {
                numeroFicha: numeroFicha,
                nombre: nombreCompleto
            };

            // Actualizar el nombre mostrado en el header de bienvenida
            document.getElementById('usuarioNombre').innerHTML = `<strong>${nombreCompleto}</strong> | Ficha: ${numeroFicha}`;

            document.getElementById('contenidoDinamico').style.display = 'block';
            document.getElementById('identificacionSection').style.display = 'none';
            document.getElementById('alertSection').style.display = 'none';

            // Activar tab de nuevo ticket
            mostrarTab('nuevo', document.querySelector('[data-tab="nuevo"]'));

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
            const container = document.getElementById('personalIT');

            container.innerHTML = personal.map(persona => `
                <div class="col-md-4 col-lg-3 mb-3">
                    <div class="personal-card">
                        <i class="fas fa-user-circle fa-3x mb-3"></i>
                        <h6 class="mb-3">${persona.nombre}</h6>
                        <span class="badge bg-${obtenerColorEstadoIT(persona.estado)} px-3 py-2">
                            <i class="fas fa-circle me-2" style="font-size: 8px;"></i>
                            ${persona.estado.charAt(0).toUpperCase() + persona.estado.slice(1)}
                        </span>
                        ${persona.tickets_activos > 0 ? `<small class="d-block mt-3" style="color: var(--text-light);">${persona.tickets_activos} ticket(s) activo(s)</small>` : ''}
                    </div>
                </div>
            `).join('');
        }

        function obtenerColorEstadoIT(estado) {
            switch(estado) {
                case 'disponible': return 'success';
                case 'ocupado': return 'warning';
                case 'ausente': return 'danger';
                default: return 'secondary';
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
                    mostrarMensaje(data.message || 'Error al cargar tickets', 'danger');
                }
            } catch (error) {
                mostrarMensaje('Error de conexión', 'danger');
            }
        }

        let todosLosTickets = []; // Variable global para almacenar todos los tickets

        function mostrarTickets(tickets) {
            todosLosTickets = tickets; // Guardar los tickets para filtrado posterior
            const container = document.getElementById('listaTickets');
            const total = document.getElementById('totalTickets');

            total.textContent = tickets.length;

            if (tickets.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No tienes tickets creados</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = tickets.map(ticket => `
                <div class="card ticket-card ticket-priority-${ticket.prioridad} mb-3" data-fecha="${ticket.fecha_creacion}">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="card-title mb-1">#${ticket.id} - ${ticket.titulo}</h6>
                                <p class="card-text text-muted mb-2">${ticket.descripcion.substring(0, 100)}...</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)}">${ticket.prioridad.toUpperCase()}</span>
                                    <span class="badge bg-${obtenerColorEstado(ticket.estado)}">${ticket.estado.replace('_', ' ').toUpperCase()}</span>
                                    <span class="badge bg-secondary">${ticket.categoria}</span>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <small class="text-muted d-block mb-2">${formatearFecha(ticket.fecha_creacion)}</small>
                                <button class="btn btn-outline-primary btn-sm" onclick="verTicket(${ticket.id})">
                                    <i class="fas fa-eye me-1"></i>Ver Detalles
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function filtrarTicketsPorFecha(filtro) {
            // Actualizar botones activos
            event.target.parentNode.querySelectorAll('.btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

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

            // Actualizar contador y mostrar tickets filtrados
            const total = document.getElementById('totalTickets');
            total.textContent = ticketsFiltrados.length;

            const container = document.getElementById('listaTickets');

            if (ticketsFiltrados.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay tickets ${filtro === 'mes_actual' ? 'para este mes' : ''}</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = ticketsFiltrados.map(ticket => `
                <div class="card ticket-card ticket-priority-${ticket.prioridad} mb-3" data-fecha="${ticket.fecha_creacion}">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="card-title mb-1">#${ticket.id} - ${ticket.titulo}</h6>
                                <p class="card-text text-muted mb-2">${ticket.descripcion.substring(0, 100)}...</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)}">${ticket.prioridad.toUpperCase()}</span>
                                    <span class="badge bg-${obtenerColorEstado(ticket.estado)}">${ticket.estado.replace('_', ' ').toUpperCase()}</span>
                                    <span class="badge bg-secondary">${ticket.categoria}</span>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <small class="text-muted d-block mb-2">${formatearFecha(ticket.fecha_creacion)}</small>
                                <button class="btn btn-outline-primary btn-sm" onclick="verTicket(${ticket.id})">
                                    <i class="fas fa-eye me-1"></i>Ver Detalles
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

    // Reemplaza la función del formulario en index.php (línea ~348-380)
document.getElementById('formNuevoTicket').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!usuarioActual) {
        mostrarMensaje('Error: Datos de usuario no encontrados', 'danger');
        return;
    }
    
    // Crear FormData para incluir archivos
    const formData = new FormData();
    formData.append('numero_ficha', usuarioActual.numeroFicha);
    formData.append('nombre', usuarioActual.nombre);
    formData.append('titulo', document.getElementById('titulo').value);
    formData.append('descripcion', document.getElementById('descripcion').value);
    formData.append('prioridad', document.getElementById('prioridad').value);
    formData.append('categoria', document.getElementById('categoria').value);
    
    // Agregar archivos si existen
    const archivos = document.getElementById('archivos').files;
    for (let i = 0; i < archivos.length; i++) {
        formData.append('archivos[]', archivos[i]);
    }
    
    try {
        const response = await fetch('api/tickets_publico.php?action=crear', {
            method: 'POST',
            body: formData  // Usar FormData en lugar de JSON
        });
        
        const data = await response.json();
        
        if (data.success) {
            mostrarMensaje('Ticket creado exitosamente', 'success');
            
            // 🔔 ENVIAR NOTIFICACIÓN USANDO EL MÉTODO QUE FUNCIONA (ANTES del reset)
            const titulo = document.getElementById('titulo').value;
            const categoria = document.getElementById('categoria').value;
            const prioridad = document.getElementById('prioridad').value;
            const nombreSolicitante = usuarioActual.nombre;
            
            const notificationTitle = `🎫 Nuevo Ticket - ${prioridad.toUpperCase()}`;
            const notificationBody = `📁 ${categoria} - ${nombreSolicitante} - ${titulo}`;
            
            console.log('📧 Enviando notificación automática para ticket:', data.ticket_id);
            sendTicketNotification(notificationTitle, notificationBody, {
                ticket_id: data.ticket_id,
                action: 'new_ticket'
            });
            
            // Resetear formulario DESPUÉS de obtener los datos
            document.getElementById('formNuevoTicket').reset();
            
            // Cambiar a la pestaña de tickets y recargar
            const ticketsTab = new bootstrap.Tab(document.getElementById('tickets-tab'));
            ticketsTab.show();
            cargarTicketsUsuario();
        } else {
            mostrarMensaje(data.message || 'Error al crear el ticket', 'danger');
            console.error('Error detallado:', data);
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
                    mostrarMensaje(data.message || 'Error al cargar el ticket', 'danger');
                }
            } catch (error) {
                mostrarMensaje('Error de conexión', 'danger');
            }
        }

        function mostrarDetalleTicket(ticket) {
            const modal = document.getElementById('modalVerTicket');
            const title = document.getElementById('modalTicketTitle');
            const body = document.getElementById('modalTicketBody');
            const footer = document.getElementById('modalTicketFooter');
            
            title.innerHTML = `Ticket #${ticket.id} - ${ticket.titulo}`;
            
            let archivosHtml = '';
            if (ticket.archivos && ticket.archivos.length > 0) {
                archivosHtml = `
                    <tr><td colspan="2"><strong>Archivos adjuntos:</strong></td></tr>
                    <tr><td colspan="2">
                        ${ticket.archivos.map(archivo => `
                            <a href="uploads/${archivo.ruta_archivo}" target="_blank" class="btn btn-sm btn-outline-primary me-2 mb-1">
                                <i class="fas fa-paperclip me-1"></i>${archivo.nombre_archivo}
                            </a>
                        `).join('')}
                    </td></tr>
                `;
            }
            
            body.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>Título:</strong></td><td>${ticket.titulo}</td></tr>
                            <tr><td><strong>Descripción:</strong></td><td>${ticket.descripcion}</td></tr>
                            <tr><td><strong>Categoría:</strong></td><td>${ticket.categoria}</td></tr>
                            <tr><td><strong>Prioridad:</strong></td><td><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)}">${ticket.prioridad.toUpperCase()}</span></td></tr>
                            <tr><td><strong>Estado:</strong></td><td><span class="badge bg-${obtenerColorEstado(ticket.estado)}">${ticket.estado.replace('_', ' ').toUpperCase()}</span></td></tr>
                            ${archivosHtml}
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>Solicitante:</strong></td><td>${ticket.solicitante_nombre}</td></tr>
                            <tr><td><strong>Ficha:</strong></td><td>${ticket.numero_ficha}</td></tr>
                            <tr><td><strong>Asignado a:</strong></td><td>${ticket.asignado_nombre || '<span class="text-muted">Sin asignar</span>'}</td></tr>
                            <tr><td><strong>Fecha creación:</strong></td><td>${formatearFecha(ticket.fecha_creacion)}</td></tr>
                            ${ticket.fecha_resolucion ? `<tr><td><strong>Fecha resolución:</strong></td><td>${formatearFecha(ticket.fecha_resolucion)}</td></tr>` : ''}
                            ${ticket.resolucion ? `<tr><td><strong>Resolución:</strong></td><td>${ticket.resolucion}</td></tr>` : ''}
                        </table>
                    </div>
                </div>
            `;
            
            let footerButtons = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>';
            
            if (ticket.estado === 'resuelto') {
                footerButtons = `
                    <button type="button" class="btn btn-success" onclick="cerrarTicket(${ticket.id}, 'satisfactoria')">
                        <i class="fas fa-thumbs-up me-2"></i>Satisfactoria
                    </button>
                    <button type="button" class="btn btn-warning" onclick="cerrarTicket(${ticket.id}, 'insatisfactoria')">
                        <i class="fas fa-thumbs-down me-2"></i>Insatisfactoria
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                `;
            }
            
            footer.innerHTML = footerButtons;
            new bootstrap.Modal(modal).show();
        }

        async function cerrarTicket(ticketId, satisfaccion) {
            try {
                const response = await fetch(`api/tickets_publico.php?action=cerrar&id=${ticketId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        satisfaccion: satisfaccion
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    mostrarMensaje('Ticket cerrado exitosamente', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('modalVerTicket')).hide();
                    cargarTicketsUsuario();
                } else {
                    mostrarMensaje(data.message || 'Error al cerrar el ticket', 'danger');
                }
            } catch (error) {
                mostrarMensaje('Error de conexión', 'danger');
            }
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
        
        
        // Función para probar notificaciones
        async function testNotification() {
            try {
                mostrarMensaje('⏳ Enviando notificación de prueba...', 'info');
                
                const response = await fetch('http://192.168.1.134:5214/web/Ticket/api.php/api/test_notification', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        'title': '🧪 Test desde Web',
                        'body': 'Esta es una notificación de prueba desde la página web - ' + new Date().toLocaleTimeString()
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    mostrarMensaje('✅ Notificación de prueba enviada correctamente!', 'success');
                    console.log('Test notification response:', data);
                } else {
                    mostrarMensaje('❌ Error enviando notificación: ' + (data.error || 'Error desconocido'), 'danger');
                }
                
            } catch (error) {
                console.error('Error:', error);
                mostrarMensaje('❌ Error de conexión: ' + error.message, 'danger');
            }
        }
        
        // Función para enviar notificación de ticket usando el mismo método exitoso
        async function sendTicketNotification(title, body, data = {}) {
            try {
                console.log('📧 Enviando notificación de ticket...', { title, body, data });
                
                const response = await fetch('http://192.168.1.134:5214/web/Ticket/api.php/api/test_notification', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        'title': title,
                        'body': body
                    })
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    console.log('✅ Notificación de ticket enviada exitosamente!', result);
                } else {
                    console.error('❌ Error enviando notificación de ticket:', result);
                }
                
                return response.ok;
                
            } catch (error) {
                console.error('❌ Error enviando notificación de ticket:', error);
                return false;
            }
        }
       
        // Actualizar personal IT cada 30 segundos
        function cargartodo(){
            cargarPersonalIT();
            accederSistema();
        }
       
    </script>
</body>
</html>