@extends('layouts.admin.app')

@section('title', 'Crear Producto')

@section('content')
<div class="row">
    <div class="col-lg-8 col-md-10 mx-auto">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-plus-circle me-2"></i>
                    Crear Nuevo Producto
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Categoría <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id" required>
                                    <option value="">Seleccionar categoría</option>
                                    @foreach($categories as $categoryOption)
                                        <option value="{{ $categoryOption->id }}" 
                                                {{ old('category_id', request('category')) == $categoryOption->id ? 'selected' : '' }}>
                                            {{ $categoryOption->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" 
                                  placeholder="Describe este producto...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Precio (USD)</label>
                                <div class="input-group">
                                    <span class="input-group-text">€</span>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price') }}" 
                                           step="0.01" min="0" placeholder="0.00">
                                </div>
                                <div class="form-text">Deja en blanco si es gratis</div>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="points" class="form-label">Puntos Requeridos <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('points') is-invalid @enderror" 
                                       id="points" name="points" value="{{ old('points') }}" 
                                       min="0" required placeholder="Ej: 100">
                                <div class="form-text">Puntos que necesita el usuario para obtener este producto</div>
                                @error('points')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Imagen del Producto</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                               id="image" name="image" accept="image/*">
                        <div class="form-text">Formatos permitidos: JPG, PNG, WebP. Tamaño máximo: 2MB</div>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <!-- Image Preview -->
                        <div id="image-preview" class="mt-3" style="display: none;">
                            <img id="preview-img" src="#" alt="Vista previa" class="img-thumbnail" style="max-width: 300px;">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="status" name="status" value="1" 
                                   {{ old('status', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="status">
                                Producto Activo
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>
                            Crear Producto
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Product Preview Card -->
        <div class="card mt-4" id="product-preview" style="display: none;">
            <div class="card-header bg-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-eye me-2"></i>
                    Vista Previa del Producto
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div id="preview-image-container" class="text-center">
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                 style="height: 200px;" id="preview-placeholder">
                                <i class="bi bi-image display-4 text-muted"></i>
                            </div>
                            <img id="preview-product-img" src="#" alt="Producto" 
                                 class="img-fluid rounded" style="max-height: 200px; display: none;">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h5 id="preview-name" class="text-muted">Nombre del producto</h5>
                        <p id="preview-category" class="text-muted">
                            <span class="badge bg-light text-dark">Categoría</span>
                        </p>
                        <p id="preview-description" class="text-muted">Descripción del producto</p>
                        <div class="d-flex gap-3 align-items-center">
                            <span id="preview-price" class="h5 text-success mb-0">€0.00</span>
                            <span id="preview-points" class="badge bg-warning text-dark">0 pts</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const categorySelect = document.getElementById('category_id');
    const descriptionTextarea = document.getElementById('description');
    const priceInput = document.getElementById('price');
    const pointsInput = document.getElementById('points');
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    // Product Preview Elements
    const productPreview = document.getElementById('product-preview');
    const previewName = document.getElementById('preview-name');
    const previewCategory = document.getElementById('preview-category');
    const previewDescription = document.getElementById('preview-description');
    const previewPrice = document.getElementById('preview-price');
    const previewPoints = document.getElementById('preview-points');
    const previewProductImg = document.getElementById('preview-product-img');
    const previewPlaceholder = document.getElementById('preview-placeholder');
    
    // Image preview
    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
                
                // Update product preview
                previewProductImg.src = e.target.result;
                previewProductImg.style.display = 'block';
                previewPlaceholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
            previewProductImg.style.display = 'none';
            previewPlaceholder.style.display = 'flex';
        }
        updateProductPreview();
    });
    
    // Update product preview on input changes
    function updateProductPreview() {
        const hasContent = nameInput.value || categorySelect.value || descriptionTextarea.value || 
                          priceInput.value || pointsInput.value;
        
        if (hasContent) {
            productPreview.style.display = 'block';
            
            previewName.textContent = nameInput.value || 'Nombre del producto';
            previewName.className = nameInput.value ? 'fw-bold' : 'text-muted';
            
            const selectedOption = categorySelect.options[categorySelect.selectedIndex];
            previewCategory.innerHTML = selectedOption.value ? 
                `<span class="badge bg-primary">${selectedOption.text}</span>` :
                '<span class="badge bg-light text-dark">Categoría</span>';
            
            previewDescription.textContent = descriptionTextarea.value || 'Descripción del producto';
            previewDescription.className = descriptionTextarea.value ? '' : 'text-muted';
            
            const price = parseFloat(priceInput.value) || 0;
            previewPrice.textContent = price > 0 ? `€${price.toFixed(2)}` : 'Gratis';
            previewPrice.className = price > 0 ? 'h5 text-success mb-0' : 'h5 text-muted mb-0';
            
            const points = parseInt(pointsInput.value) || 0;
            previewPoints.textContent = `${points} pts`;
            previewPoints.className = points > 0 ? 'badge bg-warning text-dark' : 'badge bg-light text-dark';
        } else {
            productPreview.style.display = 'none';
        }
    }
    
    // Add event listeners for preview updates
    [nameInput, categorySelect, descriptionTextarea, priceInput, pointsInput].forEach(input => {
        input.addEventListener('input', updateProductPreview);
        input.addEventListener('change', updateProductPreview);
    });
    
    // Initial preview update
    updateProductPreview();
});
</script>
@endpush