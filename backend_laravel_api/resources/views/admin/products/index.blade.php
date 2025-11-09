@extends('layouts.admin.app')

@section('title', 'Gestión de Productos')

@section('page-actions')
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>
        Nuevo Producto
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card user-management-container">
            <div class="card-header bg-white d-flex justify-content-between align-items-center card-header-border">
                <h5 class="card-title mb-0 card-title-styled">
                    <i class="bi bi-box-seam me-2 card-icon-primary"></i>
                    Todos los Productos ({{ $products->total() }})
                </h5>
                
                <div class="d-flex gap-2">
                    <!-- Filters -->
                    <select class="form-select" id="category-filter" style="width: auto;">
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $categoryOption)
                            <option value="{{ $categoryOption->id }}" 
                                    {{ request('category') == $categoryOption->id ? 'selected' : '' }}>
                                {{ $categoryOption->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    <select class="form-select" id="status-filter" style="width: auto;">
                        <option value="">Todos los estados</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                    
                    <div class="input-group search-input-group">
                        <span class="input-group-text search-input-text">
                            <i class="bi bi-search search-icon"></i>
                        </span>
                        <input type="text" class="form-control search-input" placeholder="Buscar productos..." 
                               id="search-input" value="{{ request('search') }}">
                    </div>
                </div>
            </div>
            
            <div class="card-body p-0">
                @if($products->count() > 0)
                    <form id="bulk-delete-form" action="{{ route('admin.products.bulk-delete') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="select-all">
                                            </div>
                                        </th>
                                        <th class="table-col-id">#</th>
                                        <th>Producto</th>
                                        <th>Categoría</th>
                                        <th>Precio</th>
                                        <th>Puntos</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                        <th class="table-col-actions">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input product-checkbox" type="checkbox" 
                                                           name="product_ids[]" value="{{ $product->id }}">
                                                </div>
                                            </td>
                                            <td class="table-text-muted-weight">{{ $product->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($product->image)
                                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                                             class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" 
                                                             style="width: 40px; height: 40px;">
                                                            <i class="bi bi-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="user-name-primary">{{ $product->name }}</div>
                                                        @if($product->description)
                                                            <div class="user-id-secondary">{{ Str::limit($product->description, 50) }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $product->category->name }}</span>
                                            </td>
                                            <td class="table-text-muted-weight">
                                                @if($product->price)
                                                    <span class="text-success fw-semibold">{{ $product->formatted_price }}</span>
                                                @else
                                                    <span class="text-muted">Gratis</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary fw-bold px-2 py-1">{{ $product->points }} pts</span>
                                            </td>
                                            <td class="table-text-muted">{{ $product->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input status-toggle" type="checkbox" 
                                                           data-id="{{ $product->id }}" 
                                                           data-url="{{ route('admin.products.toggle-status', $product) }}"
                                                           {{ $product->status === 'active' ? 'checked' : '' }}>
                                                    <label class="form-check-label">
                                                        @if($product->status === 'active')
                                                            <span class="badge badge-success-custom">
                                                                <i class="bi bi-check-circle me-1"></i>Activo
                                                            </span>
                                                        @else
                                                            <span class="badge badge-warning-custom">
                                                                <i class="bi bi-exclamation-circle me-1"></i>Inactivo
                                                            </span>
                                                        @endif
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.products.show', $product) }}" 
                                                       class="btn btn-sm btn-link text-primary p-1" 
                                                       title="Ver producto">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.products.edit', $product) }}" 
                                                       class="btn btn-sm btn-link text-secondary p-1" 
                                                       title="Editar producto">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('admin.products.destroy', $product) }}" 
                                                          method="POST" class="d-inline delete-form" 
                                                          onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-link text-danger p-1" 
                                                                title="Eliminar producto">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Bulk Actions -->
                        <div class="px-3 pb-3 pt-2">
                            <button class="btn btn-outline-danger btn-sm" id="bulk-delete-btn" disabled>
                                <i class="bi bi-trash me-2"></i>
                                Eliminar Seleccionados
                            </button>
                        </div>
                    </form>
                    
                    <!-- Pagination -->
                    @if($products->hasPages())
                        <div class="card-footer bg-white card-footer-border">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="pagination-info">
                                    Mostrando {{ $products->firstItem() }} a {{ $products->lastItem() }} de {{ $products->total() }} resultados
                                </div>
                                <div>
                                    {{ $products->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5 empty-state-text">
                        <i class="bi bi-box-seam empty-state-icon-large"></i>
                        <h5 class="mt-3 empty-state-title">No hay productos</h5>
                        <p class="mb-4">Comienza creando tu primer producto.</p>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>
                            Crear Primer Producto
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filters functionality
    const categoryFilter = document.getElementById('category-filter');
    const statusFilter = document.getElementById('status-filter');
    const searchInput = document.getElementById('search-input');
    
    function applyFilters() {
        const url = new URL(window.location.href);
        const params = new URLSearchParams();
        
        if (categoryFilter.value) params.set('category', categoryFilter.value);
        if (statusFilter.value !== '') params.set('status', statusFilter.value);
        if (searchInput.value) params.set('search', searchInput.value);
        
        url.search = params.toString();
        window.location.href = url.toString();
    }
    
    categoryFilter.addEventListener('change', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
    
    // Search with debounce
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            applyFilters();
        }, 500);
    });
    
    // Bulk selection
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    
    selectAll?.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkDeleteButton();
    });
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkDeleteButton);
    });
    
    function updateBulkDeleteButton() {
        const selectedCount = document.querySelectorAll('.product-checkbox:checked').length;
        bulkDeleteBtn.disabled = selectedCount === 0;
    }
    
    // Bulk delete
    bulkDeleteBtn?.addEventListener('click', function() {
        const selectedCount = document.querySelectorAll('.product-checkbox:checked').length;
        if (selectedCount > 0) {
            if (confirm(`¿Estás seguro de eliminar ${selectedCount} producto(s) seleccionado(s)?`)) {
                document.getElementById('bulk-delete-form').submit();
            }
        }
    });
    
    // Status toggle functionality
    const toggles = document.querySelectorAll('.status-toggle');
    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const productId = this.dataset.id;
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
                            badge.innerHTML = '<i class="bi bi-check-circle me-1"></i>Activo';
                        } else {
                            badge.className = 'badge badge-warning-custom';
                            badge.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>Inactivo';
                        }
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
@endpush