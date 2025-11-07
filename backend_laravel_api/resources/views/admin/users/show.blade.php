@extends('layouts.admin.app')

@section('title', 'Detalles del Usuario')

@section('page-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-2"></i>
            Editar Usuario
        </a>
        <form method="POST" action="{{ route('admin.users.delete', $user->id) }}" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este usuario?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-trash me-2"></i>
                Eliminar Usuario
            </button>
        </form>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- User Profile Card -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="user-title">{{ $user->full_name }}</h4>
                <p class="text-muted mb-3">{{ $user->email }}</p>
                
                @if($user->email_verified_at)
                    <span class="badge badge-large-verified">
                        <i class="bi bi-check-circle me-2"></i>Cuenta Verificada
                    </span>
                @else
                    <span class="badge badge-large-unverified">
                        <i class="bi bi-exclamation-circle me-2"></i>Cuenta No Verificada
                    </span>
                @endif
            </div>
        </div>
        
        <!-- QR Code Card -->
        <div class="card mt-3">
            <div class="card-header bg-white card-header-border">
                <h5 class="card-title mb-0 card-title-styled">
                    <i class="bi bi-qr-code me-2 card-icon-primary"></i>
                    Código QR del Usuario
                </h5>
            </div>
            <div class="card-body text-center">
                <div class="qr-code-container mb-3">
                    <img src="data:image/png;base64,{{ $qrCodeBase64 }}" 
                         alt="QR Code for {{ $user->full_name }}" 
                         class="qr-code-image" 
                         style="max-width: 200px; width: 100%; height: auto; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px; background: white;">
                </div>
                <p class="text-muted small mb-2">
                    <i class="bi bi-info-circle me-1"></i>
                    Este código QR contiene la información del usuario
                </p>
                <button class="btn btn-sm btn-outline-primary" onclick="downloadQRCode('{{ $user->full_name }}')">
                    <i class="bi bi-download me-2"></i>
                    Descargar QR
                </button>
            </div>
        </div>
        
        <!-- Points Card -->
        <div class="card mt-3">
            <div class="card-header bg-white card-header-border">
                <h5 class="card-title mb-0 card-title-styled">
                    <i class="bi bi-award me-2 card-icon-primary"></i>
                    Puntos del Usuario
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="display-6 fw-bold text-primary">{{ $user->points ?? 0 }}</div>
                    <small class="text-muted">Puntos Totales</small>
                </div>
                
                <!-- Award Points Form -->
                <form method="POST" action="{{ route('admin.users.award-points', $user->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label form-label-styled">Otorgar Puntos</label>
                        <select name="points" class="form-control" required>
                            <option value="">Seleccionar puntos...</option>
                            <option value="100">100 Puntos</option>
                            <option value="200">200 Puntos</option>
                            <option value="300">300 Puntos</option>
                            <option value="500">500 Puntos</option>
                            <option value="1000">1000 Puntos</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label form-label-styled">Descripción (Opcional)</label>
                        <input type="text" name="description" class="form-control" placeholder="Razón para otorgar puntos...">
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-award me-2"></i>
                        Otorgar Puntos
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- User Information -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header bg-white card-header-border">
                <h5 class="card-title mb-0 card-title-styled">
                    <i class="bi bi-person-lines-fill me-2 card-icon-primary"></i>
                    Información del Usuario
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label user-detail-label">Nombre Completo</label>
                        <p class="user-detail-value">{{ $user->full_name }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label user-detail-label">Nombre de Usuario</label>
                        <p class="user-detail-value">{{ $user->username ?? 'No establecido' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label user-detail-label">Dirección de Correo</label>
                        <p class="user-detail-value">{{ $user->email }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label user-detail-label">Rol</label>
                        <p class="user-detail-value">
                            <span class="badge {{ $user->role === 'admin' ? 'badge-verified' : 'bg-secondary' }}">
                                <i class="bi {{ $user->role === 'admin' ? 'bi-shield-check' : 'bi-person' }} me-1"></i>
                                {{ ucfirst($user->role === 'admin' ? 'Administrador' : 'Usuario') }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label user-detail-label">ID de Usuario</label>
                        <p class="user-detail-value">{{ $user->id }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label user-detail-label">Fecha de Nacimiento</label>
                        <p class="user-detail-value">{{ $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('F j, Y') : 'No establecido' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label user-detail-label">Número de Teléfono</label>
                        <p class="user-detail-value">
                            @if($user->phone_code && $user->phone_number)
                                +{{ $user->phone_code }} {{ $user->phone_number }}
                            @else
                                No establecido
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label user-detail-label">Estado de la Cuenta</label>
                        <p class="user-detail-value">
                            @if($user->email_verified_at)
                                <span class="badge badge-verified">
                                    <i class="bi bi-check-circle me-1"></i>Activo
                                </span>
                            @else
                                <span class="badge badge-unverified">
                                    <i class="bi bi-exclamation-circle me-1"></i>Verificación Pendiente
                                </span>
                            @endif
                        </p>
                    </div>
                    
                    <!-- Address Information -->
                    <div class="col-12 mt-3">
                        <h6 class="user-detail-label mb-3">
                            <i class="bi bi-geo-alt me-2"></i>Información de Dirección
                        </h6>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label user-detail-label">Dirección</label>
                        <p class="user-detail-value">{{ $user->street_address ?? 'No establecido' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label user-detail-label">Línea de Dirección 2</label>
                        <p class="user-detail-value">{{ $user->address_line_2 ?? 'No establecido' }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label user-detail-label">Ciudad</label>
                        <p class="user-detail-value">{{ $user->city ?? 'No establecido' }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label user-detail-label">Estado</label>
                        <p class="user-detail-value">{{ $user->state ?? 'No establecido' }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label user-detail-label">Código Postal</label>
                        <p class="user-detail-value">{{ $user->postal_code ?? 'No establecido' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label user-detail-label">País</label>
                        <p class="user-detail-value">{{ $user->country ?? 'No establecido' }}</p>
                    </div>
                    
                    <!-- Account Timestamps -->
                    <div class="col-12 mt-3">
                        <h6 class="user-detail-label mb-3">
                            <i class="bi bi-clock me-2"></i>Información de la Cuenta
                        </h6>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label user-detail-label">Miembro Desde</label>
                        <p class="user-detail-value">{{ $user->created_at->format('F j, Y') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label user-detail-label">Última Actualización</label>
                        <p class="user-detail-value">{{ $user->updated_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    @if($user->email_verified_at)
                        <div class="col-md-6 mb-3">
                            <label class="form-label user-detail-label">Correo Verificado</label>
                            <p class="user-detail-value">{{ $user->email_verified_at->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Account Actions -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-white card-header-border">
                <h5 class="card-title mb-0 card-title-styled">
                    <i class="bi bi-gear me-2 card-icon-primary"></i>
                    Acciones de la Cuenta
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-2"></i>
                        Editar Información del Usuario
                    </a>
                    
                    @if(!$user->email_verified_at)
                        <button class="btn btn-outline-success">
                            <i class="bi bi-check-circle me-2"></i>
                            Marcar como Verificado
                        </button>
                    @endif
                    
                    <button class="btn btn-outline-warning">
                        <i class="bi bi-key me-2"></i>
                        Restablecer Contraseña
                    </button>
                    
                    <hr style="margin: 20px 0;">
                    
                    <form method="POST" action="{{ route('admin.users.delete', $user->id) }}" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este usuario? Esta acción no se puede deshacer.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash me-2"></i>
                            Eliminar Cuenta de Usuario
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Points History -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-white card-header-border">
                <h5 class="card-title mb-0 card-title-styled">
                    <i class="bi bi-trophy me-2 card-icon-primary"></i>
                    Historial de Puntos
                </h5>
            </div>
            <div class="card-body">
                @if($pointsHistory->count() > 0)
                    <div class="points-timeline">
                        @foreach($pointsHistory as $history)
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-icon {{ $history->points > 0 ? 'timeline-icon-success' : 'timeline-icon-warning' }} me-3">
                                    <i class="bi {{ $history->points > 0 ? 'bi-plus-circle' : 'bi-dash-circle' }}"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <p class="mb-1 timeline-content-title">
                                                {{ $history->points > 0 ? '+' : '' }}{{ $history->points }} puntos
                                            </p>
                                            <small class="text-muted">{{ $history->formatted_description }}</small>
                                            <br>
                                            <small class="timeline-content-date">
                                                Por: {{ $history->admin->full_name }} - {{ $history->created_at->format('M j, Y g:i A') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    @if($pointsHistory->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $pointsHistory->links() }}
                    </div>
                    @endif
                @else
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-trophy-fill opacity-50" style="font-size: 2rem;"></i>
                        <p class="mb-0 mt-2">No hay historial de puntos aún</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Activity Log -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-white card-header-border">
                <h5 class="card-title mb-0 card-title-styled">
                    <i class="bi bi-clock-history me-2 card-icon-primary"></i>
                    Actividad Reciente
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item mb-3">
                        <div class="d-flex">
                            <div class="timeline-icon timeline-icon-success me-3">
                                <i class="bi bi-person-check timeline-icon-check"></i>
                            </div>
                            <div class="flex-1">
                                <p class="mb-1 timeline-content-title">Cuenta creada</p>
                                <small class="timeline-content-date">{{ $user->created_at->format('F j, Y \a\t g:i A') }}</small>
                            </div>
                        </div>
                    </div>
                    
                    @if($user->email_verified_at)
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-icon timeline-icon-primary me-3">
                                    <i class="bi bi-envelope-check timeline-icon-email"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="mb-1 timeline-content-title">Correo verificado</p>
                                    <small class="timeline-content-date">{{ $user->email_verified_at->format('F j, Y \a\t g:i A') }}</small>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="timeline-item">
                        <div class="d-flex">
                            <div class="timeline-icon timeline-icon-warning me-3">
                                <i class="bi bi-pencil"></i>
                            </div>
                            <div class="flex-1">
                                <p class="mb-1 timeline-content-title">Perfil actualizado</p>
                                <small class="timeline-content-date">{{ $user->updated_at->format('F j, Y \a\t g:i A') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function downloadQRCode(userName) {
    // Get the QR code image
    const qrImage = document.querySelector('.qr-code-image');
    
    // Create a canvas to convert the image
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    
    // Set canvas size to match image
    canvas.width = qrImage.naturalWidth;
    canvas.height = qrImage.naturalHeight;
    
    // Draw the image on canvas
    ctx.drawImage(qrImage, 0, 0);
    
    // Create download link
    canvas.toBlob(function(blob) {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `QR_Code_${userName.replace(/\s+/g, '_')}.png`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });
}
</script>
@endsection