<?php
/**
 * Configuración General del Sistema
 */

// Configuración del sistema
define('APP_NAME', 'Admindash');
define('APP_VERSION', '1.0.0');
define('APP_URL', '/Admindash');

// Zona horaria
date_default_timezone_set('America/Mexico_City');

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en producción con HTTPS

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1); // Cambiar a 0 en producción

// Rutas del sistema
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('CLASSES_PATH', ROOT_PATH . '/classes');
define('MODULES_PATH', ROOT_PATH . '/modules');
define('ASSETS_PATH', ROOT_PATH . '/assets');

// Configuración de autenticación
define('SESSION_TIMEOUT', 3600); // 1 hora
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutos

// Configuración de paginación
define('ITEMS_PER_PAGE', 10);

// Configuración de archivos
define('MAX_FILE_SIZE', 5242880); // 5MB en bytes
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir archivos necesarios
require_once CONFIG_PATH . '/database.php';
require_once CLASSES_PATH . '/Auth.php';
require_once CLASSES_PATH . '/User.php';
require_once CLASSES_PATH . '/Dashboard.php';
?>