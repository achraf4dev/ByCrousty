

<?php $__env->startSection('title', 'Tablero'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <!-- Stats Cards -->
    <div class="col-md-3 mb-3">
        <div class="card stats-card-primary">
            <div class="card-body stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-1">Total de Usuarios</h6>
                        <h4 class="card-title mb-0"><?php echo e($totalUsers); ?></h4>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card-success">
            <div class="card-body stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-1">Total Puntos</h6>
                        <h4 class="card-title mb-0"><?php echo e(number_format($totalPoints)); ?></h4>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-trophy"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card-warning">
            <div class="card-body stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-1">Nuevos Hoy</h6>
                        <h4 class="card-title mb-0"><?php echo e($totalUsers > 5 ? 3 : 1); ?></h4>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-person-plus"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card-purple">
            <div class="card-body stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-1">Actividad Puntos</h6>
                        <h4 class="card-title mb-0"><?php echo e($recentPointsActivity->count()); ?></h4>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-activity"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Users -->
    <div class="col-md-8 mb-3">
        <div class="card">
            <div class="card-header bg-white card-header-compact">
                <h6 class="card-title-compact">
                    <i class="bi bi-people me-2"></i>
                    Usuarios Recientes
                </h6>
            </div>
            <div class="card-body p-0">
                <?php if($recentUsers->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Se Unió</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $recentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <div class="table-user-name"><?php echo e($user->full_name); ?></div>
                                        </td>
                                        <td class="table-text-muted"><?php echo e($user->email); ?></td>
                                        <td class="table-text-muted"><?php echo e($user->created_at->format('M d, Y')); ?></td>
                                        <td>
                                            <?php if($user->email_verified_at): ?>
                                                <span class="badge status-badge active">
                                                    <i class="bi bi-check-circle me-1"></i>Activo
                                                </span>
                                            <?php else: ?>
                                                <span class="badge status-badge pending">
                                                    <i class="bi bi-exclamation-circle me-1"></i>Pendiente
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 empty-state-text">
                        <i class="bi bi-people empty-state-icon"></i>
                        <p class="mt-3 mb-0">No se encontraron usuarios</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-header bg-white card-header-compact">
                <h6 class="card-title-compact">
                    <i class="bi bi-lightning me-2"></i>
                    Acciones Rápidas
                </h6>
            </div>
            <div class="card-body card-body-compact">
                <div class="d-grid gap-2 action-btn-group">
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-people me-2"></i>
                        Gestionar Usuarios
                    </a>
                    <a href="<?php echo e(route('admin.qr-scanner')); ?>" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-camera me-2"></i>
                        Escáner Cámara
                    </a>
                    <a href="<?php echo e(route('admin.points-history')); ?>" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-trophy me-2"></i>
                        Historial de Puntos
                    </a>
                    <button class="btn btn-outline-warning btn-sm">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        Ver Reportes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Points Activity -->
<div class="row">
    <div class="col-md-12 mb-3">
        <div class="card">
            <div class="card-header bg-white card-header-compact">
                <h6 class="card-title-compact">
                    <i class="bi bi-trophy me-2"></i>
                    Actividad Reciente de Puntos
                </h6>
            </div>
            <div class="card-body">
                <?php if($recentPointsActivity->count() > 0): ?>
                    <div class="timeline">
                        <?php $__currentLoopData = $recentPointsActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-icon <?php echo e($activity->points > 0 ? 'timeline-icon-success' : 'timeline-icon-warning'); ?> me-3">
                                    <i class="bi <?php echo e($activity->points > 0 ? 'bi-plus-circle' : 'bi-dash-circle'); ?>"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="mb-1 timeline-content-title">
                                        <strong><?php echo e($activity->admin->full_name); ?></strong> otorgó 
                                        <span class="fw-bold text-primary"><?php echo e($activity->points); ?> puntos</span> 
                                        a <strong><?php echo e($activity->user->full_name); ?></strong>
                                    </p>
                                    <small class="text-muted"><?php echo e($activity->formatted_description); ?></small>
                                    <br>
                                    <small class="timeline-content-date"><?php echo e($activity->created_at->diffForHumans()); ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?php echo e(route('admin.points-history')); ?>" class="btn btn-sm btn-outline-primary">
                            Ver Todo el Historial
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-trophy-fill opacity-50" style="font-size: 2rem;"></i>
                        <p class="mb-0 mt-2">No hay actividad de puntos reciente</p>
                        <a href="<?php echo e(route('admin.qr-scanner')); ?>" class="btn btn-sm btn-primary mt-2">
                            <i class="bi bi-camera me-2"></i>
                            Comenzar a Escanear QR
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- System Info -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white card-header-border">
                <h5 class="card-title mb-0 card-title-styled">
                    <i class="bi bi-info-circle me-2 card-icon-primary"></i>
                    Información del Sistema
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <i class="bi bi-server system-info-server"></i>
                            <h6 class="mt-2 mb-1 system-info-title">Versión Laravel</h6>
                            <p class="text-muted mb-0"><?php echo e(app()->version()); ?></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <i class="bi bi-code-slash system-info-php"></i>
                            <h6 class="mt-2 mb-1 system-info-title">Versión PHP</h6>
                            <p class="text-muted mb-0"><?php echo e(PHP_VERSION); ?></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <i class="bi bi-calendar system-info-time"></i>
                            <h6 class="mt-2 mb-1 system-info-title">Hora del Servidor</h6>
                            <p class="text-muted mb-0"><?php echo e(now()->format('M d, Y H:i')); ?></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <i class="bi bi-shield-check system-info-status"></i>
                            <h6 class="mt-2 mb-1 system-info-title">Estado del Sistema</h6>
                            <p class="text-muted mb-0">
                                <span class="badge badge-status-online">En Línea</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ashraf\Desktop\pj\ByCrousty\backend_laravel_api\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>