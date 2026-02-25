<?php
require_once __DIR__ . '/../../config/config.php';

$pageTitle = 'Gestión de Usuarios';
Auth::requireRole('admin');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    $users = Dashboard::searchUsers($search, $page);
    $totalUsers = Dashboard::countSearchResults($search);
} else {
    $users = User::all($page);
    $totalUsers = User::count();
}

$totalPages = ceil($totalUsers / ITEMS_PER_PAGE);

require_once INCLUDES_PATH . '/header.php';
?>

<div class="row fade-in">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold"><?php echo $pageTitle; ?></h6>
                <a href="<?php echo APP_URL; ?>/modules/users/create.php" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Usuario
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($search)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Resultados de búsqueda para: <strong><?php echo htmlspecialchars($search); ?></strong>
                        (<?php echo number_format($totalUsers); ?> resultados)
                        <a href="index.php" class="float-end">Limpiar</a>
                    </div>
                <?php endif; ?>
                
                <!-- Buscador -->
                <form action="" method="GET" id="search-form" class="mb-3">
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               id="search-input" 
                               name="search" 
                               placeholder="Buscar usuario por nombre o email..."
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                
                <div class="table-responsive">
                    <table class="table table-custom table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Último Acceso</th>
                                <th>Fecha de Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge badge-info"><?php echo ucfirst($user['role']); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo $user['status'] ? 'success' : 'danger'; ?>">
                                            <?php echo $user['status'] ? 'Activo' : 'Inactivo'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Nunca'; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <a href="view.php?id=<?php echo $user['id']; ?>" 
                                           class="btn btn-sm btn-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit.php?id=<?php echo $user['id']; ?>" 
                                           class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                        <a href="delete.php?id=<?php echo $user['id']; ?>" 
                                           class="btn btn-sm btn-danger btn-delete" 
                                           data-message="¿Estás seguro de que deseas eliminar al usuario <?php echo htmlspecialchars($user['name']); ?>?">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">
                                        No hay usuarios registrados
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php
                        $url = !empty($search) ? "?search=" . urlencode($search) . "&amp;page=$i" : "?page=$i";
                        $class = ($i === $page) ? 'active' : '';
                        ?>
                        <a href="<?php echo $url; ?>" class="<?php echo $class; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php 
require_once INCLUDES_PATH . '/footer.php';
?>