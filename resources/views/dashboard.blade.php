@extends('layouts.app')

@section('title', 'Dashboard - Sistema de Laboratorio')

@push('styles')
<style>
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15) !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            @php
                $empresa = \App\Models\Empresa::obtenerEmpresa();
            @endphp
            <h1 class="h2">
                <i class="fas fa-tachometer-alt me-2"></i>Bienvenido, {{ Auth::user()->name }}
            </h1>
            <p class="text-muted">Panel de control de {{ $empresa?->razon_social ?? 'Sistema de Laboratorio' }}</p>
        </div>
        <div class="col-md-4 text-end">
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                </button>
            </form>
        </div>
    </div>

    <!-- Accesos Rápidos -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="fas fa-bolt me-2"></i>Accesos Rápidos
            </h5>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('clientes.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 hover-card" style="transition: all 0.3s ease;">
                    <div class="card-body text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-users fa-3x text-primary"></i>
                        </div>
                        <h6 class="mb-1">Gestionar Clientes</h6>
                        <p class="text-muted small mb-0">Crear, editar y consultar clientes</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('categorias-examen.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 hover-card" style="transition: all 0.3s ease;">
                    <div class="card-body text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-list-alt fa-3x text-success"></i>
                        </div>
                        <h6 class="mb-1">Categorías</h6>
                        <p class="text-muted small mb-0">Clasificación de exámenes</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="#" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 hover-card" style="transition: all 0.3s ease;">
                    <div class="card-body text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-flask fa-3x text-warning"></i>
                        </div>
                        <h6 class="mb-1">Exámenes</h6>
                        <p class="text-muted small mb-0">Catálogo completo</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('profesionales.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 hover-card" style="transition: all 0.3s ease;">
                    <div class="card-body text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-user-md fa-3x text-info"></i>
                        </div>
                        <h6 class="mb-1">Profesionales</h6>
                        <p class="text-muted small mb-0">Personal médico</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Configuración -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="fas fa-cog me-2"></i>Configuración
            </h5>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('empresa.edit') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 hover-card" style="transition: all 0.3s ease;">
                    <div class="card-body text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-building fa-3x text-secondary"></i>
                        </div>
                        <h6 class="mb-1">Datos de la Empresa</h6>
                        <p class="text-muted small mb-0">Información general y logo</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Pacientes</p>
                            <h3 class="mb-0">0</h3>
                        </div>
                        <i class="fas fa-users fa-3x text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Exámenes Pendientes</p>
                            <h3 class="mb-0">0</h3>
                        </div>
                        <i class="fas fa-flask fa-3x text-warning opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Resultados Validados</p>
                            <h3 class="mb-0">0</h3>
                        </div>
                        <i class="fas fa-check-circle fa-3x text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Ingresos Hoy</p>
                            <h3 class="mb-0">$0</h3>
                        </div>
                        <i class="fas fa-money-bill-wave fa-3x text-info opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Actividad Reciente
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted text-center py-5">
                        <i class="fas fa-inbox fa-3x opacity-25"></i><br>
                        No hay actividad reciente
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-user-circle me-2"></i>Perfil
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user text-white fa-3x"></i>
                        </div>
                    </div>
                    <h6 class="text-center mb-1">{{ Auth::user()->name }}</h6>
                    <p class="text-muted text-center mb-3">{{ Auth::user()->email }}</p>
                    <a href="#" class="btn btn-sm btn-primary w-100">
                        <i class="fas fa-edit me-2"></i>Editar Perfil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
