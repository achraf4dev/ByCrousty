@extends('layouts.admin.app')

@section('title', 'Ver Categoría')

@section('content')
<div class="row">
    <div class="col-lg-8 col-md-10 mx-auto">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-folder me-2"></i>
                    {{ $category->name }}
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil me-2"></i>
                        Editar
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
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
                                <td>{{ $category->id }}</td>
                            </tr>
                            <tr>
                                <th>Nombre:</th>
                                <td>{{ $category->name }}</td>
                            </tr>
                            <tr>
                                <th>Slug:</th>
                                <td><span class="badge bg-light text-dark">{{ $category->slug }}</span></td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td>
                                    <span class="badge {{ $category->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $category->status === 'active' ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Total Productos:</th>
                                <td>
                                    <span class="badge bg-info">{{ $category->products->count() }} productos</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Fecha Creación:</th>
                                <td>{{ $category->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Última Actualización:</th>
                                <td>{{ $category->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($category->description)
                    <div class="mt-4">
                        <h6>Descripción:</h6>
                        <p class="text-muted">{{ $category->description }}</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Products in this category -->
        <div class="card mt-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <i class="bi bi-box-seam me-2"></i>
                    Productos en esta categoría
                </h6>
                <a href="{{ route('admin.products.create', ['category' => $category->id]) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-2"></i>
                    Agregar Producto
                </a>
            </div>
            <div class="card-body">
                @if($category->products->count() > 0)
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
                                @foreach($category->products as $product)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $product->name }}</div>
                                            <small class="text-muted">{{ Str::limit($product->description, 40) }}</small>
                                        </td>
                                        <td>
                                            @if($product->price)
                                                <span class="fw-semibold">{{ $product->formatted_price }}</span>
                                            @else
                                                <span class="text-muted">Gratis</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark">{{ $product->points }} pts</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $product->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $product->status === 'active' ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.products.show', $product) }}" 
                                                   class="btn btn-outline-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.products.edit', $product) }}" 
                                                   class="btn btn-outline-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-box-seam display-4 text-muted"></i>
                        <h6 class="mt-3">No hay productos en esta categoría</h6>
                        <p class="text-muted">Agrega productos a esta categoría para comenzar.</p>
                        <a href="{{ route('admin.products.create', ['category' => $category->id]) }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>
                            Agregar Primer Producto
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection