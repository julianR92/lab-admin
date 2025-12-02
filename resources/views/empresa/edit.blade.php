@extends('layouts.app')

@section('title', 'Configuración de la Empresa - Sistema de Laboratorio')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-building me-2"></i>Configuración de la Empresa</h2>
            <p class="text-muted">Edita la información de tu laboratorio clínico</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información General</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('empresa.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nit" class="form-label">NIT <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('nit') is-invalid @enderror"
                                       id="nit"
                                       name="nit"
                                       value="{{ old('nit', $empresa->nit) }}"
                                       placeholder="900123456-7">
                                @error('nit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="razon_social" class="form-label">Razón Social <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('razon_social') is-invalid @enderror"
                                       id="razon_social"
                                       name="razon_social"
                                       value="{{ old('razon_social', $empresa->razon_social) }}"
                                       placeholder="Laboratorio Clínico ABC">
                                @error('razon_social')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="direccion" class="form-label">Dirección <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('direccion') is-invalid @enderror"
                                       id="direccion"
                                       name="direccion"
                                       value="{{ old('direccion', $empresa->direccion) }}"
                                       placeholder="Calle 45 #23-15">
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="barrio" class="form-label">Barrio</label>
                                <input type="text"
                                       class="form-control @error('barrio') is-invalid @enderror"
                                       id="barrio"
                                       name="barrio"
                                       value="{{ old('barrio', $empresa->barrio) }}"
                                       placeholder="Centro">
                                @error('barrio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="ciudad" class="form-label">Ciudad <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('ciudad') is-invalid @enderror"
                                   id="ciudad"
                                   name="ciudad"
                                   value="{{ old('ciudad', $empresa->ciudad) }}"
                                   placeholder="Bogotá">
                            @error('ciudad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="telefono_uno" class="form-label">Teléfono Principal <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('telefono_uno') is-invalid @enderror"
                                       id="telefono_uno"
                                       name="telefono_uno"
                                       value="{{ old('telefono_uno', $empresa->telefono_uno) }}"
                                       placeholder="(601) 234-5678">
                                @error('telefono_uno')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="telefono_dos" class="form-label">Teléfono Secundario</label>
                                <input type="text"
                                       class="form-control @error('telefono_dos') is-invalid @enderror"
                                       id="telefono_dos"
                                       name="telefono_dos"
                                       value="{{ old('telefono_dos', $empresa->telefono_dos) }}"
                                       placeholder="(601) 234-5679">
                                @error('telefono_dos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $empresa->email) }}"
                                   placeholder="info@laboratorio.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-image me-2"></i>Logo de la Empresa</h5>
                </div>
                <div class="card-body text-center">
                    @if($empresa->tieneLogoConfigurado())
                        <div class="mb-3">
                            <img src="{{ $empresa->logo_url }}"
                                 alt="Logo de {{ $empresa->razon_social }}"
                                 class="img-fluid rounded shadow-sm"
                                 style="max-height: 200px;">
                        </div>
                        <form action="{{ route('empresa.delete-logo') }}" method="POST" class="mb-3">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar el logo?')">
                                <i class="fas fa-trash me-1"></i>Eliminar Logo
                            </button>
                        </form>
                        <hr>
                    @else
                        <div class="mb-3">
                            <i class="fas fa-image fa-5x text-muted"></i>
                            <p class="text-muted mt-3">No hay logo configurado</p>
                        </div>
                    @endif

                    <form action="{{ route('empresa.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Campos ocultos con los valores actuales -->
                        <input type="hidden" name="nit" value="{{ $empresa->nit }}">
                        <input type="hidden" name="razon_social" value="{{ $empresa->razon_social }}">
                        <input type="hidden" name="direccion" value="{{ $empresa->direccion }}">
                        <input type="hidden" name="barrio" value="{{ $empresa->barrio }}">
                        <input type="hidden" name="ciudad" value="{{ $empresa->ciudad }}">
                        <input type="hidden" name="telefono_uno" value="{{ $empresa->telefono_uno }}">
                        <input type="hidden" name="telefono_dos" value="{{ $empresa->telefono_dos }}">
                        <input type="hidden" name="email" value="{{ $empresa->email }}">

                        <div class="mb-3">
                            <label for="logo" class="form-label">{{ $empresa->tieneLogoConfigurado() ? 'Cambiar' : 'Subir' }} Logo</label>
                            <input type="file"
                                   class="form-control @error('logo') is-invalid @enderror"
                                   id="logo"
                                   name="logo"
                                   accept="image/png,image/jpeg,image/jpg">
                            <small class="text-muted">PNG, JPG o JPEG. Máximo 2MB.</small>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-upload me-1"></i>{{ $empresa->tieneLogoConfigurado() ? 'Actualizar' : 'Subir' }} Logo
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-2">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>
                        La razón social se mostrará en el encabezado de la aplicación y en los reportes PDF.
                    </p>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>
                        El logo aparecerá en el dashboard y en los documentos generados.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
