<nav class="navbar">
    <div class="d-flex align-items-center">
        <!-- Sidebar Toggle Button -->
        <button class="btn btn-outline-secondary me-3" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        
        <!-- Website Name -->
        <div class="navbar-brand">
            <h5 class="mb-0 navbar-brand-text">
                <i class="bi bi-grid-fill me-2 navbar-brand-icon"></i>
                <?php echo e(config('app.name', 'ByCrousty')); ?>

            </h5>
        </div>
    </div>
    
    <div class="d-flex align-items-center gap-3">
        <!-- Search Bar -->
        <div class="search-container d-none d-md-flex">
            <div class="input-group input-group-sm">
                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control" placeholder="Buscar...">
            </div>
        </div>
        
        <!-- User Dropdown -->
        <div class="dropdown user-dropdown">
            <button class="btn dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-2"></i>
                <span><?php echo e(auth()->user()->full_name); ?></span>
            </button>
            
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <div class="dropdown-header">
                        <div><?php echo e(auth()->user()->full_name); ?></div>
                        <div><?php echo e(auth()->user()->email); ?></div>
                    </div>
                </li>
                <li>
                    <a class="dropdown-item" href="#">
                        <i class="bi bi-person me-2"></i>
                        Perfil
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#">
                        <i class="bi bi-gear me-2"></i>
                        Configuración
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="<?php echo e(route('admin.logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="dropdown-item">
                            <i class="bi bi-box-arrow-right me-2"></i>
                            Cerrar Sesión
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    
    // Load saved sidebar state from localStorage
    function loadSidebarState() {
        const sidebarCollapsed = localStorage.getItem('sidebarCollapsed');
        if (sidebarCollapsed === 'true' && window.innerWidth > 768) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('sidebar-collapsed');
        }
    }
    
    // Save sidebar state to localStorage
    function saveSidebarState(collapsed) {
        localStorage.setItem('sidebarCollapsed', collapsed);
    }
    
    // Initialize sidebar state on page load
    loadSidebarState();
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            // For mobile: toggle show class
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show');
            } else {
                // For desktop: toggle collapsed state
                const isCollapsed = sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('sidebar-collapsed');
                
                // Save the current state to localStorage
                saveSidebarState(isCollapsed);
            }
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
        
        // Handle window resize - restore desktop state if coming from mobile
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                // Remove mobile show class
                sidebar.classList.remove('show');
                // Restore collapsed state from localStorage
                loadSidebarState();
            } else {
                // Remove desktop collapsed classes on mobile
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('sidebar-collapsed');
            }
        });
    }
});
</script><?php /**PATH C:\Users\Ashraf\Desktop\pj\ByCrousty\backend_laravel_api\resources\views/components/admin/navbar.blade.php ENDPATH**/ ?>