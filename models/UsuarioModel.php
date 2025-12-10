<?php

// 🚨 IMPORTANTE: Verifica que este nombre de archivo sea EXACTAMENTE el que tienes en config/
require_once __DIR__ . '/../config/conexion.php';

/**
 * Busca un usuario en la base de datos por su dirección de correo electrónico,
 * uniendo las tablas USUARIO y CREDENCIALES para obtener todos los datos.
 *
 * @param string $email El correo electrónico del usuario a buscar.
 * @return array|null Un array asociativo con los datos del usuario si se encuentra, o null en caso de no encontrarlo o de error.
 */
function BuscarUsuarioPorLogin($email)
{
    // Asegúrate de que los métodos y la clase coincidan con la capitalización en conexion.php
    $pdo = Conexion::obtenerInstancia()->obtenerPDO();

    try {
        // La consulta utiliza los nombres de columna confirmados: C.email y C.contraseña
        $sql_consulta = "
            SELECT 
                U.Id_Usuario, 
                U.nombre_completo, 
                U.id_rol, 
                U.estado,
                C.contraseña,   -- Columna de hash en tu BD
                C.email         -- Columna de correo en tu BD
            FROM 
                USUARIO U
            INNER JOIN 
                CREDENCIALES C ON U.Id_Usuario = C.Id_Usuario
            WHERE 
                C.email = :email; -- Búsqueda por la columna 'email'
        ";

        $sentencia = $pdo->prepare($sql_consulta);

        // Vinculamos el parámetro :email
        $sentencia->bindParam(':email', $email, PDO::PARAM_STR);

        // Ejecutamos la consulta en la base de datos
        $sentencia->execute();

        // Obtenemos el resultado (un array asociativo o false)
        $datos_usuario = $sentencia->fetch(PDO::FETCH_ASSOC);

        // Retornamos los datos del usuario (array) o NULL si no se encontró.
        return $datos_usuario;
    } catch (PDOException $e) {
        // En caso de error de SQL, lo registramos para la depuración y retornamos NULL.
        // Esto evita que el error se muestre al usuario final.
        error_log("Error crítico en BuscarUsuarioPorEmail: " . $e->getMessage());
        // En un entorno de producción, puedes dejar esta línea. Para ver el error, 
        // puedes usar temporalmente: die("Error SQL: " . $e->getMessage());
        return null;
    }
}

/**
 * =========================================================================
 * NUEVA FUNCIÓN: OBTENER LISTADO DE USUARIOS (USADA PARA GESTIÓN)
 * =========================================================================
 */

/**
 * Obtiene todos los usuarios, incluyendo su rol y email, para el listado de gestión.
 * Esta función es el "READ" del CRUD de usuarios.
 *
 * @return array Array de objetos de usuario (stdClass) o un array vacío en caso de error.
 */
function ObtenerTodosLosUsuarios()
{
    try {
        $pdo = Conexion::obtenerInstancia()->obtenerPDO();
    } catch (\Throwable $e) {
        // Registrar error de conexión
        error_log("Fallo al obtener conexión PDO: " . $e->getMessage());
        return [];
    }

    try {
        // ¡CONSULTA SQL CORREGIDA! (Usando nombres de tabla en minúsculas y alias coherentes)
        $sql = "SELECT 
                    u.Id_Usuario, 
                    u.nombre_completo, 
                    c.email, 
                    r.nombre_rol, 
                    u.estado
                FROM 
                    usuario u             -- Corregido: USUARIO -> usuario
                INNER JOIN 
                    rol r ON u.id_rol = r.Id_Rol    -- Corregido: ROLES -> rol
                INNER JOIN
                    credenciales c ON u.Id_Usuario = c.Id_Usuario -- Corregido: CREDENCIALES -> credenciales
                ORDER BY 
                    u.Id_Usuario ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        // Si el problema es de nombres de columna, este error aparecerá en el log.
        error_log("Error de consulta SQL en ObtenerTodosLosUsuarios: " . $e->getMessage());
        return [];
    }
}


// =========================================================================
// FUNCIÓN: OBTENER UN USUARIO POR ID (PARA EDITAR)
// =========================================================================

/**
 * Obtiene los detalles completos de un solo usuario basado en su ID,
 * incluyendo el rol actual, para precargar el formulario de edición.
 *
 * @param int $id_usuario El ID único del usuario a buscar.
 * @return stdClass|null Objeto con los datos del usuario o null si no se encuentra.
 */
