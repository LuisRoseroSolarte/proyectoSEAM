<?php
// === controllers/logout.php ===

// 1. Asegurarse de que la sesión esté iniciada para poder destruirla.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Destruir las variables de sesión.
$_SESSION = array();

// 3. Destruir la sesión por completo (incluyendo la cookie).
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 4. Finalmente, destruir la sesión.
session_destroy();

// 5. Redirigir al usuario a la página de login.
// Usamos la ruta correcta: /public/php/login.php
header("Location: ../public/php/login.php");
exit;
