@extends('layouts.admin.app')

@section('title', 'Editar Usuario')

@section('page-actions')
    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-2"></i>
        Volver al Usuario
    </a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <div class="card user-edit-container">
            <div class="card-header bg-white card-header-border">
                <h5 class="card-title mb-0 card-title-styled">
                    <i class="bi bi-pencil me-2 card-icon-primary"></i>
                    Editar Información del Usuario
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label form-label-required">
                                Nombre Completo <span class="required-asterisk">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('full_name') is-invalid @enderror" 
                                   id="full_name" 
                                   name="full_name" 
                                   value="{{ old('full_name', $user->full_name) }}" 
                                   required>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label form-label-styled">
                                Nombre de Usuario
                            </label>
                            <input type="text" 
                                   class="form-control @error('username') is-invalid @enderror" 
                                   id="username" 
                                   name="username" 
                                   value="{{ old('username', $user->username) }}">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label form-label-required">
                                Dirección de Correo <span class="required-asterisk">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label form-label-styled">
                                Rol
                            </label>
                            <select class="form-control @error('role') is-invalid @enderror" 
                                    id="role" 
                                    name="role">
                                <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>Usuario</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrador</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="birthday" class="form-label form-label-styled">
                                Fecha de Nacimiento
                            </label>
                            <input type="date" 
                                   class="form-control @error('birthday') is-invalid @enderror" 
                                   id="birthday" 
                                   name="birthday" 
                                   value="{{ old('birthday', $user->birthday) }}">
                            @error('birthday')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone_number" class="form-label form-label-styled">
                                Número de Teléfono
                            </label>
                            <input type="text" 
                                   class="form-control @error('phone_number') is-invalid @enderror" 
                                   id="phone_number" 
                                   name="phone_number" 
                                   value="{{ old('phone_number', $user->phone_number) }}"
                                   placeholder="+1234567890">
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Address Information -->
                        <div class="col-12 mt-3 mb-3">
                            <h6 class="form-label form-label-styled">
                                <i class="bi bi-geo-alt me-2"></i>Información de Dirección
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="street_address" class="form-label form-label-styled">
                                Dirección
                            </label>
                            <input type="text" 
                                   class="form-control @error('street_address') is-invalid @enderror" 
                                   id="street_address" 
                                   name="street_address" 
                                   value="{{ old('street_address', $user->street_address) }}">
                            @error('street_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="city" class="form-label form-label-styled">
                                Ciudad
                            </label>
                            <input type="text" 
                                   class="form-control @error('city') is-invalid @enderror" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city', $user->city) }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="state" class="form-label form-label-styled">
                                Estado / Provincia
                            </label>
                            <input type="text" 
                                   class="form-control @error('state') is-invalid @enderror" 
                                   id="state" 
                                   name="state" 
                                   value="{{ old('state', $user->state) }}">
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="postal_code" class="form-label form-label-styled">
                                Código Postal
                            </label>
                            <input type="text" 
                                   class="form-control @error('postal_code') is-invalid @enderror" 
                                   id="postal_code" 
                                   name="postal_code" 
                                   value="{{ old('postal_code', $user->postal_code) }}">
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Password Section -->
                        <div class="col-12 mt-3 mb-3">
                            <h6 class="form-label form-label-styled">
                                <i class="bi bi-lock me-2"></i>Configuración de Contraseña
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label form-label-styled">
                                Nueva Contraseña
                            </label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Deje en blanco para mantener la contraseña actual">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text form-text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Solo complete este campo si desea cambiar la contraseña del usuario.
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label for="password_confirmation" class="form-label form-label-styled">
                                Confirmar Nueva Contraseña
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   placeholder="Confirmar nueva contraseña">
                        </div>
                    </div>
                    
                    
                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>
                            Actualizar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('password_confirmation');
    
    // Only require password confirmation if password is filled
    passwordField.addEventListener('input', function() {
        if (this.value.length > 0) {
            confirmPasswordField.required = true;
            confirmPasswordField.placeholder = 'Required when changing password';
        } else {
            confirmPasswordField.required = false;
            confirmPasswordField.placeholder = 'Confirm new password';
        }
    });
});
</script>
@endpush