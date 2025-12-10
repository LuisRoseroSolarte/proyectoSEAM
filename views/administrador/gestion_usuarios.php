<?php
// === views/administrador/gestion_usuarios.php (VERSIÓN MEJORADA) ===

// 1. Protección de la página: Solo Rol 1 (Administrador)
require_once __DIR__ . '/../../controllers/verificar_sesion.php';
verificarAcceso([1]);

// 2. Incluir el Modelo de Usuario
require_once __DIR__ . '/../../models/UsuarioModel.php';

// Variables de mensajes de retroalimentación
$mensaje_exito = null;
$mensaje_error = null;

// 3. Lógica: Manejo de la Solicitud de Eliminación (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    $id_a_eliminar = (int)$_POST['eliminar_id'];
    $id_usuario_actual = (int)$_SESSION['id_usuario'];

    if ($id_a_eliminar === $id_usuario_actual) {
        $mensaje_error = "Error: No puedes eliminar tu propia cuenta de administrador.";
    } else {
        $eliminacion_exitosa = EliminarUsuario($id_a_eliminar);
        if ($eliminacion_exitosa) {
            $mensaje_exito = "El usuario con ID $id_a_eliminar ha sido eliminado correctamente.";
        } else {
            $mensaje_error = "Hubo un error al intentar eliminar el usuario de la base de datos.";
        }
    }
}

// 4. Lógica: Carga de Datos para la lista
try {
    $usuarios = ObtenerTodosLosUsuarios();
    $mensaje_error_bd = null;
    if (empty($usuarios) && empty($mensaje_exito)) {
        $mensaje_error_bd = "No se encontraron usuarios en la base de datos.";
    }
} catch (Exception $e) {
    $usuarios = [];
    $mensaje_error_bd = "Error al cargar los datos: " . $e->getMessage();
}

$titulo_pagina = "Gestión de Usuarios";
$nombre_usuario = htmlspecialchars($_SESSION['nombre']);
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

        .badge-activo {
            background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-inactivo {
            background: linear-gradient(90deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
        }

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

        .alert-custom {
            border-radius: 10px;
            border: none;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .modal-header-danger {
            background: linear-gradient(90deg, #dc3545 0%, #c82333 100%);
            color: white;
        }

        .modal-content {
            border-radius: 15px;
            overflow: hidden;
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
                <a href="dashboard.php" class="list-group-item"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                <a href="gestion_usuarios.php" class="list-group-item active-link"><i class="fas fa-users me-2"></i> Usuarios</a>
                <a href="gestion_clientes.php" class="list-group-item"><i class="fas fa-user-tie me-2"></i> Clientes</a>
                <a href="gestion_ordenes.php" class="list-group-item"><i class="fas fa-file-invoice me-2"></i> Órdenes de Servicio</a>
            </div>
        </div>
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-custom">
                <div class="container-fluid">
                    <div class="d-flex align-items-center">
                        <button class="menu-toggle" id="menu-toggle"><i class="fas fa-bars"></i></button>
                        <h2>Gestión de Usuarios</h2>
                    </div>
                    <div class="ms-auto">
                        <div class="dropdown">
                            <div class="user-info dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i>
                                <span class="d-none d-md-inline"><?php echo $nombre_usuario; ?></span>
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
                <?php if ($mensaje_exito): ?>
                    <div class="alert alert-success alert-custom mb-4" role="alert">
                        <i class="fas fa-check-circle me-2"></i> <?php echo $mensaje_exito; ?>
                    </div>
                <?php endif; ?>
                <?php if ($mensaje_error): ?>
                    <div class="alert alert-danger alert-custom mb-4" role="alert">
                        <i class="fas fa-times-circle me-2"></i> <?php echo $mensaje_error; ?>
                    </div>
                <?php endif; ?>
                <div class="card card-custom">
                    <div class="card-header-custom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div><i class="fas fa-users me-2"></i><span class="fs-5 fw-bold">Lista de Usuarios</span></div>
                        <a href="crear_usuario.php" class="btn btn-seam-primary"><i class="fas fa-user-plus me-2"></i> Nuevo Usuario</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-custom table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre Completo</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($usuarios)): ?>
                                        <?php foreach ($usuarios as $usuario): ?>
                                            <tr>
                                                <td class="align-middle"><?php echo htmlspecialchars($usuario->Id_Usuario); ?></td>
                                                <td class="align-middle"><?php echo htmlspecialchars($usuario->nombre_completo); ?></td>
                                                <td class="align-middle"><?php echo htmlspecialchars($usuario->email); ?></td>
                                                <td class="align-middle"><?php echo htmlspecialchars($usuario->nombre_rol); ?></td>
                                                <td class="align-middle">
                                                    <span class="<?php echo ($usuario->estado == 1) ? 'badge-activo' : 'badge-inactivo'; ?>">
                                                        <?php echo ($usuario->estado == 1) ? 'Activo' : 'Inactivo'; ?>
                                                    </span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <a href="editar_usuario.php?id=<?php echo $usuario->Id_Usuario; ?>" class="action-btn btn-edit mx-1" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="action-btn btn-delete mx-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#confirmDeleteModal"
                                                        data-id="<?php echo $usuario->Id_Usuario; ?>"
                                                        data-nombre="<?php echo htmlspecialchars($usuario->nombre_completo); ?>"
                                                        title="Eliminar">
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
                                                    <h4 class="mt-3">No hay usuarios registrados</h4>
                                                    <p>Comienza agregando tu primer usuario</p>
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
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-danger">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i> Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">¿Estás seguro de que deseas eliminar al usuario <strong><span id="modalUserName"></span></strong> (ID: <span id="modalUserId"></span>)?</p>
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i>Esta acción <strong>no</strong> se puede deshacer.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i> Cancelar</button>
                    <form id="deleteForm" method="POST" action="gestion_usuarios.php" style="display: inline;">
                        <input type="hidden" name="eliminar_id" id="eliminarUserId">
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i> Sí, Eliminar</button>
                    </form>
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
            const deleteModal = document.getElementById('confirmDeleteModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const userId = button.getAttribute('data-id');
                    const userName = button.getAttribute('data-nombre');
                    deleteModal.querySelector('#modalUserName').textContent = userName;
                    deleteModal.querySelector('#modalUserId').textContent = userId;
                    deleteModal.querySelector('#eliminarUserId').value = userId;
                });
            }
        });
    </script>
</body>

</html>