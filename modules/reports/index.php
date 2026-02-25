<?php
require_once __DIR__ . '/../../config/config.php';

$pageTitle = 'Reportes y Estadísticas';

// Obtener datos para reportes
$stats = Dashboard::getStatistics();
$usersByRole = Dashboard::getUsersByRole();
$usersByMonth = Dashboard::getUsersByMonth();
$recentUsers = Dashboard::getRecentUsers(10);

require_once INCLUDES_PATH . '/header.php';
?>

<div class="row fade-in">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold"><?php echo $pageTitle; ?></h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Resumen General -->
                    <div class="col-md-3">
                        <div class="card stat-card primary mb-3">
                            <div class="card-body">
                                <div class="stat-title">Total Usuarios</div>
                                <div class="stat-value"><?php echo number_format($stats['users']); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card stat-card success mb-3">
                            <div class="card-body">
                                <div class="stat-title">Usuarios Activos</div>
                                <div class="stat-value"><?php echo number_format($stats['active_users']); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card stat-card info mb-3">
                            <div class="card-body">
                                <div class="stat-title">Nuevos (Mes)</div>
                                <div class="stat-value"><?php echo number_format($stats['new_users']); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card stat-card warning mb-3">
                            <div class="card-body">
                                <div class="stat-title">Administradores</div>
                                <div class="stat-value"><?php echo number_format($stats['admins']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row fade-in">
    <!-- Gráfico de Usuarios por Mes -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold">Tendencia de Usuarios</h6>
            </div>
            <div class="card-body">
                <div style="height: 350px;">
                    <canvas id="usersTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Distribución por Rol -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold">Distribución por Rol</h6>
            </div>
            <div class="card-body">
                <div style="height: 350px;">
                    <canvas id="roleDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row fade-in">
    <!-- Tabla de Usuarios Recientes -->
    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold">Últimos Usuarios Registrados</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-custom table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Fecha Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentUsers)): ?>
                                <?php foreach ($recentUsers as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge badge-info"><?php echo ucfirst($user['role']); ?></span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">
                                        No hay usuarios registrados
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// JavaScript para gráficos
$chartLabels = [];
$chartData = [];
foreach (array_slice(array_reverse($usersByMonth), 0, 6) as $data) {
    $months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
               'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    $chartLabels[] = $months[$data['month'] - 1];
    $chartData[] = $data['count'];
}

$roleLabels = [];
$roleData = [];
$roleColors = [];
$colorIndex = 0;
$colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];
foreach ($usersByRole as $role) {
    $roleLabels[] = ucfirst($role['role']);
    $roleData[] = $role['count'];
    $roleColors[] = $colors[$colorIndex % count($colors)];
    $colorIndex++;
}

$customJS = <<<EOT
// Gráfico de tendencia de usuarios
const trendCtx = document.getElementById('usersTrendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio'],
        datasets: [{
            label: 'Usuarios Nuevos',
            data: ['12', '19', '3', '5', '2', '3'],
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});

// Gráfico de distribución por rol
const distributionCtx = document.getElementById('roleDistributionChart').getContext('2d');
new Chart(distributionCtx, {
    type: 'doughnut',
    data: {
        labels: ['Administradores', 'Usuarios'],
        datasets: [{
            data: [1, 2],
            backgroundColor: ['#4e73df', '#1cc88a'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
EOT;

require_once INCLUDES_PATH . '/footer.php';
?>