<div class="sidebar">
    <div class="sidebar-header">
        <h4 class="mb-0">
            <i class="bi bi-speedometer2 me-2"></i>
            <span>Panel de Admin</span>
        </h4>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="<?php echo e(route('admin.dashboard')); ?>" 
                   class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                    <i class="bi bi-house-door me-2"></i>
                    <span>Tablero</span>
                </a>
            </li>
            
            <li>
                <a href="<?php echo e(route('admin.users.index')); ?>" 
                   class="nav-link <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">
                    <i class="bi bi-people me-2"></i>
                    <span>Usuarios</span>
                </a>
            </li>
            
            <li>
                <a href="<?php echo e(route('admin.categories.index')); ?>" 
                   class="nav-link <?php echo e(request()->routeIs('admin.categories.*') ? 'active' : ''); ?>">
                    <i class="bi bi-tags me-2"></i>
                    <span>Categorías</span>
                </a>
            </li>
            
            <li>
                <a href="<?php echo e(route('admin.products.index')); ?>" 
                   class="nav-link <?php echo e(request()->routeIs('admin.products.*') ? 'active' : ''); ?>">
                    <i class="bi bi-box-seam me-2"></i>
                    <span>Productos</span>
                </a>
            </li>
            
            <li>
                <a href="<?php echo e(route('admin.points-history')); ?>" 
                   class="nav-link <?php echo e(request()->routeIs('admin.points-history') ? 'active' : ''); ?>">
                    <i class="bi bi-trophy me-2"></i>
                    <span>Historial Puntos</span>
                </a>
            </li>
            
            <li>
                <a href="#" class="nav-link">
                    <i class="bi bi-gear me-2"></i>
                    <span>Configuración</span>
                </a>
            </li>
            
            <li class="sidebar-logout">
                <form method="POST" action="<?php echo e(route('admin.logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                        <i class="bi bi-box-arrow-right me-2"></i>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</div>

<?php /**PATH C:\Users\Ashraf\Desktop\pj\ByCrousty\backend_laravel_api\resources\views/components/admin/sidebar.blade.php ENDPATH**/ ?>