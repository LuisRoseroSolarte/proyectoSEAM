<?php
// === views/empleado/dashboard_empleado.php ===

// 1. Incluir el script de verificación.
// Ajuste de la ruta: Subimos dos niveles (../) para salir de /empleado/ y salir de /views/.
require_once __DIR__ . '/../../controllers/verificar_sesion.php';


// 2. Llamar la función, permitiendo SOLO el Rol 2 (Empleado)
verificarAcceso([2]);


// 2. Lógica para restringir el acceso solo al rol de Empleado (id_rol = 2)
if ($_SESSION['rol_id'] != 2) {
    // Si no es Empleado, redirigir al dashboard por defecto (Admin)
    header("Location: ../administrador/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Empleado SEAM</title>
</head>

<body>
    <h1>Bienvenido, Empleado: <?php echo htmlspecialchars($_SESSION['nombre']); ?></h1>
    <h2>Panel de Control para Empleados (Acceso Restringido)</h2>
    <p>Aquí se mostrarán las órdenes, tickets o tareas específicas para el empleado.</p>

    <p><a href="../../controllers/logout.php">Cerrar Sesión</a></p>
</body>

</html>