<?php
// 1. Lógica de Inclusión y Obtención de Datos
require_once __DIR__ . '/../../models/ClienteModel.php';
require_once __DIR__ . '/../../config/conexion.php';

// Simulación de datos de sesión (ajusta esto a tu lógica real)
$usuario_sesion = 'admin.seam (ADMINISTRADOR SEAM)';

$clientes = [];
$mensaje_error_bd = null;

try {
    $clientes = ObtenerTodosLosClientes();
} catch (Exception $e) {
    $clientes = [];
    $mensaje_error_bd = "No se pudieron cargar los clientes de la base de datos.";
    error_log("Error en gestión_clientes.php: " . $e->getMessage());
}

$titulo_pagina = "Gestión de Clientes";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_pagina; ?> - SEAM Ingeniería</title>
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

        /* Sidebar Styles */
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

        /* Main Content */
        #page-content-wrapper {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            width: calc(100% - var(--sidebar-width));
        }

        #wrapper.toggled #page-content-wrapper {
            margin-left: 0;
            width: 100%;
        }

        /* Navbar */
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

        /* User Dropdown */
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

        /* Card Styles */
        .card-custom {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .card-header-custom {
            background: linear-gradient(90deg, var(--seam-blue) 0%, var(--seam-blue-dark) 100%);
            color: white;
            padding: 20px;
            border: none;
        }

        /* Buttons */
        .btn-seam-primary {
            background: linear-gradient(90deg, var(--seam-yellow) 0%, var(--seam-yellow-dark) 100%);
            border: none;
            color: var(--seam-blue);
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-seam-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
            color: var(--seam-blue-dark);
        }

        /* Table Styles */
        .table-custom {
            background: white;
        }

        .table-custom thead {
            background: linear-gradient(90deg, var(--seam-blue) 0%, var(--seam-blue-dark) 100%);
            color: white;
        }

        .table-custom thead th {
            border: none;
            padding: 15px;
            font-weight: 600;
        }

        .table-custom tbody tr {
            transition: all 0.3s ease;
        }

        .table-custom tbody tr:hover {
            background: rgba(0, 102, 204, 0.05);
            transform: scale(1.01);
        }

        /* Status Badges */
        .status-badge-activo {
            background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
        }

        .status-badge-inactivo {
            background: linear-gradient(90deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
        }

        /* Action Buttons */
        .action-btn {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            border: none;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        .btn-edit {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: white;
        }

        .btn-delete {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }

        /* Empty State */
        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--seam-blue);
            opacity: 0.3;
            margin-bottom: 20px;
        }

        /* Alert Styles */
        .alert-custom {
            border-radius: 10px;
            border: none;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        /* Responsive Design */
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

            .navbar-custom h2 {
                font-size: 1.3rem;
            }

            .table-responsive {
                font-size: 0.85rem;
            }

            .action-btn {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 576px) {
            .sidebar-logo h3 {
                font-size: 1.2rem;
            }

            .list-group-item {
                padding: 12px 20px !important;
                font-size: 0.9rem;
            }

            .card-header-custom {
                padding: 15px;
            }

            .btn-seam-primary {
                padding: 8px 15px;
                font-size: 0.9rem;
            }
        }

        /* Overlay for mobile menu */
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
    </style>
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <div class="sidebar-logo">
                <h3>
                    <div class="logo-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div>
                        <div style="font-size: 1.5rem; line-height: 1.2;">SEAM</div>
                        <div style="font-size: 0.9rem; font-weight: 400; opacity: 0.9;">Ingeniería</div>
                    </div>
                </h3>
            </div>
            <div class="list-group list-group-flush my-3">
                <a href="../administrador/dashboard.php" class="list-group-item">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a href="../administrador/gestion_usuarios.php" class="list-group-item">
                    <i class="fas fa-users me-2"></i> Usuarios
                </a>
                <a href="#" class="list-group-item active-link">
                    <i class="fas fa-user-tie me-2"></i> Clientes
                </a>
                <a href="../administrador/gestion_ordenes.php" class="list-group-item">
                    <i class="fas fa-file-invoice me-2"></i> Órdenes de Servicio
                </a>
            </div>
        </div>

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-custom">
                <div class="container-fluid">
                    <div class="d-flex align-items-center">
                        <button class="menu-toggle" id="menu-toggle">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h2>Gestión de Clientes</h2>
                    </div>
                    <div class="ms-auto">
                        <div class="dropdown">
                            <div class="user-info dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i>
                                <span class="d-none d-md-inline"><?php echo htmlspecialchars($usuario_sesion); ?></span>
                            </div>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Perfil</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Configuración</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Salir</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid p-4">
                <!-- Alert Error BD -->
                <?php if ($mensaje_error_bd): ?>
                    <div class="alert alert-danger alert-custom mb-4" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo $mensaje_error_bd; ?>
                    </div>
                <?php endif; ?>

                <!-- Main Card -->
                <div class="card card-custom">
                    <div class="card-header-custom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <i class="fas fa-table me-2"></i>
                            <span class="fs-5 fw-bold">Lista de Clientes</span>
                        </div>
                        <a href="crear_cliente.php" class="btn btn-seam-primary">
                            <i class="fas fa-user-plus me-2"></i> Nuevo Cliente
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-custom table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre Completo</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($clientes)): ?>
                                        <?php foreach ($clientes as $cliente): ?>
                                            <tr>
                                                <td class="align-middle"><?php echo htmlspecialchars($cliente->idCliente); ?></td>
                                                <td class="align-middle"><?php echo htmlspecialchars($cliente->nombreCompleto); ?></td>
                                                <td class="align-middle"><?php echo htmlspecialchars($cliente->email); ?></td>
                                                <td class="align-middle"><?php echo htmlspecialchars($cliente->telefono); ?></td>
                                                <td class="align-middle">
                                                    <span class="<?php echo ($cliente->estado == 'activo') ? 'status-badge-activo' : 'status-badge-inactivo'; ?>">
                                                        <?php echo htmlspecialchars(ucfirst($cliente->estado)); ?>
                                                    </span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <a href="editar_cliente.php?id=<?php echo $cliente->idCliente; ?>" class="action-btn btn-edit mx-1" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="action-btn btn-delete mx-1" onclick="confirmarEliminacion('<?php echo $cliente->idCliente; ?>', '<?php echo htmlspecialchars($cliente->nombreCompleto); ?>')" title="Eliminar">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6">
                                                <div class="empty-state">
                                                    <i class="fas fa-users-slash"></i>
                                                    <h4 class="mt-3">No hay clientes registrados</h4>
                                                    <p>Comienza agregando tu primer cliente</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar
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

        // Confirm Delete
        function confirmarEliminacion(idCliente, nombreCliente) {
            if (confirm('¿Está seguro de que desea eliminar al cliente "' + nombreCliente + '" (ID: ' + idCliente + ')?\n\nEsta acción no se puede deshacer.')) {
                window.location.href = 'eliminar_cliente.php?id=' + idCliente;
            }
        }

        // Close sidebar on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('toggled');
                overlay.classList.remove('active');
            }
        });
    </script>
</body>

</html>