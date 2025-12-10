<?php
// === views/administrador/crear_usuario.php ===

// 1. Protección de la página: Solo Rol 1 (Administrador)
require_once __DIR__ . '/../../controllers/verificar_sesion.php';
verificarAcceso([1]);

// 2. Inclusión de Modelos y Controladores
require_once __DIR__ . '/../../models/UsuarioModel.php';

// Variables de mensajes y datos iniciales
$mensaje_exito = null;
$mensaje_error = null;
$roles = ObtenerRoles(); // Necesario para el selector de roles

// Inicializar el array de datos para mantener los valores si hay un error de validación
$data = [
    'nombre_completo' => '',
    'email' => '',
    'Id_Rol' => '',
    'estado' => 1 // Por defecto Activo
];

// =================================================================
// 3. Lógica: Manejo del Formulario (POST)
// =================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Captura de datos
    $data['nombre_completo'] = trim($_POST['nombre_completo'] ?? '');
    $data['email']           = trim($_POST['email'] ?? '');
    $data['Id_Rol']          = (int)($_POST['Id_Rol'] ?? 0);
    $data['estado']          = (int)($_POST['estado'] ?? 1);
    $password                = $_POST['password'] ?? '';
    $confirm_password        = $_POST['confirm_password'] ?? '';

    // Validación simple
    if (empty($data['nombre_completo']) || empty($data['email']) || empty($password) || $data['Id_Rol'] === 0) {
        $mensaje_error = "Todos los campos obligatorios (Nombre, Email, Rol, Contraseña) deben ser llenados.";
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $mensaje_error = "El formato del email no es válido.";
    } elseif ($password !== $confirm_password) {
        $mensaje_error = "La contraseña y la confirmación de contraseña no coinciden.";
    } elseif (strlen($password) < 6) {
        $mensaje_error = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        // Validación superada, intentar crear el usuario

        // 1. Verificar si el email ya existe
        if (BuscarUsuarioPorLogin($data['email'])) { // ASUMIENDO que tienes esta función en UsuarioModel
            $mensaje_error = "Error: El email proporcionado ya está registrado como usuario.";
        } else {
            // 2. Intentar crear
            $creacion_exitosa = CrearNuevoUsuario($data, $password);

            if ($creacion_exitosa) {
                $mensaje_exito = "¡Usuario creado exitosamente!";
                // Limpiar el formulario después del éxito
                $data = ['nombre_completo' => '', 'email' => '', 'Id_Rol' => '', 'estado' => 1];
            } else {
                $mensaje_error = "Hubo un error al intentar crear el usuario en la base de datos.";
            }
        }
    }
}
// NOTA: Para que la validación de email funcione, asegúrate de que tienes la función
// BuscarUsuarioPorEmail($email) en tu UsuarioModel que devuelva true/false.

$titulo_pagina = "Crear Nuevo Usuario";
$nombre_usuario = htmlspecialchars($_SESSION['nombre']);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_pagina; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --secondary-color: #7b4397;
            --tertiary-color: #dc2430;
            --bg-light: #f4f6f9;
            --sidebar-width: 250px;
        }

        body {
            background-color: var(--bg-light);
            overflow-x: hidden;
        }

        .wrapper {
            display: flex;
        }

        .sidebar {
            width: var(--sidebar-width);
            background-color: #ffffff;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            height: 100vh;
            position: fixed;
            z-index: 1050;
            transition: all 0.3s;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 0;
            width: calc(100% - var(--sidebar-width));
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            display: none;
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(0px - var(--sidebar-width));
                position: absolute;
            }

            .sidebar.active {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }

        .logo {
            font-size: 1.5em;
            font-weight: bold;
            color: var(--secondary-color);
            padding: 20px;
            border-bottom: 1px solid #eee;
        }

        .menu-item a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            text-decoration: none;
            color: #333;
            transition: background-color 0.3s;
        }

        .menu-item.active a {
            background-color: var(--bg-light);
            color: var(--secondary-color);
            border-left: 3px solid var(--secondary-color);
            font-weight: 600;
        }

        .top-navbar {
            background: linear-gradient(to right, #4c4a8a, var(--secondary-color));
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>

    <div class="wrapper">

        <div class="sidebar" id="sidebar">
            <div class="logo">SEAM Ingeniería</div>
            <ul class="nav flex-column">
                <li class="nav-item menu-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                <li class="nav-item menu-item active"><a class="nav-link" href="gestion_usuarios.php"><i class="fas fa-users me-2"></i>Usuarios</a></li>
                <li class="nav-item menu-item"><a class="nav-link" href="#"><i class="fas fa-user-tie me-2"></i>Clientes</a></li>
                <li class="nav-item menu-item"><a class="nav-link" href="#"><i class="fas fa-clipboard-list me-2"></i>Órdenes de Servicio</a></li>
            </ul>
        </div>

        <div class="overlay" id="overlay"></div>

        <div class="main-content">

            <nav class="top-navbar navbar navbar-expand-md py-3">
                <div class="container-fluid">
                    <button class="navbar-toggler d-block d-md-none" type="button" id="sidebarToggle">
                        <span class="navbar-toggler-icon"><i class="fas fa-bars"></i></span>
                    </button>

                    <div class="collapse navbar-collapse justify-content-end">
                        <div class="user-info text-white">
                            <i class="fas fa-user-circle me-1"></i>
                            <span>admin.seam (<?php echo strtoupper($nombre_usuario); ?>)</span>
                            <a href="../../controllers/logout.php" class="btn btn-sm btn-outline-light ms-3">Salir</a>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4">

                <h1 class="mb-4"><?php echo $titulo_pagina; ?></h1>

                <a href="gestion_usuarios.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left me-1"></i> Volver a Usuarios</a>

                <?php if ($mensaje_exito): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-1"></i> <?php echo $mensaje_exito; ?>
                    </div>
                <?php endif; ?>

                <?php if ($mensaje_error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-times-circle me-1"></i> <?php echo $mensaje_error; ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm p-4">
                    <h2 class="fs-5 mb-4">Información del Nuevo Empleado/Usuario</h2>

                    <form method="POST" action="crear_usuario.php">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="nombre_completo" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre_completo" name="nombre_completo"
                                    value="<?php echo htmlspecialchars($data['nombre_completo']); ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email / Username <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo htmlspecialchars($data['email']); ?>" required>
                                <div class="form-text">Usaremos el Email como nombre de usuario (Username).</div>
                            </div>

                            <div class="col-md-6">
                                <label for="Id_Rol" class="form-label">Rol <span class="text-danger">*</span></label>
                                <select id="Id_Rol" name="Id_Rol" class="form-select" required>
                                    <option value="">Seleccione un rol...</option>
                                    <?php foreach ($roles as $rol): ?>
                                        <option value="<?php echo $rol->Id_Rol; ?>"
                                            <?php echo ($data['Id_Rol'] == $rol->Id_Rol) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($rol->nombre_rol); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="estado" class="form-label">Estado</label>
                                <select id="estado" name="estado" class="form-select" required>
                                    <option value="1" <?php echo ($data['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                                    <option value="0" <?php echo ($data['estado'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                                </select>
                            </div>

                            <hr class="my-4">

                            <div class="col-md-6">
                                <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary me-2"><i class="fas fa-save me-1"></i> Guardar Usuario</button>
                                <a href="gestion_usuarios.php" class="btn btn-outline-secondary">Cancelar</a>
                            </div>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Lógica para el sidebar (debe ser la misma que usaste en dashboard.php)
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const toggleButton = document.getElementById('sidebarToggle');

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                overlay.style.display = sidebar.classList.contains('active') ? 'block' : 'none';
            }

            if (toggleButton) toggleButton.addEventListener('click', toggleSidebar);
            if (overlay) overlay.addEventListener('click', toggleSidebar);

            const menuLinks = sidebar.querySelectorAll('a');
            menuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        toggleSidebar();
                    }
                });
            });
        });
    </script>
</body>

</html>