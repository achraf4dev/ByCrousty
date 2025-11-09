

<?php $__env->startSection('title', 'Gestión de Categorías'); ?>

<?php $__env->startSection('page-actions'); ?>
    <a href="<?php echo e(route('admin.categories.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>
        Nueva Categoría
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card user-management-container">
            <div class="card-header bg-white d-flex justify-content-between align-items-center card-header-border">
                <h5 class="card-title mb-0 card-title-styled">
                    <i class="bi bi-folder me-2 card-icon-primary"></i>
                    Todas las Categorías (<?php echo e($categories->total()); ?>)
                </h5>
                
                <div class="d-flex gap-2">
                    <div class="input-group search-input-group">
                        <span class="input-group-text search-input-text">
                            <i class="bi bi-search search-icon"></i>
                        </span>
                        <input type="text" class="form-control search-input" placeholder="Buscar categorías..." id="search-input">
                    </div>
                </div>
            </div>
            
            <div class="card-body p-0">
                <?php if($categories->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="table-col-id">#</th>
                                    <th>Categoría</th>
                                    <th>Slug</th>
                                    <th>Productos</th>
                                    <th>Fecha Creación</th>
                                    <th>Estado</th>
                                    <th class="table-col-actions">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="table-text-muted-weight"><?php echo e($category->id); ?></td>
                                        <td>
                                            <div>
                                                <div class="user-name-primary"><?php echo e($category->name); ?></div>
                                                <?php if($category->description): ?>
                                                    <div class="user-id-secondary"><?php echo e(Str::limit($category->description, 50)); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark"><?php echo e($category->slug); ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary fw-bold px-2 py-1">
                                                <?php echo e($category->products_count); ?> productos
                                            </span>
                                        </td>
                                        <td class="table-text-muted"><?php echo e($category->created_at->format('M d, Y')); ?></td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input status-toggle" type="checkbox" 
                                                       data-id="<?php echo e($category->id); ?>" 
                                                       data-url="<?php echo e(route('admin.categories.toggle-status', $category)); ?>"
                                                       <?php echo e($category->status === 'active' ? 'checked' : ''); ?>>
                                                <label class="form-check-label">
                                                    <?php if($category->status === 'active'): ?>
                                                        <span class="badge badge-success-custom">
                                                            <i class="bi bi-check-circle me-1"></i>Activa
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning-custom">
                                                            <i class="bi bi-exclamation-circle me-1"></i>Inactiva
                                                        </span>
                                                    <?php endif; ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('admin.categories.show', $category)); ?>" 
                                                   class="btn btn-sm btn-link text-primary p-1" 
                                                   title="Ver categoría">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('admin.categories.edit', $category)); ?>" 
                                                   class="btn btn-sm btn-link text-secondary p-1" 
                                                   title="Editar categoría">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="<?php echo e(route('admin.categories.destroy', $category)); ?>" 
                                                      method="POST" class="d-inline delete-form" 
                                                      onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-link text-danger p-1" 
                                                            title="Eliminar categoría">
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
                    <?php if($categories->hasPages()): ?>
                        <div class="card-footer bg-white card-footer-border">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="pagination-info">
                                    Mostrando <?php echo e($categories->firstItem()); ?> a <?php echo e($categories->lastItem()); ?> de <?php echo e($categories->total()); ?> resultados
                                </div>
                                <div>
                                    <?php echo e($categories->links()); ?>

                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-5 empty-state-text">
                        <i class="bi bi-folder-x empty-state-icon-large"></i>
                        <h5 class="mt-3 empty-state-title">No hay categorías</h5>
                        <p class="mb-4">Comienza creando tu primera categoría.</p>
                        <a href="<?php echo e(route('admin.categories.create')); ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>
                            Crear Primera Categoría
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchValue = this.value.toLowerCase();
                const tableRows = document.querySelectorAll('tbody tr');
                
                tableRows.forEach(row => {
                    const categoryName = row.cells[1].textContent.toLowerCase();
                    const categorySlug = row.cells[2].textContent.toLowerCase();
                    
                    if (categoryName.includes(searchValue) || categorySlug.includes(searchValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }, 300);
        });
    }
    
    // Status toggle functionality
    const toggles = document.querySelectorAll('.status-toggle');
    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const categoryId = this.dataset.id;
            const url = this.dataset.url;
            const isChecked = this.checked;
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Find the badge in the same row and update it
                    const row = this.closest('tr');
                    const badge = row.querySelector('.badge-success-custom, .badge-warning-custom');
                    
                    if (badge) {
                        if (data.status === 'active') {
                            badge.className = 'badge badge-success-custom';
                            badge.innerHTML = '<i class="bi bi-check-circle me-1"></i>Activa';
                        } else {
                            badge.className = 'badge badge-warning-custom';
                            badge.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>Inactiva';
                        }
                    }
                    
                    // Show message if products were affected
                    if (data.message && data.affected_products > 0) {
                        // Create a toast notification or alert
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-info alert-dismissible fade show position-fixed';
                        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 1055; max-width: 400px;';
                        alertDiv.innerHTML = `
                            <i class="bi bi-info-circle me-2"></i>${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        document.body.appendChild(alertDiv);
                        
                        // Auto remove after 5 seconds
                        setTimeout(() => {
                            if (alertDiv.parentNode) {
                                alertDiv.remove();
                            }
                        }, 5000);
                    }
                } else {
                    this.checked = !isChecked;
                    alert('Error al actualizar el estado');
                }
            })
            .catch(error => {
                this.checked = !isChecked;
                alert('Error al actualizar el estado');
            });
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ashraf\Desktop\pj\ByCrousty\backend_laravel_api\resources\views/admin/categories/index.blade.php ENDPATH**/ ?>