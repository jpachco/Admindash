<?php
/**
 * Clase de Autenticación Producción
 */

class Auth {
    /**
     * Verifica si el usuario está autenticado
     */
    public static function check() {
        return isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Obtiene el usuario actual
     */
    public static function user() {
        if (self::check()) {
            return User::find($_SESSION['user_id']);
        }
        return null;
    }
    
    /**
     * Inicia sesión del usuario
     */
    public static function login($email, $password) {
        $db = Database::getConnection();
        
        try {
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND status = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['logged_in'] = true;
                $_SESSION['last_activity'] = time();
                
                // Actualizar último login
                User::updateLastLogin($user['id']);
                
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cierra la sesión del usuario
     */
    public static function logout() {
        $_SESSION = array();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }
    
    /**
     * Verifica el tiempo de sesión
     */
    public static function checkSessionTimeout() {
        if (isset($_SESSION['last_activity'])) {
            $inactive = time() - $_SESSION['last_activity'];
            if ($inactive > SESSION_TIMEOUT) {
                self::logout();
                return false;
            }
            $_SESSION['last_activity'] = time();
        }
        return true;
    }
    
    /**
     * Requiere autenticación para acceder a una página
     */
    public static function requireLogin() {
        if (!self::check()) {
            header('Location: ' . APP_URL . '/modules/home/login.php');
            exit();
        }
        
        if (!self::checkSessionTimeout()) {
            header('Location: ' . APP_URL . '/modules/home/login.php?timeout=1');
            exit();
        }
    }
    
    /**
     * Verifica si el usuario tiene un rol específico
     */
    public static function hasRole($role) {
        if (!self::check()) {
            return false;
        }
        return $_SESSION['user_role'] === $role;
    }
    
    /**
     * Requiere un rol específico para acceder
     */
    public static function requireRole($role) {
        self::requireLogin();
        if (!self::hasRole($role)) {
            header('Location: ' . APP_URL . '/index.php?error=unauthorized');
            exit();
        }
    }
}
?>