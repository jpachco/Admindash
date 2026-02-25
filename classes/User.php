<?php
/**
 * Clase de Usuarios
 */

class User {
    /**
     * Obtiene todos los usuarios
     */
    public static function all($page = 1, $limit = ITEMS_PER_PAGE) {
        $db = Database::getConnection();
        $offset = ($page - 1) * $limit;
        
        try {
            $stmt = $db->prepare("SELECT id, name, email, role, status, created_at, last_login 
                                  FROM users 
                                  ORDER BY created_at DESC 
                                  OFFSET ? ROWS FETCH NEXT ? ROWS ONLY");
            $stmt->execute([$offset, $limit]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error fetching users: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene un usuario por ID
     */
    public static function find($id) {
        $db = Database::getConnection();
        
        try {
            $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Error finding user: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Crea un nuevo usuario
     */
    public static function create($data) {
        $db = Database::getConnection();
        
        try {
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $stmt = $db->prepare("INSERT INTO users (name, email, password, role, status, created_at) 
                                  VALUES (?, ?, ?, ?, ?, GETDATE())");
            $stmt->execute([
                $data['name'],
                $data['email'],
                $hashedPassword,
                $data['role'],
                $data['status']
            ]);
            
            return $db->lastInsertId();
        } catch (Exception $e) {
            error_log("Error creating user: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualiza un usuario
     */
    public static function update($id, $data) {
        $db = Database::getConnection();
        
        try {
            if (isset($data['password']) && !empty($data['password'])) {
                $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, password = ?, role = ?, status = ? 
                                      WHERE id = ?");
                $stmt->execute([
                    $data['name'],
                    $data['email'],
                    $hashedPassword,
                    $data['role'],
                    $data['status'],
                    $id
                ]);
            } else {
                $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, role = ?, status = ? 
                                      WHERE id = ?");
                $stmt->execute([
                    $data['name'],
                    $data['email'],
                    $data['role'],
                    $data['status'],
                    $id
                ]);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Error updating user: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina un usuario
     */
    public static function delete($id) {
        $db = Database::getConnection();
        
        try {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return true;
        } catch (Exception $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualiza el último login del usuario
     */
    public static function updateLastLogin($id) {
        $db = Database::getConnection();
        
        try {
            $stmt = $db->prepare("UPDATE users SET last_login = GETDATE() WHERE id = ?");
            $stmt->execute([$id]);
        } catch (Exception $e) {
            error_log("Error updating last login: " . $e->getMessage());
        }
    }
    
    /**
     * Cuenta el total de usuarios
     */
    public static function count() {
        $db = Database::getConnection();
        
        try {
            $stmt = $db->query("SELECT COUNT(*) as total FROM users");
            $result = $stmt->fetch();
            return $result['total'];
        } catch (Exception $e) {
            error_log("Error counting users: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Verifica si el email existe
     */
    public static function emailExists($email, $excludeId = null) {
        $db = Database::getConnection();
        
        try {
            if ($excludeId) {
                $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$email, $excludeId]);
            } else {
                $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
            }
            
            return $stmt->fetch() !== false;
        } catch (Exception $e) {
            error_log("Error checking email: " . $e->getMessage());
            return false;
        }
    }
}
?>