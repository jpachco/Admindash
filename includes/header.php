<?php
require_once __DIR__ . '/../config/config.php';

// Verificar autenticación si no es página de login
if (!strpos($_SERVER['REQUEST_URI'], 'login.php')) {
    Auth::requireLogin();
}

$currentUser = Auth::user();

/**
 * Función robusta para detectar la página activa
 * Compara el final de la URL para evitar falsos positivos
 */
function isActive($targetPath, $isHome = false) {
    // 1. Obtenemos la URL limpia que el usuario ve en el navegador
    $currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // 2. Si es Inicio, solo activamos si es EXACTAMENTE la raíz o index.php
    if ($isHome) {
        // Obtenemos la última parte de la ruta
        $end = basename($currentUri);
        // Es activo si termina en index.php o si la ruta es la raíz del proyecto
        return ($end == 'index.php' || $end == '' || $end == 'public') ? 'active' : '';
    }
    
    // 3. Para los demás módulos (reports, users, etc)
    // Buscamos si el nombre del módulo existe como una "carpeta" en la URL
    return (strpos($currentUri, '/' . $targetPath . '/') !== false) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?><?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&amp;display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
    
    <!-- Custom CSS for this page -->
    <?php if (isset($customCSS)): ?>
    <style>
        <?php echo $customCSS; ?>
    </style>
    <?php endif; ?>
</head>
<body>

<?php if (!strpos($_SERVER['REQUEST_URI'], 'login.php')): ?>
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand text-white" href="<?php echo APP_URL; ?>/index.php">
                <i class="fas fa-tachometer-alt me-2 text-info"></i> HaberHolding
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo isActive('index.php', true); ?>" href="<?php echo APP_URL; ?>/index.php">
                            <i class="fas fa-home"></i> Inicio
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo isActive('reports'); ?>" href="<?php echo APP_URL; ?>/modules/reports/index.php">
                            <i class="fas fa-chart-bar"></i> Reportes
                        </a>
                    </li>
                    
                    <?php if (Auth::hasRole('admin')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo (isActive('monitor') || isActive('users')) ? 'active' : ''; ?>" 
                               href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-cogs"></i> Administración
                            </a>
                            <ul class="dropdown-menu shadow border-0" aria-labelledby="adminDropdown">
                                <li>
                                    <a class="dropdown-item <?php echo isActive('monitor'); ?>" href="<?php echo APP_URL; ?>/modules/monitor/index.php">
                                        <i class="fas fa-database me-2"></i> Monitor
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item <?php echo isActive('users'); ?>" href="<?php echo APP_URL; ?>/modules/users/index.php">
                                        <i class="fas fa-users me-2"></i> Usuarios
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-muted" href="#">
                                        <i class="fas fa-plus me-2"></i> Configuración Pro (Test)
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <?php if (Auth::hasRole('admin')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo (isActive('monitor') || isActive('users')) ? 'active' : ''; ?>" 
                               href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-diagram-project"></i> Procesos
                            </a>
                            <ul class="dropdown-menu shadow border-0" aria-labelledby="adminDropdown">
                                <li>
                                    <a class="dropdown-item <?php echo isActive('monitor'); ?>" href="<?php echo APP_URL; ?>/modules/process/sendgastos.php" <?php echo strpos($_SERVER['REQUEST_URI'], 'Process') !== false ? 'class="active"' : ''; ?>>
                                    <i class="fa-solid fa-file-pdf"></i> Enviar PDF's
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item <?php echo isActive('users'); ?>" href="<?php echo APP_URL; ?>/modules/process/loadPD.php" <?php echo strpos($_SERVER['REQUEST_URI'], 'Process') !== false ? 'class="active"' : ''; ?>>
                                    <i class="fa-solid fa-file-import"></i> Participación Diaria
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="navbar-nav align-items-center">
                    <div class="nav-item dropdown d-flex align-items-center">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($currentUser['name'], 0, 1)); ?>
                        </div>
                        <a class="nav-link dropdown-toggle pe-3 text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo htmlspecialchars($currentUser['name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?php echo APP_URL; ?>/modules/home/logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i> Salir
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <header class="py-4">
                    <h3 class="fw-bold"><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/index.php" class="text-decoration-none">Home</a></li>
                            <?php if (isset($pageTitle) && $pageTitle !== 'Dashboard'): ?>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo $pageTitle; ?></li>
                            <?php endif; ?>
                        </ol>
                    </nav>
                </header>
                
                <main class="main-content">
<?php endif; ?>