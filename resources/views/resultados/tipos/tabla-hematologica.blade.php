{{-- Vista para exámenes con tabla hematológica --}}
<div class="alert alert-info mb-3">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Progreso:</strong> <span id="progreso-texto">0 de {{ $servicioExamen->examen->parametros->count() }} parámetros capturados</span>
    <div class="progress mt-2" style="height: 25px;">
        <div id="progreso-barra" class="progress-bar bg-success" role="progressbar" style="width: 0%">0%</div>
    </div>
</div>

@foreach ($parametrosAgrupados as $seccion => $parametros)
    <div class="card mb-3">
        <div class="card-header" data-bs-toggle="collapse" data-bs-target="#seccion-{{ $loop->index }}" style="cursor: pointer;">
            <h6 class="mb-0">
                <i class="fas fa-chevron-down me-2"></i>{{ $seccion ?: 'General' }}
                <span class="badge bg-secondary float-end">{{ $parametros->count() }} parámetros</span>
            </h6>
        </div>
        <div id="seccion-{{ $loop->index }}" class="collapse {{ $loop->first ? 'show' : '' }}">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th width="35%">Parámetro</th>
                                <th width="20%">Valor</th>
                                <th width="15%">Unidad</th>
                                <th width="30%">Rango Referencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($parametros as $parametro)
                                @include('resultados.tipos.partials.fila-parametro', ['parametro' => $parametro])
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endforeach

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input[name^="resultados"], select[name^="resultados"]');
    const totalParametros = {{ $servicioExamen->examen->parametros->count() }};

    function actualizarProgreso() {
        let capturados = 0;
        inputs.forEach(input => {
            if (input.value && input.value.trim() !== '') {
                capturados++;
            }
        });

        const porcentaje = Math.round((capturados / totalParametros) * 100);
        document.getElementById('progreso-texto').textContent = `${capturados} de ${totalParametros} parámetros capturados`;
        document.getElementById('progreso-barra').style.width = porcentaje + '%';
        document.getElementById('progreso-barra').textContent = porcentaje + '%';
    }

    inputs.forEach(input => {
        input.addEventListener('input', actualizarProgreso);
        input.addEventListener('change', actualizarProgreso);
    });

    // Actualizar al cargar
    actualizarProgreso();
});
</script>
@endpush
