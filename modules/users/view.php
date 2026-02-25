<?php
require_once __DIR__ . '/../../config/config.php';

$pageTitle = 'Ver Usuario';
Auth::requireRole('admin');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user = User::find($id);

if (!$user) {
    header('Location: index.php?error=user_not_found');
    exit();
}

require_once INCLUDES_PATH . '/header.php';
?>

<div class="row fade-in">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold"><?php echo $pageTitle; ?></h6>
                <a href="index.php" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>ID:</strong>
                    </div>
                    <div class="col-md-6">
                        <?php echo $user['id']; ?>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <strong>Nombre Completo:</strong>
                    </div>
                    <div class="col-md-6">
                        <?php echo htmlspecialchars($user['name']); ?>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <strong>Email:</strong>
                    </div>
                    <div class="col-md-6">
                        <?php echo htmlspecialchars($user['email']); ?>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <strong>Rol:</strong>
                    </div>
                    <div class="col-md-6">
                        <span class="badge badge-info"><?php echo ucfirst($user['role']); ?></span>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <strong>Estado:</strong>
                    </div>
                    <div class="col-md-6">
                        <span class="badge badge-<?php echo $user['status'] ? 'success' : 'danger'; ?>">
                            <?php echo $user['status'] ? 'Activo' : 'Inactivo'; ?>
                        </span>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <strong>Fecha de Registro:</strong>
                    </div>
                    <div class="col-md-6">
                        <?php echo date('d/m/Y H:i:s', strtotime($user['created_at'])); ?>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <strong>Último Acceso:</strong>
                    </div>
                    <div class="col-md-6">
                        <?php echo $user['last_login'] ? date('d/m/Y H:i:s', strtotime($user['last_login'])) : 'Nunca'; ?>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-12">
                        <a href="edit.php?id=<?php echo $user['id']; ?>" class="btn btn-custom btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                        <a href="delete.php?id=<?php echo $user['id']; ?>" 
                           class="btn btn-custom btn-danger btn-delete" 
                           data-message="¿Estás seguro de que deseas eliminar al usuario <?php echo htmlspecialchars($user['name']); ?>?">
                            <i class="fas fa-trash"></i> Eliminar
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once INCLUDES_PATH . '/footer.php';
?>