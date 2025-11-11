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
    <style>
        * {
            transition: color 0.3s ease, background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }

        :root {
            --primary-color: #dc2626;
            --primary-dark: #991b1b;
            --primary-light: #fecaca;
            --bg-dark: #0f172a;
            --bg-light: #ffffff;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --border-color: #e2e8f0;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #dc2626;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: var(--bg-light);
            color: var(--text-dark);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', sans-serif;
            min-height: 100vh;
            line-height: 1.6;
        }

        /* Header Principal */
        .header-main {
            background: linear-gradient(135deg, var(--text-dark) 0%, #1e293b 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 3rem;
            border-bottom: 4px solid var(--primary-color);
            position: relative;
            overflow: hidden;
        }

        .header-main::before {
            content: '';
            position: absolute;
            top: 0;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at top right, rgba(220, 38, 38, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .header-main h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
            letter-spacing: -0.5px;
        }

        .header-main p {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.8);
            position: relative;
            z-index: 1;
            font-weight: 300;
        }

        .main-card {
            background: var(--bg-light);
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .main-card:hover {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 20px 50px rgba(220, 38, 38, 0.1);
        }

        /* Botones Modernos */
        .btn {
            border-radius: 8px;
            padding: 10px 24px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.4);
            transform: translateY(-2px);
        }

        .btn-success {
            background: var(--success-color);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-success:hover {
            background: #059669;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: var(--text-dark);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            border-color: var(--text-dark);
            transform: translateY(-2px);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        .btn-outline-primary.active {
            background: var(--primary-color);
            color: white;
        }

        /* Form Controls */
        .form-control, .form-select {
            border-radius: 8px;
            padding: 12px 16px;
            border: 1.5px solid var(--border-color);
            font-size: 0.95rem;
            background: var(--bg-light);
            color: var(--text-dark);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
            background: var(--bg-light);
        }

        .form-control::placeholder {
            color: var(--text-light);
        }

        .form-label {
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        /* Tarjetas de Tickets */
        .ticket-card {
            border-radius: 12px;
            border-left: 4px solid transparent;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid var(--border-color);
            background: var(--bg-light);
            cursor: pointer;
        }

        .ticket-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 35px rgba(220, 38, 38, 0.15);
            border-color: var(--primary-color);
        }

        .ticket-priority-urgente { border-left-color: #dc2626; }
        .ticket-priority-alta { border-left-color: #f59e0b; }
        .ticket-priority-media { border-left-color: #3b82f6; }
        .ticket-priority-baja { border-left-color: #94a3b8; }

        .ticket-card .card-title {
            color: var(--text-dark);
            font-weight: 700;
            font-size: 1rem;
        }

        .ticket-card .card-text {
            color: var(--text-light);
        }

        /* Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.8rem;
            letter-spacing: 0.3px;
        }

        .bg-danger { background: #dc2626; }
        .bg-warning { background: #f59e0b; }
        .bg-info { background: #0ea5e9; }
        .bg-success { background: #10b981; }
        .bg-secondary { background: #94a3b8; }

        /* Tabs Modernos */
        .nav-tabs {
            border-bottom: 2px solid var(--border-color);
        }

        .nav-tabs .nav-link {
            color: var(--text-light);
            border: none;
            font-weight: 600;
            padding: 1rem 1.5rem;
            position: relative;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .nav-tabs .nav-link:hover {
            color: var(--primary-color);
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom: 3px solid var(--primary-color);
            background: transparent;
        }

        /* Tab Content */
        .tab-content {
            min-height: 400px;
            padding-top: 1.5rem;
        }

        /* Card Moderno */
        .card {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            background: var(--bg-light);
        }

        .card-header {
            background: linear-gradient(135deg, var(--text-dark) 0%, #1e293b 100%);
            color: white;
            border: none;
            font-weight: 700;
            padding: 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Estado del Personal IT */
        .personal-card {
            border-radius: 12px;
            border: 1px solid var(--border-color);
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            background: var(--bg-light);
        }

        .personal-card:hover {
            transform: translateY(-6px);
            border-color: var(--primary-color);
            box-shadow: 0 12px 30px rgba(220, 38, 38, 0.12);
        }

        .personal-card .fa-user-circle {
            color: var(--primary-color);
            transition: transform 0.3s ease;
        }

        .personal-card:hover .fa-user-circle {
            transform: scale(1.15);
        }

        .personal-card h6 {
            color: var(--text-dark);
            font-weight: 700;
            margin: 1rem 0;
        }

        /* Alertas */
        .alert {
            border: none;
            border-radius: 10px;
            border-left: 4px solid;
            padding: 1.25rem;
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border-left-color: #f59e0b;
        }

        .alert-info {
            background: #cffafe;
            color: #0c4a6e;
            border-left-color: #0ea5e9;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left-color: #10b981;
        }

        .alert-danger {
            background: #fee2e2;
            color: #7f1d1d;
            border-left-color: #dc2626;
        }

        .alert-heading {
            color: inherit;
            font-weight: 700;
        }

        /* Modal */
        .modal-content {
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 1.5rem;
            font-weight: 700;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        /* Table */
        .table {
            font-size: 0.95rem;
        }

        .table th {
            color: var(--text-dark);
            font-weight: 700;
            border-bottom: 2px solid var(--border-color);
        }

        .table td {
            color: var(--text-dark);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem;
        }

        /* Container Padding */
        .container {
            max-width: 1200px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-main h1 {
                font-size: 1.8rem;
            }

            .btn {
                padding: 8px 16px;
                font-size: 0.9rem;
            }

            .nav-tabs .nav-link {
                padding: 0.75rem 1rem;
                font-size: 0.85rem;
            }
        }

        /* Animaciones */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }
            100% {
                background-position: 1000px 0;
            }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        .slide-in-up {
            animation: slideInUp 0.4s ease-out;
        }

        .scale-in {
            animation: scaleIn 0.3s ease-out;
        }

        /* Efecto para Input Groups */
        .input-group {
            position: relative;
            z-index: 1;
        }

        .input-group-text {
            transition: all 0.3s ease;
        }

        .form-control:focus + .input-group-text,
        .input-group:has(.form-control:focus) .input-group-text {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        /* Mejoras de sombras y profundidad */
        .shadow-sm-hover {
            transition: box-shadow 0.3s ease;
        }

        .shadow-sm-hover:hover {
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15) !important;
        }

        /* Estilo para enlaces */
        a {
            text-decoration: none;
            color: var(--primary-color);
            transition: color 0.3s ease;
        }

        a:hover {
            color: var(--primary-dark);
        }

        /* Scroll suave para dropdown */
        .dropdown-menu {
            border: 1px solid var(--border-color);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .dropdown-item:hover,
        .dropdown-item:focus {
            background: #f8fafc;
            color: var(--primary-color);
        }

        /* Mejorar foco de teclado */
        *:focus {
            outline: none;
        }

        button:focus,
        input:focus,
        select:focus,
        textarea:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        /* Animación de carga */
        .loading {
            animation: shimmer 2s infinite;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 1000px 100%;
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
                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
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
                        <button type="button" class="btn" style="background: #f1f5f9; color: var(--primary-color); border: 2px solid var(--primary-color); padding: 0.75rem; font-weight: 600;" onclick="mostrarFormularioTicket()">
                            <i class="fas fa-plus me-2"></i>Nuevo Ticket
                        </button>
                        <a href="admin.php" class="btn" style="background: #f1f5f9; color: var(--text-dark); border: 2px solid var(--border-color); padding: 0.75rem; font-weight: 600; text-decoration: none;">
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
        <div style="background: linear-gradient(135deg, var(--text-dark) 0%, #1e293b 100%); border-radius: 12px; padding: 2rem; color: white; margin-bottom: 2rem;">
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
                            <button type="button" class="btn" style="background: #f1f5f9; color: var(--text-dark); border: 1px solid var(--border-color); padding: 0.75rem; font-weight: 600;" onclick="mostrarTab('tickets', document.querySelector('[data-tab=tickets]'))">
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