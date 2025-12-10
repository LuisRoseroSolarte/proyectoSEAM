<?php
// === views/administrador/dashboard.php (VERSIÓN MEJORADA) ===

// 1. Protección de la página: Solo Rol 1 (Administrador)
require_once __DIR__ . '/../../controllers/verificar_sesion.php';
verificarAcceso([1]);

// 2. Inclusión de Modelos
require_once __DIR__ . '/../../models/UsuarioModel.php';

// 3. Lógica: Consulta a la base de datos
$total_usuarios = ContarTotalUsuarios();
$total_clientes = 0; // Implementar función cuando exista ClienteModel
$ordenes_activas = 0; // Implementar función cuando exista OrdenModel

$fecha_actual = date("l, d \d\e F Y - H:i:s");
$nombre_usuario = htmlspecialchars($_SESSION['nombre']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SEAM Ingeniería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --seam-blue: #0066CC;
            --seam-blue-dark: #004C99;
            --seam-yellow: #FFD700;
            --seam-yellow-dark: #FFC700;
            --sidebar-width: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e8f0f8 100%);
            min-height: 100vh;
        }

        #sidebar-wrapper {
            min-height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--seam-blue) 0%, var(--seam-blue-dark) 100%);
            position: fixed;
            left: 0;
            top: 0;
            transition: margin-left 0.3s ease;
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0, 102, 204, 0.2);
        }

        #sidebar-wrapper.toggled {
            margin-left: calc(-1 * var(--sidebar-width));
        }

        .sidebar-logo {
            padding: 25px 20px;
            background: rgba(255, 215, 0, 0.1);
            border-bottom: 3px solid var(--seam-yellow);
        }

        .sidebar-logo h3 {
            color: white;
            font-weight: 700;
            font-size: 1.5rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-logo h3>div:last-child {
            display: flex;
            flex-direction: column;
        }

        .logo-icon {
            width: 45px;
            height: 45px;
            background: var(--seam-yellow);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: var(--seam-blue);
            font-size: 1.3rem;
        }

        .list-group-item {
            background: transparent !important;
            border: none !important;
            color: rgba(255, 255, 255, 0.8) !important;
            padding: 15px 25px !important;
            transition: all 0.3s ease;
            font-weight: 500;
            margin: 5px 10px;
            border-radius: 10px !important;
        }

        .list-group-item:hover {
            background: rgba(255, 255, 255, 0.1) !important;
            color: white !important;
            transform: translateX(5px);
        }

        .list-group-item.active-link {
            background: linear-gradient(90deg, var(--seam-yellow) 0%, var(--seam-yellow-dark) 100%) !important;
            color: var(--seam-blue) !important;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
        }

        #page-content-wrapper {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            width: calc(100% - var(--sidebar-width));
        }

        #wrapper.toggled #page-content-wrapper {
            margin-left: 0;
            width: 100%;
        }

        .navbar-custom {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
        }

        .navbar-custom h2 {
            background: linear-gradient(90deg, var(--seam-blue) 0%, var(--seam-blue-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            margin: 0;
        }

        .menu-toggle {
            background: var(--seam-blue);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 15px;
        }

        .menu-toggle:hover {
            background: var(--seam-blue-dark);
            transform: scale(1.05);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 15px;
            background: linear-gradient(90deg, var(--seam-blue) 0%, var(--seam-blue-dark) 100%);
            border-radius: 25px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-info:hover {
            box-shadow: 0 4px 15px rgba(0, 102, 204, 0.3);
        }

        .welcome-header {
            background: linear-gradient(135deg, var(--seam-blue) 0%, var(--seam-blue-dark) 100%);
            border-radius: 20px;
            padding: 2rem;
            color: white;
            box-shadow: 0 8px 25px rgba(0, 102, 204, 0.3);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .welcome-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 215, 0, 0.1);
            border-radius: 50%;
        }

        .welcome-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .welcome-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .welcome-header small {
            opacity: 0.8;
            position: relative;
            z-index: 1;
        }

        .metric-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: none;
            height: 100%;
        }

        .metric-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .metric-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            margin-bottom: 1rem;
        }

        .metric-icon.users {
            background: linear-gradient(135deg, var(--seam-blue) 0%, var(--seam-blue-dark) 100%);
        }

        .metric-icon.clients {
            background: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 100%);
        }

        .metric-icon.orders {
            background: linear-gradient(135deg, var(--seam-yellow) 0%, var(--seam-yellow-dark) 100%);
            color: var(--seam-blue);
        }

        .metric-value {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(90deg, var(--seam-blue) 0%, var(--seam-blue-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0.5rem 0;
        }

        .metric-label {
            color: #6c757d;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .quick-actions-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .quick-actions-card h2 {
            background: linear-gradient(90deg, var(--seam-blue) 0%, var(--seam-blue-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .action-btn {
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-primary-custom {
            background: linear-gradient(90deg, var(--seam-blue) 0%, var(--seam-blue-dark) 100%);
            color: white;
        }

        .btn-success-custom {
            background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-warning-custom {
            background: linear-gradient(90deg, var(--seam-yellow) 0%, var(--seam-yellow-dark) 100%);
            color: var(--seam-blue);
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .sidebar-overlay.active {
            display: block;
        }

        @media (max-width: 768px) {
            #sidebar-wrapper {
                margin-left: calc(-1 * var(--sidebar-width));
            }

            #sidebar-wrapper.toggled {
                margin-left: 0;
            }

            #page-content-wrapper {
                margin-left: 0;
                width: 100%;
            }

            .welcome-header h1 {
                font-size: 1.5rem;
            }

            .welcome-header p {
                font-size: 1rem;
            }

            .metric-value {
                font-size: 2rem;
            }

            .action-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="d-flex" id="wrapper">
        <div id="sidebar-wrapper">
            <div class="sidebar-logo">
                <h3>
                    <div class="logo-icon"><i class="fas fa-bolt"></i></div>
                    <div>
                        <div style="font-size: 1.5rem; line-height: 1.2;">SEAM</div>
                        <div style="font-size: 0.9rem; font-weight: 400; opacity: 0.9;">Ingeniería</div>
                    </div>
                </h3>
            </div>
            <div class="list-group list-group-flush my-3">
                <a href="dashboard.php" class="list-group-item active-link"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                <a href="gestion_usuarios.php" class="list-group-item"><i class="fas fa-users me-2"></i> Usuarios</a>
                <a href="gestion_clientes.php" class="list-group-item"><i class="fas fa-user-tie me-2"></i> Clientes</a>
                <a href="gestion_ordenes.php" class="list-group-item"><i class="fas fa-file-invoice me-2"></i> Órdenes de Servicio</a>
            </div>
        </div>
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-custom">
                <div class="container-fluid">
                    <div class="d-flex align-items-center">
                        <button class="menu-toggle" id="menu-toggle"><i class="fas fa-bars"></i></button>
                        <h2>Dashboard</h2>
                    </div>
                    <div class="ms-auto">
                        <div class="dropdown">
                            <div class="user-info dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i>
                                <span class="d-none d-md-inline"><?php echo strtoupper($nombre_usuario); ?></span>
                            </div>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Perfil</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Configuración</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="../../controllers/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Salir</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            <div class="container-fluid p-4">
                <div class="welcome-header">
                    <h1><i class="fas fa-hand-sparkles me-2"></i> ¡Bienvenido, <?php echo strtoupper($nombre_usuario); ?>!</h1>
                    <p>Panel de Control - SEAM Ingeniería</p>
                    <small><i class="far fa-calendar-alt me-2"></i><?php echo $fecha_actual; ?></small>
                </div>
                <div class="row g-4 mb-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="metric-card">
                            <div class="metric-icon users"><i class="fas fa-users"></i></div>
                            <div class="metric-label">Total Usuarios</div>
                            <div class="metric-value"><?php echo $total_usuarios; ?></div>
                            <small class="text-muted"><i class="fas fa-arrow-up text-success me-1"></i> Activos en el sistema</small>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="metric-card">
                            <div class="metric-icon clients"><i class="fas fa-user-tie"></i></div>
                            <div class="metric-label">Clientes Registrados</div>
                            <div class="metric-value"><?php echo $total_clientes; ?></div>
                            <small class="text-muted"><i class="fas fa-arrow-up text-success me-1"></i> Base de clientes</small>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="metric-card">
                            <div class="metric-icon orders"><i class="fas fa-clipboard-check"></i></div>
                            <div class="metric-label">Órdenes Activas</div>
                            <div class="metric-value"><?php echo $ordenes_activas; ?></div>
                            <small class="text-muted"><i class="fas fa-clock text-warning me-1"></i> En proceso</small>
                        </div>
                    </div>
                </div>
                <div class="quick-actions-card">
                    <h2><i class="fas fa-bolt me-2"></i> Acciones Rápidas</h2>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="gestion_usuarios.php" class="action-btn btn-primary-custom">
                            <i class="fas fa-user-plus"></i><span>Nuevo Usuario</span>
                        </a>
                        <a href="gestion_clientes.php" class="action-btn btn-success-custom">
                            <i class="fas fa-user-tie"></i><span>Nuevo Cliente</span>
                        </a>
                        <a href="gestion_ordenes.php" class="action-btn btn-warning-custom">
                            <i class="fas fa-file-invoice"></i><span>Nueva Orden</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menu-toggle');
            const wrapper = document.getElementById('wrapper');
            const sidebar = document.getElementById('sidebar-wrapper');
            const overlay = document.getElementById('sidebarOverlay');
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('toggled');
                wrapper.classList.toggle('toggled');
                overlay.classList.toggle('active');
            });
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('toggled');
                wrapper.classList.remove('toggled');
                overlay.classList.remove('active');
            });
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('toggled');
                    overlay.classList.remove('active');
                }
            });
        });
    </script>
</body>

</html>