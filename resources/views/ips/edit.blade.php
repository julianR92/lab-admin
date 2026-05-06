@extends('layouts.app')

@section('title', 'Editar IPS - Sistema de Laboratorio')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">
                        <i class="fas fa-hospital me-2"></i>Editar IPS
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('ips.update', $ip) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-building me-2 text-primary"></i>Información de la IPS
                        </h5>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="razon_social" class="form-label">
                                    <i class="fas fa-hospital me-1"></i>Razón Social <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control @error('razon_social') is-invalid @enderror"
                                    id="razon_social"
                                    name="razon_social"
                                    value="{{ old('razon_social', $ip->razon_social) }}"
                                    required
                                >
                                @error('razon_social')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nit" class="form-label">
                                    <i class="fas fa-id-card me-1"></i>NIT <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control @error('nit') is-invalid @enderror"
                                    id="nit"
                                    name="nit"
                                    value="{{ old('nit', $ip->nit) }}"
                                    required
                                >
                                @error('nit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="correo_electronico" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Correo Electrónico <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="email"
                                    class="form-control @error('correo_electronico') is-invalid @enderror"
                                    id="correo_electronico"
                                    name="correo_electronico"
                                    value="{{ old('correo_electronico', $ip->correo_electronico) }}"
                                    required
                                >
                                @error('correo_electronico')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="logo" class="form-label">
                                    <i class="fas fa-image me-1"></i>Logo
                                </label>
                                @if($ip->logo)
                                    <div class="mb-2 d-flex align-items-center gap-3">
                                        <img src="{{ asset('storage/' . $ip->logo) }}" alt="Logo actual" class="img-thumbnail" style="max-height: 80px;">
                                        <p class="text-muted small mb-0">Logo actual (sube uno nuevo para reemplazarlo)</p>
                                    </div>
                                @endif
                                <input
                                    type="file"
                                    class="form-control @error('logo') is-invalid @enderror"
                                    id="logo"
                                    name="logo"
                                    accept="image/png,image/jpeg,image/jpg"
                                >
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">PNG, JPG o JPEG. Máximo 2MB</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('ips.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Actualizar IPS
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
