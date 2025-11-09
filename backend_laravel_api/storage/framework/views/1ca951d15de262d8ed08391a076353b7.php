

<?php $__env->startSection('title', 'Gestión de Usuarios'); ?>

<?php $__env->startSection('page-actions'); ?>
    <a href="#" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>
        Agregar Nuevo Usuario
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card user-management-container">
            <div class="card-header bg-white d-flex justify-content-between align-items-center card-header-border">
                <h5 class="card-title mb-0 card-title-styled">
                    <i class="bi bi-people me-2 card-icon-primary"></i>
            Todos los Usuarios (<?php echo e($users->total()); ?>)
        </h5>
        
        <div class="d-flex gap-2">
            <div class="input-group search-input-group">
                <span class="input-group-text search-input-text">
                    <i class="bi bi-search search-icon"></i>
                </span>
                <input type="text" class="form-control search-input" placeholder="Buscar usuarios...">
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <?php if($users->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="table-col-id">#</th>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th>Puntos</th>
                            <th>Registrado</th>
                            <th>Estado</th>
                            <th class="table-col-actions">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="table-text-muted-weight"><?php echo e($user->id); ?></td>
                                <td>
                                    <div>
                                        <div class="user-name-primary"><?php echo e($user->full_name); ?></div>
                                        <div class="user-id-secondary"><?php echo e($user->username); ?></div>
                                    </div>
                                </td>
                                <td class="table-text-muted-weight"><?php echo e($user->email); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-primary fw-bold px-2 py-1">
                                        <?php echo e($user->points ?? 0); ?> pts
                                    </span>
                                </td>
                                <td class="table-text-muted"><?php echo e($user->created_at->format('M d, Y')); ?></td>
                                <td>
                                    <?php if($user->email_verified_at): ?>
                                        <span class="badge badge-success-custom">
                                            <i class="bi bi-check-circle me-1"></i>Verificado
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-warning-custom">
                                            <i class="bi bi-exclamation-circle me-1"></i>Pendiente
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo e(route('admin.users.show', $user->id)); ?>" 
                                           class="btn btn-sm btn-link text-primary p-1" 
                                           title="Ver usuario">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" 
                                           class="btn btn-sm btn-link text-secondary p-1" 
                                           title="Editar usuario">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="<?php echo e(route('admin.users.delete', $user->id)); ?>" 
                                              class="d-inline" 
                                              onsubmit="return confirm('¿Estás seguro de que quieres eliminar este usuario?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" 
                                                    class="btn btn-sm btn-link text-danger p-1" 
                                                    title="Eliminar usuario">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if($users->hasPages()): ?>
                <div class="card-footer bg-white card-footer-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="pagination-info">
                            Mostrando <?php echo e($users->firstItem()); ?> a <?php echo e($users->lastItem()); ?> de <?php echo e($users->total()); ?> resultados
                        </div>
                        <div>
                            <?php echo e($users->links()); ?>

                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-5 empty-state-text">
                <i class="bi bi-people empty-state-icon-large"></i>
                <h5 class="mt-3 empty-state-title">No se encontraron usuarios</h5>
                <p class="mb-4">Aún no hay usuarios en el sistema.</p>
                <a href="#" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    Agregar Primer Usuario
                </a>
            </div>
        <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ashraf\Desktop\pj\ByCrousty\backend_laravel_api\resources\views/admin/users/index.blade.php ENDPATH**/ ?>