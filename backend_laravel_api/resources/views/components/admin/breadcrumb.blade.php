@php
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
@endphp

<div class="breadcrumb-container">
    <div class="d-flex justify-content-between align-items-center">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                @foreach($breadcrumbs as $index => $breadcrumb)
                    @if($loop->last)
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ $breadcrumb['name'] }}
                        </li>
                    @else
                        <li class="breadcrumb-item">
                            <a href="{{ $breadcrumb['url'] }}">
                                {{ $breadcrumb['name'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
        
        <!-- Page Actions -->
        <div class="page-actions">
            @yield('page-actions')
        </div>
    </div>
</div>

