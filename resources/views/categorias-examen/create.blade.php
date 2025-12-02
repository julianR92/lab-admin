@extends('layouts.app')

@section('title', 'Nueva Categoría - Sistema de Laboratorio')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Nueva Categoría de Examen
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('categorias-examen.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="categoria" class="form-label">
                                    <i class="fas fa-tag me-1"></i>Nombre de la Categoría <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control @error('categoria') is-invalid @enderror"
                                    id="categoria"
                                    name="categoria"
                                    value="{{ old('categoria') }}"
                                    placeholder="Ej: QUÍMICA CLÍNICA"
                                    required
                                >
                                @error('categoria')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="orden" class="form-label">
                                    <i class="fas fa-sort-numeric-down me-1"></i>Orden <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="number"
                                    class="form-control @error('orden') is-invalid @enderror"
                                    id="orden"
                                    name="orden"
                                    value="{{ old('orden', $ultimoOrden + 1) }}"
                                    min="1"
                                    required
                                >
                                @error('orden')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Posición en el listado</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">
                                <i class="fas fa-align-left me-1"></i>Descripción
                            </label>
                            <textarea
                                class="form-control @error('descripcion') is-invalid @enderror"
                                id="descripcion"
                                name="descripcion"
                                rows="3"
                                placeholder="Describe qué tipo de exámenes incluye esta categoría..."
                            >{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-toggle-on me-1"></i>Estado <span class="text-danger">*</span>
                            </label>
                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input @error('status') is-invalid @enderror"
                                    type="checkbox"
                                    id="status"
                                    name="status"
                                    value="1"
                                    {{ old('status', '1') == '1' ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="status">
                                    Categoría activa
                                </label>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Las categorías inactivas no aparecerán en los listados</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('categorias-examen.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Categoría
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
