<?php
// === controllers/verificar_sesion.php (SOLUCIÓN FINAL DE AUTORIZACIÓN) ===

/**
 * Función que verifica la sesión y el rol del usuario para la autorización.
 * @param array $roles_permitidos Array de IDs de rol permitidos para esta página (ej: [1]).
 */
function verificarAcceso($roles_permitidos = [])
{

    // 1. Iniciar la sesión
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $login_url    = "/proyectoSEAM/public/php/login.php";
    $admin_url    = "/proyectoSEAM/views/administrador/dashboard.php";
    $empleado_url = "/proyectoSEAM/views/empleado/dashboard_empleado.php";

    // A. Comprobar si NO está logueado
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        $_SESSION['error_message'] = "Acceso denegado: Por favor, inicie sesión.";
        header("Location: " . $login_url);
        exit;
    }

    // 🚨 CORRECCIÓN CLAVE: Aseguramos que el rol sea un entero para la comparación.
    $usuario_rol = (int)$_SESSION['rol_id'];

    // B. Comprobar la Autorización (si se especificaron roles)
    if (!empty($roles_permitidos) && !in_array($usuario_rol, $roles_permitidos)) {

        // El usuario está logueado, pero su rol NO está permitido para esta página.
        $_SESSION['error_message'] = "No tiene permiso para acceder a esta sección.";

        // 🚨 LÓGICA DE REDIRECCIÓN ROBUSTA: Enviar al usuario a su dashboard principal.
        $redireccion_url = $login_url; // Default fallback

        if ($usuario_rol === 1) {
            $redireccion_url = $admin_url;
        } elseif ($usuario_rol === 2) {
            $redireccion_url = $empleado_url;
        }

        // Si el rol es 1 o 2, serán enviados a su dashboard. 
        // Si no es ninguno, se mantendrán en el fallback de login.
        header("Location: " . $redireccion_url);
        exit;
    }
}
