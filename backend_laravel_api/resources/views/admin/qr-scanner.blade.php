@extends('layouts.admin.app')

@section('title', 'Escáner QR - Otorgar Puntos')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white card-header-border">
                <h5 class="card-title mb-0 card-title-styled">
                    <i class="bi bi-qr-code-scan me-2 card-icon-primary"></i>
                    Escanear Código QR para Otorgar Puntos
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.qr-scan') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label form-label-styled">
                            <i class="bi bi-qr-code me-2"></i>
                            Datos del Código QR
                        </label>
                        <textarea 
                            name="qr_code_data" 
                            class="form-control" 
                            rows="6" 
                            placeholder="Pegue aquí los datos del código QR escaneado..."
                            required></textarea>
                        <small class="form-text text-muted">
                            Escanee el código QR del usuario con su aplicación y pegue los datos aquí.
                        </small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label form-label-styled">
                                <i class="bi bi-award me-2"></i>
                                Puntos a Otorgar
                            </label>
                            <select name="points" class="form-control" required>
                                <option value="">Seleccionar puntos...</option>
                                <option value="100">100 Puntos</option>
                                <option value="200">200 Puntos</option>
                                <option value="300">300 Puntos</option>
                                <option value="500">500 Puntos</option>
                                <option value="750">750 Puntos</option>
                                <option value="1000">1000 Puntos</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label form-label-styled">
                                <i class="bi bi-chat-text me-2"></i>
                                Descripción (Opcional)
                            </label>
                            <input 
                                type="text" 
                                name="description" 
                                class="form-control" 
                                placeholder="Razón para otorgar puntos...">
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle me-2"></i>
                            Procesar QR y Otorgar Puntos
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Volver al Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Info Card -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Instrucciones
                </h6>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li class="mb-2">Solicite al usuario que muestre su código QR desde la aplicación móvil</li>
                    <li class="mb-2">Escanee el código QR con cualquier aplicación de escaneo</li>
                    <li class="mb-2">Copie y pegue los datos del código QR en el campo de arriba</li>
                    <li class="mb-2">Seleccione la cantidad de puntos a otorgar</li>
                    <li class="mb-0">Agregue una descripción opcional y haga clic en "Procesar"</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle me-2"></i>
                    ¡Puntos Otorgados Exitosamente!
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">{{ session('success') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="modal fade" id="errorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error al Procesar QR
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">{{ session('error') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show success modal if there's a success message
    @if(session('success'))
    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
    successModal.show();
    @endif
    
    // Show error modal if there's an error message
    @if(session('error'))
    var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
    errorModal.show();
    @endif
});
</script>
@endsection