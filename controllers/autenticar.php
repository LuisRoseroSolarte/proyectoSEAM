<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// INCLUSIÓN DE DEPENDENCIAS: Necesitamos el Modelo para interactuar con la Base de Datos
require_once __DIR__ . '/../models/UsuarioModel.php';

// =================================================================
// 1. VALIDACIÓN INICIAL Y RECEPCIÓN DE DATOS
// =================================================================

// Verificar si la solicitud es por POST y si las variables existen.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'], $_POST['password'])) {

    // Sanitización y Asignación de Variables:
    $email_ingresado = trim(htmlspecialchars($_POST['email']));
    // La contraseña ingresada no se sanitiza completamente porque se usará con password_verify
    $password_ingresada = trim($_POST['password']);

    // VALIDACIÓN DE DATOS VACÍOS 
    if (empty($email_ingresado) || empty($password_ingresada)) {

        // Redirigir y guardar un mensaje de error si falta un campo.
        $_SESSION['error_message'] = "Error: Por favor, ingresa tanto el correo como la contraseña.";

        // 🚨 RUTA CORRECTA: /public/php/login.php
        header("Location: ../public/php/login.php");

        exit;
    }

    // 🚨 NUEVA VALIDACIÓN: LONGITUD MÍNIMA DE CONTRASEÑA
    if (strlen($password_ingresada) < 6) {
        $_SESSION['error_message'] = "Error: La contraseña debe tener un mínimo de 6 caracteres.";
        header("Location: ../public/php/login.php");
        exit;
    }

    // =================================================================
    // 2. LÓGICA DE AUTENTICACIÓN (Llamada al Modelo)
    // =================================================================

    // Llamamos al Modelo para buscar al usuario por su email.
    $usuario = BuscarUsuarioPorLogin($email_ingresado);

    // Verificar si el usuario fue encontrado en la BD.
    if ($usuario) {

        // Verificar la Contraseña.
        // Obtenemos el hash almacenado en la columna 'contraseña'
        $hash_almacenado = $usuario['contraseña'];

        if (password_verify($password_ingresada, $hash_almacenado)) {

            // ÉXITO: Autenticación correcta.
            unset($usuario['contraseña']); // Eliminamos el hash por seguridad

            // Crear las variables de sesión.
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $usuario['Id_Usuario'];
            $_SESSION['nombre'] = $usuario['nombre_completo'];
            $_SESSION['rol_id'] = $usuario['id_rol']; // La clave para la redirección.

            // 🚨 MODIFICACIÓN CLAVE: LÓGICA DE REDIRECCIÓN BASADA EN ROL

            if ($_SESSION['rol_id'] == 1) {
                // Rol 1: Administrador -> Redirige al dashboard de administrador
                header("Location: ../views/administrador/dashboard.php");
            } elseif ($_SESSION['rol_id'] == 2) {
                // Rol 2: Empleado -> Redirige al nuevo dashboard de empleado
                header("Location: ../views/empleado/dashboard_empleado.php");
            } else {
                // Rol no reconocido: Fallo de seguridad
                $_SESSION['error_message'] = "Rol de usuario no reconocido. Acceso denegado.";
                header("Location: ../public/php/login.php");
            }

            exit;
        } else {
            // FALLO DE CONTRASEÑA: La contraseña no coincide.
            $error = "Credenciales incorrectas: Contraseña no válida.";
        }
    } else {
        // FALLO DE USUARIO: El email no existe en la base de datos.
        $error = "Credenciales incorrectas: El usuario no existe.";
    }
} else {
    // ACCESO DIRECTO: Si alguien intenta acceder a autenticar.php directamente por URL (GET).
    $error = "Acceso no autorizado al controlador.";
}

// =================================================================
// 3. GESTIÓN DE ERRORES Y REDIRECCIÓN FINAL
// =================================================================

// Si existe alguna variable $error, la guardamos en la sesión.
if (isset($error)) {
    $_SESSION['error_message'] = $error;
}

// Redireccionar de vuelta al formulario de login.
// 🚨 RUTA CORRECTA: /public/php/login.php
header("Location: ../public/php/login.php");
exit;
