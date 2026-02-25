<?php

require_once __DIR__ . '/../../config/config.php';

$pageTitle = 'Iniciar Sesión';

$error = '';
$success = '';

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Por favor completa todos los campos';
    } else {
        if (Auth::login($email, $password)) {
            header('Location: ' . APP_URL . '/index.php');
            exit();
        } else {
            $error = 'Email o contraseña incorrectos';
        }
    }
}

// Verificar si ya está logueado
if (Auth::check()) {
    header('Location: ' . APP_URL . '/index.php');
    exit();
}

require_once INCLUDES_PATH . '/header.php';
?>

<div class="login-page">
    <div class="card login-card fade-in">
        <div class="login-header">
            <h2><?php echo APP_NAME; ?></h2>
            <p>Inicia sesión para continuar</p>
        </div>
        
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" data-validate>
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" 
                           class="form-control" 
                           id="email" 
                           name="email" 
                           required
                           placeholder="tu@email.com">
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" 
                           class="form-control" 
                           id="password" 
                           name="password" 
                           required
                           placeholder="••••••••">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-custom btn-primary w-100">
                        <i class="fas fa-sign-in-alt"></i>
                        Iniciar Sesión
                    </button>
                </div>
            </form>
        </div>
        
        <div class="login-footer">
            <p class="text-muted">
                ¿No tienes una cuenta? 
                <a href="register.php" style="color: var(--primary-color);">Regístrate</a>
            </p>
        </div>
    </div>
</div>

<?php 
require_once INCLUDES_PATH . '/footer.php';
?>