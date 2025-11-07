@extends('layouts.admin.app')

@section('title', 'Historial de Puntos')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white card-header-border">
                <h5 class="card-title mb-0 card-title-styled">
                    <i class="bi bi-trophy me-2 card-icon-primary"></i>
                    Historial Completo de Puntos ({{ $pointsHistory->total() }} transacciones)
                </h5>
            </div>
            <div class="card-body">
                @if($pointsHistory->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Puntos</th>
                                    <th>Descripción</th>
                                    <th>Otorgado por</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pointsHistory as $history)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-3">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    {{ substr($history->user->full_name, 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $history->user->full_name }}</div>
                                                <small class="text-muted">{{ $history->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $history->points > 0 ? 'bg-success' : 'bg-warning' }} fw-bold px-3 py-2">
                                            {{ $history->points > 0 ? '+' : '' }}{{ $history->points }} pts
                                        </span>
                                    </td>
                                    <td>
                                        <div>{{ $history->formatted_description }}</div>
                                        @if($history->qr_code_data)
                                            <small class="text-muted">
                                                <i class="bi bi-qr-code me-1"></i>
                                                Via QR Code
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-2">
                                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 30px; height: 30px; font-size: 12px;">
                                                    {{ substr($history->admin->full_name, 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $history->admin->full_name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{ $history->created_at->format('M j, Y') }}</div>
                                        <small class="text-muted">{{ $history->created_at->format('g:i A') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Mostrando {{ $pointsHistory->firstItem() }} a {{ $pointsHistory->lastItem() }} 
                            de {{ $pointsHistory->total() }} transacciones
                        </div>
                        {{ $pointsHistory->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-trophy-fill text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        <h5 class="text-muted mt-3">No hay historial de puntos</h5>
                        <p class="text-muted">Aún no se han otorgado puntos a ningún usuario.</p>
                        <a href="{{ route('admin.qr-scanner') }}" class="btn btn-primary">
                            <i class="bi bi-qr-code-scan me-2"></i>
                            Comenzar a Otorgar Puntos
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card stats-card stats-card-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-subtitle">Total Puntos Otorgados</div>
                        <div class="card-title">{{ $pointsHistory->sum('points') }}</div>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-trophy"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stats-card stats-card-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-subtitle">Transacciones Hoy</div>
                        <div class="card-title">{{ $pointsHistory->where('created_at', '>=', today())->count() }}</div>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stats-card stats-card-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-subtitle">Via QR Code</div>
                        <div class="card-title">{{ $pointsHistory->whereNotNull('qr_code_data')->count() }}</div>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-qr-code"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stats-card stats-card-purple text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-subtitle">Usuarios con Puntos</div>
                        <div class="card-title">{{ $pointsHistory->pluck('user_id')->unique()->count() }}</div>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection