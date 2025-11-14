@extends('layouts.admin.guest')

@section('title', 'Iniciar Sesión Admin')

@section('content')
<form method="POST" action="{{ route('admin.login') }}">
    @csrf
    
    <div class="login-input-group">
        <i class="bi bi-envelope"></i>
        <input type="email" 
               class="form-control @error('email') is-invalid @enderror" 
               name="email" 
               placeholder="Dirección de Correo" 
               value="{{ old('email') }}" 
               required 
               autofocus>
    </div>
    
    <div class="login-input-group">
        <i class="bi bi-lock"></i>
        <input type="password" 
               class="form-control @error('password') is-invalid @enderror" 
               name="password" 
               placeholder="Contraseña" 
               required>
    </div>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">
                Recordarme
            </label>
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary login-btn">
        <i class="bi bi-box-arrow-in-right me-2"></i>
        Iniciar Sesión
    </button>
</form>
@endsection