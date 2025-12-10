<?php
// === public/login.php ===
// VISTA: Actualizada para usar la estructura del diseño (Mockup).
session_start();

$mensaje_error_de_login = '';
if (isset($_SESSION['error_message'])) {
    $mensaje_error_de_login = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema SEAM Ingeniería</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-login-fondo">

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row w-100">
            <div class="col-12 col-md-8 col-lg-6 col-xl-4 mx-auto">

                <div class="card card-login shadow-lg p-4 p-md-5">
                    <div class="card-body">

                        <div class="text-center mb-4">
                            <i class="fa-solid fa-circle-user fa-3x text-primary icono-avatar"></i>
                            <h2 class="mt-3 titulo-login">2. Iniciar Sesión SEAM</h2>
                        </div>

                        <?php
                        if (!empty($mensaje_error_de_login)) {
                            echo '<div class="alert alert-danger text-center" role="alert">';
                            echo htmlspecialchars($mensaje_error_de_login);
                            echo '</div>';
                        }
                        ?>

                        <form action="/proyectoSEAM/controllers/autenticar.php" method="POST">

                            <div class="usuario">
                                <label for="email" class="form-label label-login">
                                    <i class="fa-solid fa-user-alt icono-label"></i> Usuario:
                                </label>

                                <input type="email" id="email" name="email" class="form-control " required placeholder="Ingresa tu usuario" autofocus>
                            </div>

                            <div class="contrasena">
                                <label for="password" class="form-label label-login">
                                    <i class="fa-solid fa-lock icono-label "></i> Contraseña:
                                </label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control" required placeholder="Ingresa tu contraseña" minlength="6">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fa-solid fa-eye" id="eyeIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-acceder w-100 py-2 mb-3">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i> Acceder
                            </button>

                            <a href="../index.html" class="btn btn-regresar w-100 py-2 ">
                                <i class="fa-solid fa-arrow-left"></i> Regresar
                            </a>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function(e) {
            // Alterna el tipo de input entre 'password' y 'text'
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // Cambia el icono de ojo abierto a ojo cerrado
            eyeIcon.classList.toggle('fa-eye-slash');
        });
    </script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>