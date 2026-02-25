<?php
/**
 * Gestión Multiconexión SQL Server
 * Sistema de Dashboard PHP - Edición Ejecutiva
 */

class Database {
    // Registro de conexiones activas
    private static $instances = [];

    // Diccionario de configuraciones (Fácil de mantener)
    private static $configs = [
        'default' => [
            'host'     => '190.27.1.13',
            'instance' => 'BI',
            'db'       => 'dashboard_db',
            'user'     => 'magil',
            'pass'     => 'Marcoo0'
        ],
        'nav' => [
            'host'     => '172.25.12.12',
            'instance' => 'db_ofa',
            'db'       => 'db_factory',
            'user'     => 'jlortiz',
            'pass'     => 'Escarnobroske2K'
        ],
        'MF' => [
            'host'     => '190.27.1.13',
            'instance' => 'BI',
            'db'       => 'dbdistribucion',
            'user'     => 'magil',
            'pass'     => 'Marcoo0'
        ],
        'RB' => [
            'host'     => '190.27.1.13',
            'instance' => 'BI',
            'db'       => 'dbRoberts',
            'user'     => 'magil',
            'pass'     => 'Marcoo0'
        ],
        'HL' => [
            'host'     => '190.27.1.13',
            'instance' => 'BI',
            'db'       => 'dbhighlife',
            'user'     => 'magil',
            'pass'     => 'Marcoo0'
        ],
        'BG' => [
            'host'     => '172.25.12.26',
            'instance' => 'BI',
            'db'       => 'dbLamberti',
            'user'     => 'magil',
            'pass'     => 'Marcoo0'
        ]
    ];

    /**
     * Obtiene la conexión solicitada
     * @param string $name Nombre de la configuración ('default' o 'nav')
     * @return PDO
     */
    public static function getConnection($name = 'default') {
        // Verificar si la configuración existe
        if (!isset(self::$configs[$name])) {
            throw new Exception("La configuración de base de datos '{$name}' no existe.");
        }

        // Retornar la conexión si ya fue creada (Singleton por nombre)
        if (!isset(self::$instances[$name]) || self::$instances[$name] === null) {
            self::$instances[$name] = self::connect($name);
        }

        return self::$instances[$name];
    }

    /**
     * Proceso interno de conexión
     */
    private static function connect($name) {
        $conf = self::$configs[$name];
        
        try {
            // Construcción del DSN para SQL Server
            $dsn = "sqlsrv:Server=" . $conf['host'] . "\\" . $conf['instance'] . ";Database=" . $conf['db'].";TrustServerCertificate=false;Encrypt=false;";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                // Forzar set de caracteres si es necesario
                PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_UTF8,
            ];

            return new PDO($dsn, $conf['user'], $conf['pass'], $options);

        } catch (PDOException $e) {
            // Error con estilo ejecutivo: informativo pero controlado
            error_log("Error de conexión DB ({$name}): " . $e->getMessage());
            die("Error crítico: No se pudo establecer comunicación con el origen de datos '{$name}'.");
        }
    }

    /**
     * Cierra una conexión específica o todas
     */
    public static function closeConnection($name = null) {
        if ($name) {
            self::$instances[$name] = null;
        } else {
            foreach (self::$instances as $key => $val) {
                self::$instances[$key] = null;
            }
        }
    }
}