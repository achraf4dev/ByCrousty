

<?php $__env->startSection('title', 'Ver Categoría'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-8 col-md-10 mx-auto">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-folder me-2"></i>
                    <?php echo e($category->name); ?>

                </h5>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('admin.categories.edit', $category)); ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil me-2"></i>
                        Editar
                    </a>
                    <a href="<?php echo e(route('admin.categories.index')); ?>" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left me-2"></i>
                        Volver
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">                   
                    <div class="col-md-12">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 30%;">ID:</th>
                                <td><?php echo e($category->id); ?></td>
                            </tr>
                            <tr>
                                <th>Nombre:</th>
                                <td><?php echo e($category->name); ?></td>
                            </tr>
                            <tr>
                                <th>Slug:</th>
                                <td><span class="badge bg-light text-dark"><?php echo e($category->slug); ?></span></td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td>
                                    <span class="badge <?php echo e($category->status ? 'bg-success' : 'bg-secondary'); ?>">
                                        <?php echo e($category->status ? 'Activa' : 'Inactiva'); ?>

                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Total Productos:</th>
                                <td>
                                    <span class="badge bg-info"><?php echo e($category->products->count()); ?> productos</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Fecha Creación:</th>
                                <td><?php echo e($category->created_at->format('d/m/Y H:i')); ?></td>
                            </tr>
                            <tr>
                                <th>Última Actualización:</th>
                                <td><?php echo e($category->updated_at->format('d/m/Y H:i')); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <?php if($category->description): ?>
                    <div class="mt-4">
                        <h6>Descripción:</h6>
                        <p class="text-muted"><?php echo e($category->description); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Products in this category -->
        <div class="card mt-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <i class="bi bi-box-seam me-2"></i>
                    Productos en esta categoría
                </h6>
                <a href="<?php echo e(route('admin.products.create', ['category' => $category->id])); ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-2"></i>
                    Agregar Producto
                </a>
            </div>
            <div class="card-body">
                <?php if($category->products->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Precio</th>
                                    <th>Puntos</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $category->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold"><?php echo e($product->name); ?></div>
                                            <small class="text-muted"><?php echo e(Str::limit($product->description, 40)); ?></small>
                                        </td>
                                        <td>
                                            <?php if($product->price): ?>
                                                <span class="fw-semibold">€<?php echo e(number_format($product->price, 2)); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">Gratis</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark"><?php echo e($product->points); ?> pts</span>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo e($product->status ? 'bg-success' : 'bg-secondary'); ?>">
                                                <?php echo e($product->status ? 'Activo' : 'Inactivo'); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo e(route('admin.products.show', $product)); ?>" 
                                                   class="btn btn-outline-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('admin.products.edit', $product)); ?>" 
                                                   class="btn btn-outline-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-box-seam display-4 text-muted"></i>
                        <h6 class="mt-3">No hay productos en esta categoría</h6>
                        <p class="text-muted">Agrega productos a esta categoría para comenzar.</p>
                        <a href="<?php echo e(route('admin.products.create', ['category' => $category->id])); ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>
                            Agregar Primer Producto
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ashraf\Desktop\pj\ByCrousty\backend_laravel_api\resources\views/admin/categories/show.blade.php ENDPATH**/ ?>