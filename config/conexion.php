<?php
// =================================================================
// 1. DEFINICIÓN DE CONSTANTES (Parámetros de conexión)
// =================================================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_seam'); // DB_NAME: Nombre exacto de la base de datos (Ej: seam_db).

define('DB_USER', 'root'); // DB_USER: Nombre del usuario de MySQL.

define('DB_PASS', ''); // DB_PASS: Contraseña del usuario de MySQL.

// DSN (Data Source Name): Cadena de conexión específica para PDO y MySQL.
// Esto le dice a PDO qué motor usar y dónde está.

define('DB_DNS', 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8');

// =================================================================
// 2. CLASE DE CONEXIÓN PDO (Buenas Prácticas OOP)
// =================================================================

class conexion
{

    private static $instancia = null; // (1) Propiedad estática para mantener la única instancia de conexión (Patrón Singleton Simple).
    private $pdo; //Propiedad que almacenará el objeto de conexión PDO

    private function __construct()
    {
        try {
            $this->pdo = new PDO(DB_DNS, DB_USER, DB_PASS); // Intentamos crear la conexión PDO.

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Asegura que PDO lance excepciones ante errores de SQL (más fácil de debuggear).

            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Devuelve filas como arrays asociativos por defecto (más práctico).

        } catch (PDOException $e) { // La clase PDOException maneja errores de conexión.
            die('error critico d conexion: ' . $e->getMessage());
        }
    }

    //Método Estático (Singleton): Crea una ÚNICA instancia de la conexión si aún no existe.
    public static function ObtenerInstancia()
    {
        if (self::$instancia === null) {
            // Si no hay instancia, crea una nueva (llama al constructor privado).
            self::$instancia = new self();
        }
        // Devuelve la instancia única.
        return self::$instancia;
    }

    //Método para Obtener el Objeto PDO: Retorna el objeto $this->pdo para hacer consultas.
    public function ObtenerPDO()
    {
        return $this->pdo;
    }
}
