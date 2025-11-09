@extends('layouts.admin.app')

@section('title', 'Ver Producto')

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-box-seam me-2"></i>
                    {{ $product->name }}
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil me-2"></i>
                        Editar
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left me-2"></i>
                        Volver
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">
                        @if($product->image)
                            <div class="text-center">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                     class="img-fluid rounded shadow-sm" style="max-height: 400px;">
                            </div>
                        @else
                            <div class="text-center">
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 300px;">
                                    <div class="text-muted">
                                        <i class="bi bi-image display-1"></i>
                                        <p class="mt-3">Sin imagen</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="col-md-7">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 30%;">ID:</th>
                                <td>{{ $product->id }}</td>
                            </tr>
                            <tr>
                                <th>Nombre:</th>
                                <td>{{ $product->name }}</td>
                            </tr>
                            <tr>
                                <th>Categoría:</th>
                                <td>
                                    <a href="{{ route('admin.categories.show', $product->category) }}" 
                                       class="badge bg-primary text-decoration-none">
                                        {{ $product->category->name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Precio:</th>
                                <td>
                                    @if($product->price)
                                        <span class="h5 text-success mb-0">{{ $product->formatted_price }}</span>
                                    @else
                                        <span class="h5 text-muted mb-0">Gratis</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Puntos Requeridos:</th>
                                <td>
                                    <span class="badge bg-warning text-dark fs-6">{{ $product->points }} puntos</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td>
                                    <span class="badge {{ $product->status === 'active' ? 'bg-success' : 'bg-secondary' }} fs-6">
                                        {{ $product->status === 'active' ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Fecha Creación:</th>
                                <td>{{ $product->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Última Actualización:</th>
                                <td>{{ $product->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($product->description)
                    <div class="mt-4">
                        <h6>Descripción del Producto:</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-0">{{ $product->description }}</p>
                        </div>
                    </div>
                @endif
                
                <!-- Product Actions -->
                <div class="mt-4 pt-3 border-top">
                    <h6>Acciones Rápidas:</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-pencil me-2"></i>
                            Editar Producto
                        </a>
                        
                        <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-{{ $product->status === 'active' ? 'secondary' : 'success' }} btn-sm">
                                <i class="bi bi-{{ $product->status === 'active' ? 'eye-slash' : 'eye' }} me-2"></i>
                                {{ $product->status === 'active' ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                        
                        <a href="{{ route('admin.categories.show', $product->category) }}" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-folder me-2"></i>
                            Ver Categoría
                        </a>
                        
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm" 
                                    onclick="return confirm('¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer.')">
                                <i class="bi bi-trash me-2"></i>
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="card mt-4">
                <div class="card-header bg-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-collection me-2"></i>
                        Otros productos en "{{ $product->category->name }}"
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($relatedProducts as $relatedProduct)
                            <div class="col-md-4 col-lg-3 mb-3">
                                <div class="card h-100">
                                    @if($relatedProduct->image)
                                        <img src="{{ $relatedProduct->image_url }}" class="card-img-top" 
                                             alt="{{ $relatedProduct->name }}" style="height: 150px; object-fit: cover;">
                                    @else
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 150px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="card-body d-flex flex-column">
                                        <h6 class="card-title">{{ $relatedProduct->name }}</h6>
                                        <p class="card-text text-muted small flex-grow-1">
                                            {{ Str::limit($relatedProduct->description, 60) }}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <small>
                                                @if($relatedProduct->price)
                                                    <span class="text-success fw-semibold">{{ $relatedProduct->formatted_price }}</span>
                                                @else
                                                    <span class="text-muted">Gratis</span>
                                                @endif
                                            </small>
                                            <small>
                                                <span class="badge bg-warning text-dark">{{ $relatedProduct->points }} pts</span>
                                            </small>
                                        </div>
                                        <div class="mt-2">
                                            <a href="{{ route('admin.products.show', $relatedProduct) }}" 
                                               class="btn btn-sm btn-outline-primary w-100">
                                                Ver Detalles
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Product Statistics -->
        <div class="card mt-4">
            <div class="card-header bg-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>
                    Estadísticas del Producto
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="p-3">
                            <i class="bi bi-eye display-4 text-info"></i>
                            <h5 class="mt-2">0</h5>
                            <p class="text-muted mb-0">Vistas</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3">
                            <i class="bi bi-cart display-4 text-success"></i>
                            <h5 class="mt-2">0</h5>
                            <p class="text-muted mb-0">Canjes</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3">
                            <i class="bi bi-heart display-4 text-danger"></i>
                            <h5 class="mt-2">0</h5>
                            <p class="text-muted mb-0">Favoritos</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3">
                            <i class="bi bi-star display-4 text-warning"></i>
                            <h5 class="mt-2">0.0</h5>
                            <p class="text-muted mb-0">Calificación</p>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <small class="text-muted">* Las estadísticas se actualizarán cuando los usuarios interactúen con el producto</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection