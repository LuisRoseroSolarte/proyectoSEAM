<?php
// === views/administrador/editar_usuario.php (VERSIÓN FINAL) ===

// 1. Inclusión y seguridad: Solo Administradores (Rol 1)
// Este archivo requiere que el usuario esté logueado y sea Administrador.
require_once __DIR__ . '/../../controllers/verificar_sesion.php';
verificarAcceso([1]);

// 2. Inclusión de Modelos
// Necesitamos las funciones ObtenerUsuarioPorId() y ActualizarUsuario()
require_once __DIR__ . '/../../models/UsuarioModel.php';

$titulo_pagina = "Editar Usuario";
$nombre_usuario = htmlspecialchars($_SESSION['nombre']);

// Variables para mensajes de retroalimentación
$mensaje_exito = null;
$mensaje_error = null;

// =================================================================
// 3. Lógica: Obtener el ID del usuario a editar y Cargar Datos
// =================================================================

// Verificamos si se pasó un ID válido por la URL (GET request)
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Si no hay ID válido, redirigimos inmediatamente.
    header('Location: gestion_usuarios.php');
    exit();
}

$id_usuario = (int)$_GET['id'];
$usuario_a_editar = ObtenerUsuarioPorId($id_usuario);

// Verificamos si el usuario existe en la base de datos
if (!$usuario_a_editar) {
    $error_carga = "El usuario con ID $id_usuario no fue encontrado en la base de datos.";
    // Objeto dummy para evitar errores si el usuario no existe y se intenta acceder a sus propiedades
    $usuario_a_editar = (object)['nombre_completo' => 'N/A', 'email' => 'N/A', 'id_rol' => 0, 'estado' => 0];
} else {
    $error_carga = null;
}

// Lógica de roles (Simulación):
// En un proyecto real, cargarías esto desde la base de datos con una función (ej. ObtenerTodosLosRoles())
$roles = [
    (object)['Id_Rol' => 1, 'nombre_rol' => 'Administrador'],
    (object)['Id_Rol' => 2, 'nombre_rol' => 'Empleado']
];


// =================================================================
// 4. Lógica: Manejo del Formulario (POST) - ¡PROCESAR ACTUALIZACIÓN!
// =================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error_carga) {
    // 4.1. Recolección y saneamiento de datos
    $nombre_actualizado = trim($_POST['nombre_completo']);
    $email_actualizado = trim($_POST['email']);
    $rol_actualizado = (int)$_POST['id_rol'];
    $estado_actualizado = (int)$_POST['estado'];

    // 4.2. Validación simple
    if (empty($nombre_actualizado) || empty($email_actualizado)) {
        $mensaje_error = "El nombre y el correo electrónico no pueden estar vacíos.";
    } else {
        // 4.3. Llamada al Modelo para actualizar
        $actualizacion_exitosa = ActualizarUsuario(
            $id_usuario,
            $nombre_actualizado,
            $email_actualizado,
            $rol_actualizado,
            $estado_actualizado
        );

        if ($actualizacion_exitosa) {
            $mensaje_exito = "¡Usuario actualizado correctamente!";

            // Recargar los datos del usuario desde la BD 
            // para que el formulario se actualice con los nuevos valores guardados.
            $usuario_a_editar = ObtenerUsuarioPorId($id_usuario);
        } else {
            $mensaje_error = "Hubo un error al intentar guardar los cambios en la base de datos. Revise el log.";
        }
    }
}

// =================================================================
// 5. VISTA (HTML)
// =================================================================
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
        /* --- ESTILOS DE LAYOUT --- */
        :root {
            --secondary-color: #7b4397;
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
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 0;
            width: calc(100% - var(--sidebar-width));
        }

        .top-navbar {
            background: linear-gradient(to right, #4c4a8a, var(--secondary-color));
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
        }

        .menu-item.active a {
            background-color: var(--bg-light);
            color: var(--secondary-color);
            border-left: 3px solid var(--secondary-color);
            font-weight: 600;
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

        <div class="main-content">
            <nav class="top-navbar navbar navbar-expand-md py-3">
                <div class="container-fluid">
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

                <h1 class="mb-4"><?php echo $titulo_pagina; ?> - ID: <?php echo $id_usuario; ?></h1>
                <a href="gestion_usuarios.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left me-1"></i> Volver a Usuarios</a>

                <div class="card shadow-sm p-4">

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

                    <?php if ($error_carga): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error_carga; ?>
                        </div>
                    <?php else: ?>

                        <form method="POST" action="editar_usuario.php?id=<?php echo $id_usuario; ?>">

                            <div class="mb-3">
                                <label for="nombre_completo" class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" id="nombre_completo" name="nombre_completo"
                                    value="<?php echo htmlspecialchars($usuario_a_editar->nombre_completo); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo htmlspecialchars($usuario_a_editar->email); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="id_rol" class="form-label">Rol</label>
                                <select class="form-select" id="id_rol" name="id_rol" required>
                                    <?php foreach ($roles as $rol): ?>
                                        <option value="<?php echo $rol->Id_Rol; ?>"
                                            <?php if ($rol->Id_Rol == $usuario_a_editar->id_rol) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($rol->nombre_rol); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="1" <?php if ($usuario_a_editar->estado == 1) echo 'selected'; ?>>Activo</option>
                                    <option value="0" <?php if ($usuario_a_editar->estado == 0) echo 'selected'; ?>>Inactivo</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Guardar Cambios
                            </button>
                        </form>

                    <?php endif; ?>

                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>