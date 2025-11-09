<?php
    $breadcrumbs = [];
    $currentRoute = request()->route()->getName();
    
    // Define breadcrumb mappings
    $breadcrumbMap = [
        'admin.dashboard' => [
            ['name' => 'Dashboard', 'url' => null]
        ],
        'admin.users.index' => [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Users', 'url' => null]
        ],
        'admin.users.show' => [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Users', 'url' => route('admin.users.index')],
            ['name' => 'User Details', 'url' => null]
        ],
        'admin.users.edit' => [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Users', 'url' => route('admin.users.index')],
            ['name' => 'Edit User', 'url' => null]
        ],
        'admin.categories.index' => [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Categorías', 'url' => null]
        ],
        'admin.categories.create' => [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Categorías', 'url' => route('admin.categories.index')],
            ['name' => 'Nueva Categoría', 'url' => null]
        ],
        'admin.categories.show' => [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Categorías', 'url' => route('admin.categories.index')],
            ['name' => 'Detalles de Categoría', 'url' => null]
        ],
        'admin.categories.edit' => [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Categorías', 'url' => route('admin.categories.index')],
            ['name' => 'Editar Categoría', 'url' => null]
        ],
        'admin.products.index' => [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Productos', 'url' => null]
        ],
        'admin.products.create' => [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Productos', 'url' => route('admin.products.index')],
            ['name' => 'Nuevo Producto', 'url' => null]
        ],
        'admin.products.show' => [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Productos', 'url' => route('admin.products.index')],
            ['name' => 'Detalles de Producto', 'url' => null]
        ],
        'admin.products.edit' => [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Productos', 'url' => route('admin.products.index')],
            ['name' => 'Editar Producto', 'url' => null]
        ],
    ];
    
    $breadcrumbs = $breadcrumbMap[$currentRoute] ?? [['name' => 'Dashboard', 'url' => null]];
?>

<div class="breadcrumb-container">
    <div class="d-flex justify-content-between align-items-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $breadcrumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($loop->last): ?>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?php echo e($breadcrumb['name']); ?>

                        </li>
                    <?php else: ?>
                        <li class="breadcrumb-item">
                            <a href="<?php echo e($breadcrumb['url']); ?>">
                                <?php echo e($breadcrumb['name']); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ol>
        </nav>
        
        <!-- Page Actions -->
        <div class="page-actions">
            <?php echo $__env->yieldContent('page-actions'); ?>
        </div>
    </div>
</div>

<?php /**PATH C:\Users\Ashraf\Desktop\pj\ByCrousty\backend_laravel_api\resources\views/components/admin/breadcrumb.blade.php ENDPATH**/ ?>