function ObtenerUsuarioPorId($id_usuario)
{
    try {
        $pdo = Conexion::obtenerInstancia()->obtenerPDO();
    } catch (\Throwable $e) {
        error_log("Fallo de conexión en ObtenerUsuarioPorId: " . $e->getMessage());
        return null;
    }

    try {
        // Consulta para obtener todos los campos necesarios para la edición.
        // Incluye el id_rol (FK) para saber qué rol tiene actualmente.
        $sql = "SELECT 
                    u.Id_Usuario, 
                    u.nombre_completo, 
                    u.id_rol, 
                    u.estado, 
                    c.email
                FROM 
                    usuario u             
                INNER JOIN
                    credenciales c ON u.Id_Usuario = c.Id_Usuario
                WHERE
                    u.Id_Usuario = :id_usuario";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        // Retorna el resultado como un objeto simple (stdClass).
        return $stmt->fetch(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        error_log("Error SQL en ObtenerUsuarioPorId: " . $e->getMessage());
        return null;
    }
}

// =========================================================================
// FUNCIÓN: ACTUALIZAR UN USUARIO
// =========================================================================

/**
 * Actualiza los datos de un usuario en las tablas 'usuario' y 'credenciales'.
 * Esta función usa una Transacción para asegurar que ambas actualizaciones 
 * se realicen correctamente o que ninguna se realice.
 *
 * @param int $id_usuario El ID del usuario a actualizar.
 * @param string $nombre_completo Nuevo nombre.
 * @param string $email Nuevo email.
 * @param int $id_rol Nuevo ID de rol.
 * @param int $estado Nuevo estado (1: Activo, 0: Inactivo).
 * @return bool True si ambas actualizaciones fueron exitosas, false en caso contrario.
 */
function ActualizarUsuario(
    $id_usuario,
    $nombre_completo,
    $email,
    $id_rol,
    $estado
) {
    try {
        $pdo = Conexion::obtenerInstancia()->obtenerPDO();
    } catch (\Throwable $e) {
        error_log("Fallo de conexión en ActualizarUsuario: " . $e->getMessage());
        return false;
    }

    try {
        // 1. INICIAR TRANSACCIÓN (Para asegurar atomicidad)
        $pdo->beginTransaction();

        // 2. ACTUALIZAR TABLA 'usuario'
        $sql_usuario = "UPDATE usuario SET nombre_completo = :nombre, id_rol = :rol, estado = :estado 
                        WHERE Id_Usuario = :id";

        $stmt_usuario = $pdo->prepare($sql_usuario);
        $stmt_usuario->bindParam(':nombre', $nombre_completo);
        $stmt_usuario->bindParam(':rol', $id_rol, PDO::PARAM_INT);
        $stmt_usuario->bindParam(':estado', $estado, PDO::PARAM_INT);
        $stmt_usuario->bindParam(':id', $id_usuario, PDO::PARAM_INT);
        $stmt_usuario->execute();

        // 3. ACTUALIZAR TABLA 'credenciales' (Solo el email en este caso)
        $sql_credenciales = "UPDATE credenciales SET email = :email 
                             WHERE Id_Usuario = :id";

        $stmt_credenciales = $pdo->prepare($sql_credenciales);
        $stmt_credenciales->bindParam(':email', $email);
        $stmt_credenciales->bindParam(':id', $id_usuario, PDO::PARAM_INT);
        $stmt_credenciales->execute();

        // 4. CONFIRMAR TRANSACCIÓN
        $pdo->commit();
        return true; // Éxito en ambas operaciones

    } catch (PDOException $e) {
        // 5. REVERTIR TRANSACCIÓN en caso de fallo en cualquiera de las consultas
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Error SQL al actualizar usuario ID $id_usuario: " . $e->getMessage());
        return false; // Fallo en la actualización
    }
}

// =========================================================================
// FUNCIÓN: OBTENER EL CONTEO TOTAL DE USUARIOS
// =========================================================================

/**
 * Cuenta el número total de registros en la tabla 'usuario'.
 *
 * @return int El número total de usuarios o 0 en caso de fallo.
 */
function ContarTotalUsuarios()
{
    try {
        $pdo = Conexion::obtenerInstancia()->obtenerPDO();
    } catch (\Throwable $e) {
        error_log("Fallo de conexión en ContarTotalUsuarios: " . $e->getMessage());
        return 0;
    }

    try {
        // Usamos COUNT(*) para un conteo eficiente
        $sql = "SELECT COUNT(*) FROM usuario";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        // fetchColumn(0) obtiene el valor de la primera columna (el conteo)
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Error SQL en ContarTotalUsuarios: " . $e->getMessage());
        return 0;
    }
}

// =========================================================================
// FUNCIÓN: ELIMINAR UN USUARIO
// =========================================================================

/**
 * Elimina un usuario de las tablas 'credenciales' y 'usuario' usando una transacción.
 * Se debe eliminar primero de 'credenciales' (la hija) y luego de 'usuario' (la padre)
 * para evitar problemas de clave foránea.
 *
 * @param int $id_usuario El ID del usuario a eliminar.
 * @return bool True si ambas eliminaciones fueron exitosas, false en caso contrario.
 */
function EliminarUsuario($id_usuario)
{
    try {
        $pdo = Conexion::obtenerInstancia()->obtenerPDO();
    } catch (\Throwable $e) {
        error_log("Fallo de conexión en EliminarUsuario: " . $e->getMessage());
        return false;
    }

    try {
        // 1. INICIAR TRANSACCIÓN
        $pdo->beginTransaction();

        // 2. ELIMINAR de la tabla 'credenciales' (Debe ser primero por FK)
        $sql_credenciales = "DELETE FROM credenciales WHERE Id_Usuario = :id";

        $stmt_credenciales = $pdo->prepare($sql_credenciales);
        $stmt_credenciales->bindParam(':id', $id_usuario, PDO::PARAM_INT);
        $stmt_credenciales->execute();

        // 3. ELIMINAR de la tabla 'usuario'
        $sql_usuario = "DELETE FROM usuario WHERE Id_Usuario = :id";

        $stmt_usuario = $pdo->prepare($sql_usuario);
        $stmt_usuario->bindParam(':id', $id_usuario, PDO::PARAM_INT);
        $stmt_usuario->execute();

        // 4. CONFIRMAR TRANSACCIÓN
        $pdo->commit();
        return true; // Éxito en la eliminación

    } catch (PDOException $e) {
        // 5. REVERTIR TRANSACCIÓN en caso de fallo
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Error SQL al eliminar usuario ID $id_usuario: " . $e->getMessage());
        return false; // Fallo en la eliminación
    }
}

// =========================================================================
// FUNCIÓN: CREAR UN NUEVO USUARIO (CON TRANSACCIÓN)
// =========================================================================

/**
 * Crea un nuevo usuario en las tablas 'usuario' y 'credenciales' usando una transacción.
 *
 * @param array $datos_usuario Contiene nombre_completo, email, Id_Rol, estado.
 * @param string $password_plano La contraseña en texto plano.
 * @return bool True si la creación fue exitosa, false en caso contrario.
 */
// =========================================================================
// FUNCIÓN: CREAR UN NUEVO USUARIO (SIN DUPLICIDAD DE EMAIL EN TABLA USUARIO)
// =========================================================================
function CrearNuevoUsuario($datos_usuario, $password_plano)
{
    // ... (Conexión y Hash) ...
    $pdo = Conexion::obtenerInstancia()->obtenerPDO();
    $password_hash = password_hash($password_plano, PASSWORD_DEFAULT);

    try {
        $pdo->beginTransaction();

        // 2. INSERTAR en la tabla 'usuario' (CORRECCIÓN: QUITAMOS LA COLUMNA 'email')
        $sql_usuario = "INSERT INTO usuario (nombre_completo, Id_Rol, estado) 
                        VALUES (:nombre, :rol, :estado)";

        $stmt_usuario = $pdo->prepare($sql_usuario);
        $stmt_usuario->bindParam(':nombre', $datos_usuario['nombre_completo']);
        // $stmt_usuario->bindParam(':email_user', $datos_usuario['email']); // ¡Quitamos este bind!
        $stmt_usuario->bindParam(':rol', $datos_usuario['Id_Rol']);
        $stmt_usuario->bindParam(':estado', $datos_usuario['estado']);
        $stmt_usuario->execute();

        // 3. OBTENER el último ID insertado
        $id_nuevo_usuario = $pdo->lastInsertId();

        // 4. INSERTAR en la tabla 'credenciales' (Aquí sí usamos el 'email' como login ID)
        $sql_credenciales = "INSERT INTO credenciales (Id_Usuario, email, contraseña) 
                             VALUES (:id, :email_login, :hash)";

        $stmt_credenciales = $pdo->prepare($sql_credenciales);
        $stmt_credenciales->bindParam(':id', $id_nuevo_usuario, PDO::PARAM_INT);
        $stmt_credenciales->bindParam(':email_login', $datos_usuario['email']);
        $stmt_credenciales->bindParam(':hash', $password_hash);
        $stmt_credenciales->execute();

        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        // El debug DEBE permanecer para ver el error si persiste.
        die("Error SQL Crítico en Creación: " . $e->getMessage() .
            ". Código de error: " . $e->getCode());
    }
}
// =========================================================================
// FUNCIÓN: OBTENER TODOS LOS ROLES
// =========================================================================

/**
 * Obtiene todos los roles disponibles en la base de datos.
 * @return array Lista de objetos Rol o array vacío en caso de error.
 */
function ObtenerRoles()
{
    try {
        $pdo = Conexion::obtenerInstancia()->obtenerPDO();
        $sql = "SELECT Id_Rol, nombre_rol FROM rol ORDER BY Id_Rol";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        error_log("Error al obtener roles: " . $e->getMessage());
        return [];
    }
}


// ... otras funciones antes ...

// =========================================================================
// FUNCIÓN: BUSCAR USUARIO POR EMAIL (USANDO JOIN A CREDENCIALES)
// =========================================================================
function BuscarUsuarioPorEmail($email)
{
    try {
        $pdo = Conexion::obtenerInstancia()->obtenerPDO();

        // Buscamos el email en la tabla 'credenciales'
        $sql = "SELECT u.Id_Usuario, u.nombre_completo, c.email 
                FROM usuario u
                INNER JOIN credenciales c ON u.Id_Usuario = c.Id_Usuario
                WHERE c.email = :email";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        // Mantenemos el debug activo
        die("Error SQL en Validación de Email: " . $e->getMessage() .
            ". Código de error: " . $e->getCode());
    }
}
