

<?php $__env->startSection('title', 'Historial de Puntos'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white card-header-border">
                <h5 class="card-title mb-0 card-title-styled">
                    <i class="bi bi-trophy me-2 card-icon-primary"></i>
                    Historial Completo de Puntos (<?php echo e($pointsHistory->total()); ?> transacciones)
                </h5>
            </div>
            <div class="card-body">
                <?php if($pointsHistory->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Puntos</th>
                                    <th>Descripción</th>
                                    <th>Otorgado por</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $pointsHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-3">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    <?php echo e(substr($history->user->full_name, 0, 1)); ?>

                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-semibold"><?php echo e($history->user->full_name); ?></div>
                                                <small class="text-muted"><?php echo e($history->user->email); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo e($history->points > 0 ? 'bg-success' : 'bg-warning'); ?> fw-bold px-3 py-2">
                                            <?php echo e($history->points > 0 ? '+' : ''); ?><?php echo e($history->points); ?> pts
                                        </span>
                                    </td>
                                    <td>
                                        <div><?php echo e($history->formatted_description); ?></div>
                                        <?php if($history->qr_code_data): ?>
                                            <small class="text-muted">
                                                <i class="bi bi-qr-code me-1"></i>
                                                Via QR Code
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-2">
                                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 30px; height: 30px; font-size: 12px;">
                                                    <?php echo e(substr($history->admin->full_name, 0, 1)); ?>

                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-semibold"><?php echo e($history->admin->full_name); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div><?php echo e($history->created_at->format('M j, Y')); ?></div>
                                        <small class="text-muted"><?php echo e($history->created_at->format('g:i A')); ?></small>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Mostrando <?php echo e($pointsHistory->firstItem()); ?> a <?php echo e($pointsHistory->lastItem()); ?> 
                            de <?php echo e($pointsHistory->total()); ?> transacciones
                        </div>
                        <?php echo e($pointsHistory->links()); ?>

                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-trophy-fill text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        <h5 class="text-muted mt-3">No hay historial de puntos</h5>
                        <p class="text-muted">Aún no se han otorgado puntos a ningún usuario.</p>
                        <a href="<?php echo e(route('admin.qr-scanner')); ?>" class="btn btn-primary">
                            <i class="bi bi-qr-code-scan me-2"></i>
                            Comenzar a Otorgar Puntos
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card stats-card stats-card-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-subtitle">Total Puntos Otorgados</div>
                        <div class="card-title"><?php echo e($pointsHistory->sum('points')); ?></div>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-trophy"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stats-card stats-card-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-subtitle">Transacciones Hoy</div>
                        <div class="card-title"><?php echo e($pointsHistory->where('created_at', '>=', today())->count()); ?></div>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stats-card stats-card-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-subtitle">Via QR Code</div>
                        <div class="card-title"><?php echo e($pointsHistory->whereNotNull('qr_code_data')->count()); ?></div>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-qr-code"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stats-card stats-card-purple text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-subtitle">Usuarios con Puntos</div>
                        <div class="card-title"><?php echo e($pointsHistory->pluck('user_id')->unique()->count()); ?></div>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ashraf\Desktop\pj\ByCrousty\backend_laravel_api\resources\views/admin/points-history.blade.php ENDPATH**/ ?>