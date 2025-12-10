<?php

// Asegúrate de incluir tu archivo de conexión a la base de datos
require_once __DIR__ . '/../config/conexion.php';

// =========================================================================
// FUNCIÓN: OBTENER TODOS LOS CLIENTES
// =========================================================================

/**
 * Obtiene todos los registros de clientes desde la base de datos.
 * Ajustado según el esquema del diagrama (idCliente, nombreCompleto, etc.).
 * * @return array Un array de objetos (stdClass) con los datos de los clientes, o un array vacío en caso de error o si no hay clientes.
 */
function ObtenerTodosLosClientes()
{
    try {
        $pdo = Conexion::obtenerInstancia()->obtenerPDO();

        // La consulta usa los nombres de columna del diagrama UML: idCliente, nombreCompleto, etc.
        $sql = "SELECT idCliente, nombreCompleto, email, telefono, direccion, estado, fechaCreacion, fechaUltimaModificacion 
                FROM cliente
                ORDER BY idCliente DESC"; // Usamos 'idCliente' para ordenar

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        // Devuelve todos los resultados como un array de objetos
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        // En caso de error de conexión o SQL, se registra y se devuelve un array vacío.
        error_log("Error al obtener todos los clientes: " . $e->getMessage());
        return [];
    }
}


// Aquí se añadirán futuras funciones: CrearNuevoCliente, ObtenerClientePorId, ActualizarCliente, EliminarCliente.
