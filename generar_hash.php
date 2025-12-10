<?php
// Script para generar un Hash seguro para la base de datos.

$password_original = 'empleado123'; // <-- Contraseña que vas a usar en el login

// Genera el Hash con el algoritmo seguro (Bcrypt)
$hash_seguro = password_hash($password_original, PASSWORD_DEFAULT);

echo "Contraseña de prueba: <b>" . htmlspecialchars($password_original) . "</b><br>";
echo "COPIA ESTE HASH: <b>" . htmlspecialchars($hash_seguro) . "</b><br>";

// Ejemplo del Hash que debes copiar: $2y$10$tM3.T7Zf.jF0gWJqF4z/hO7DqQWw.S3qJqYFw0b3G.4E.5F.aR1G
