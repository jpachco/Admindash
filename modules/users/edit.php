<?php
require_once __DIR__ . '/../../config/config.php';

$pageTitle = 'Editar Usuario';
Auth::requireRole('admin');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user = User::find($id);

if (!$user) {
    header('Location: index.php?error=user_not_found');
    exit();
}

$error = '';
$success = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $status = isset($_POST['status']) ? 1 : 0;
    
    // Validaciones
    if (empty($name) || empty($email)) {
        $error = 'Por favor completa todos los campos requeridos';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Por favor ingresa un email válido';
    } elseif (!empty($password) && strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres';
    } elseif (!empty($password) && $password !== $confirmPassword) {
        $error = 'Las contraseñas no coinciden';
    } elseif (User::emailExists($email, $id)) {
        $error = 'Este email ya está registrado';
    } else {
        // Actualizar usuario
        $userData = [
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'status' => $status
        ];
        
        if (!empty($password)) {
            $userData['password'] = $password;
        }
        
        if (User::update($id, $userData)) {
            $success = 'Usuario actualizado exitosamente';
            header('Location: index.php?success=updated');
            exit();
        } else {
            $error = 'Error al actualizar el usuario';
        }
    }
}

require_once INCLUDES_PATH . '/header.php';
?>

<div class="row fade-in">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold"><?php echo $pageTitle; ?></h6>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" data-validate>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label">Nombre Completo *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       required
                                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : htmlspecialchars($user['name']); ?>"
                                       placeholder="Juan Pérez">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       required
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($user['email']); ?>"
                                       placeholder="usuario@ejemplo.com">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password" class="form-label">Nueva Contraseña</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       placeholder="•••••••• (dejar en blanco para mantener actual)">
                                <small class="text-muted">Dejar en blanco para no cambiar</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="confirm_password" class="form-label">Confirmar Nueva Contraseña</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       data-password-match="#password"
                                       placeholder="••••••••">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role" class="form-label">Rol *</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="user" <?php echo ($user['role'] === 'user') ? 'selected' : ''; ?>>Usuario</option>
                                    <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Administrador</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status" class="form-label">Estado</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="1" <?php echo ($user['status'] == 1) ? 'selected' : ''; ?>>Activo</option>
                                    <option value="0" <?php echo ($user['status'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-custom btn-primary">
                            <i class="fas fa-save"></i> Actualizar Usuario
                        </button>
                        <a href="index.php" class="btn btn-custom btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
require_once INCLUDES_PATH . '/footer.php';
?>