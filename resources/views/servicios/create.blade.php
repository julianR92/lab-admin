@extends('layouts.app')

@section('title', 'Nueva Orden de Servicio - Sistema de Laboratorio')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2">
                <i class="fas fa-file-medical me-2"></i>Nueva Orden de Servicio
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('servicios.index') }}">Servicios</a></li>
                    <li class="breadcrumb-item active">Nuevo</li>
                </ol>
            </nav>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Errores en el formulario:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form id="servicioForm" method="POST" action="{{ route('servicios.store') }}">
        @csrf

        <div class="row">
            <!-- Cliente -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Información del Cliente</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9">
                                <label for="buscarCliente" class="form-label">Buscar Cliente <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="buscarCliente" placeholder="Nombre, apellido o documento..." autocomplete="off">
                                <div id="clienteResults" class="list-group mt-2 position-absolute" style="z-index: 1000; max-height: 300px; overflow-y: auto; display: none;"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label d-block">&nbsp;</label>
                                <a href="{{ route('clientes.create') }}" class="btn btn-success w-100" target="_blank">
                                    <i class="fas fa-plus me-2"></i>Nuevo Cliente
                                </a>
                            </div>
                        </div>

                        <input type="hidden" name="cliente_id" id="cliente_id" required>

                        <div id="clienteInfo" class="mt-3" style="display: none;">
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Nombre:</strong> <span id="infoNombre"></span><br>
                                        <strong>Documento:</strong> <span id="infoDocumento"></span><br>
                                        <strong>Género:</strong> <span id="infoGenero"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Edad:</strong> <span id="infoEdad"></span> años<br>
                                        <strong>Teléfono:</strong> <span id="infoTelefono"></span><br>
                                        <strong>EPS:</strong> <span id="infoEps"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Exámenes -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-vial me-2"></i>Selección de Exámenes</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="examenSelect" class="form-label">Seleccionar Examen <span class="text-danger">*</span></label>
                            <select class="form-select" id="examenSelect">
                                <option value="">-- Seleccione un examen --</option>
                                @foreach ($examenes as $categoria => $examenesCategoria)
                                    <optgroup label="{{ $categoria }}">
                                        @foreach ($examenesCategoria as $examen)
                                            <option value="{{ $examen->id }}" data-precio="{{ $examen->valor_total }}" data-nombre="{{ $examen->codigo }} - {{ $examen->nombre }}">
                                                {{ $examen->codigo }} - {{ $examen->nombre }} ({{ number_format($examen->valor_total, 0, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40%">Examen</th>
                                        <th width="20%">Precio Unitario</th>
                                        <th width="15%">Cantidad</th>
                                        <th width="15%">Subtotal</th>
                                        <th width="10%">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="examenesTable">
                                    <tr id="emptyRow">
                                        <td colspan="5" class="text-center text-muted">No se han agregado exámenes</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="table-info">
                                        <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                                        <td><strong>$<span id="totalGeneral">0</span></strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <input type="hidden" name="examenes" id="examenes" required>
                        <input type="hidden" name="precios" id="precios" required>
                    </div>
                </div>
            </div>

            <!-- Información de Pago -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Información de Pago</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('fecha') is-invalid @enderror"
                                   id="fecha" name="fecha" max="{{ date('Y-m-d') }}" value="{{ old('fecha', date('Y-m-d')) }}" required>
                            @error('fecha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="valor_pagado" class="form-label">Valor a Pagar</label>
                            <input type="number" class="form-control @error('valor_pagado') is-invalid @enderror"
                                   id="valor_pagado" name="valor_pagado"  value="{{ old('valor_pagado', 0) }}"
                                   min="0" step="0.01" readonly>
                            @error('valor_pagado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">El valor se actualiza automáticamente con el total de la orden</small>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="pagoParcial">
                            <label class="form-check-label" for="pagoParcial">
                                Registrar pago parcial
                            </label>
                        </div>

                        <div class="mb-3" id="campoPagoParcial" style="display: none;">
                            <label for="valor_parcial" class="form-label">Valor del Pago Parcial</label>
                            <input type="number" class="form-control" id="valor_parcial"
                                   min="0" step="0.01" placeholder="Ingrese el valor a pagar">
                            <div class="invalid-feedback" id="errorPagoParcial"></div>
                            <small class="text-muted">Ingrese un valor menor al total</small>
                        </div>

                        <div class="mb-3">
                            <label for="medio_pago" class="form-label">Medio de Pago</label>
                            <select class="form-select @error('medio_pago') is-invalid @enderror" id="medio_pago" name="medio_pago">
                                <option value="">-- Seleccione --</option>
                                <option value="Efectivo" {{ old('medio_pago') == 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="Tarjeta débito" {{ old('medio_pago') == 'Tarjeta débito' ? 'selected' : '' }}>Tarjeta débito</option>
                                <option value="Tarjeta crédito" {{ old('medio_pago') == 'Tarjeta crédito' ? 'selected' : '' }}>Tarjeta crédito</option>
                                <option value="Transferencia" {{ old('medio_pago') == 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                                <option value="Nequi" {{ old('medio_pago') == 'Nequi' ? 'selected' : '' }}>Nequi</option>
                                <option value="Daviplata" {{ old('medio_pago') == 'Daviplata' ? 'selected' : '' }}>Daviplata</option>
                            </select>
                            @error('medio_pago')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control @error('observaciones') is-invalid @enderror"
                                      id="observaciones" name="observaciones" rows="3">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-2"></i>Crear Orden
                        </button>
                        <a href="{{ route('servicios.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const buscarInput = document.getElementById('buscarCliente');
    const clienteResults = document.getElementById('clienteResults');
    const clienteInfo = document.getElementById('clienteInfo');
    const clienteIdInput = document.getElementById('cliente_id');
    const examenSelect = document.getElementById('examenSelect');
    const examenesTable = document.getElementById('examenesTable');
    const emptyRow = document.getElementById('emptyRow');

    let examenesAgregados = [];
    let preciosExamenes = [];
    let cantidadesExamenes = [];
    let timeoutId = null;

    // Búsqueda de cliente con autocompletado
    buscarInput.addEventListener('input', function() {
        const query = this.value.trim();

        clearTimeout(timeoutId);

        if (query.length < 2) {
            clienteResults.style.display = 'none';
            return;
        }

        timeoutId = setTimeout(() => {
            fetch(`/api/clientes/buscar?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        clienteResults.innerHTML = data.map(cliente => `
                            <a href="#" class="list-group-item list-group-item-action" data-cliente='${JSON.stringify(cliente)}'>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>${cliente.nombre_completo}</strong><br>
                                        <small class="text-muted">${cliente.tipo_documento}: ${cliente.documento} | Edad: ${cliente.edad} años | Género: ${cliente.genero}</small>
                                    </div>
                                </div>
                            </a>
                        `).join('');
                        clienteResults.style.display = 'block';
                    } else {
                        clienteResults.innerHTML = '<div class="list-group-item text-muted">No se encontraron resultados</div>';
                        clienteResults.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error al buscar clientes:', error);
                    clienteResults.style.display = 'none';
                });
        }, 300);
    });

    // Seleccionar cliente
    clienteResults.addEventListener('click', function(e) {
        e.preventDefault();
        const item = e.target.closest('.list-group-item');
        if (item && item.dataset.cliente) {
            const cliente = JSON.parse(item.dataset.cliente);

            clienteIdInput.value = cliente.id;
            buscarInput.value = cliente.nombre_completo;

            document.getElementById('infoNombre').textContent = cliente.nombre_completo;
            document.getElementById('infoDocumento').textContent = `${cliente.tipo_documento}: ${cliente.documento}`;
            document.getElementById('infoGenero').textContent = cliente.genero;
            document.getElementById('infoEdad').textContent = cliente.edad;
            document.getElementById('infoTelefono').textContent = cliente.telefono || 'No registrado';
            document.getElementById('infoEps').textContent = cliente.eps || 'No registrado';

            clienteInfo.style.display = 'block';
            clienteResults.style.display = 'none';
        }
    });

    // Cerrar resultados al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!buscarInput.contains(e.target) && !clienteResults.contains(e.target)) {
            clienteResults.style.display = 'none';
        }
    });

    // Agregar examen
    examenSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (!selectedOption.value) return;

        const examenId = selectedOption.value;
        const examenNombre = selectedOption.dataset.nombre;
        const examenPrecio = parseFloat(selectedOption.dataset.precio);

        // Verificar si ya fue agregado
        if (examenesAgregados.includes(examenId)) {
            alert('Este examen ya fue agregado');
            this.value = '';
            return;
        }

        // Agregar a los arrays
        examenesAgregados.push(examenId);
        preciosExamenes.push(examenPrecio);
        cantidadesExamenes.push(1);

        // Ocultar fila vacía
        emptyRow.style.display = 'none';

        // Agregar fila
        const row = document.createElement('tr');
        row.dataset.examenId = examenId;
        row.innerHTML = `
            <td>${examenNombre}</td>
            <td>$${formatNumber(examenPrecio)}</td>
            <td>
                <input type="number" class="form-control form-control-sm cantidad-input"
                       value="1" min="1" max="99" data-index="${examenesAgregados.length - 1}">
            </td>
            <td class="text-end">
                <strong>$<span class="subtotal">${formatNumber(examenPrecio)}</span></strong>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm eliminar-examen" data-examen-id="${examenId}">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        examenesTable.appendChild(row);

        // Reset select
        this.value = '';

        actualizarTotal();
        actualizarInputsHidden();
    });

    // Eliminar examen
    examenesTable.addEventListener('click', function(e) {
        if (e.target.closest('.eliminar-examen')) {
            const button = e.target.closest('.eliminar-examen');
            const examenId = button.dataset.examenId;
            const row = button.closest('tr');
            const index = examenesAgregados.indexOf(examenId);

            if (index > -1) {
                examenesAgregados.splice(index, 1);
                preciosExamenes.splice(index, 1);
                cantidadesExamenes.splice(index, 1);
            }

            row.remove();

            if (examenesAgregados.length === 0) {
                emptyRow.style.display = '';
            }

            actualizarTotal();
            actualizarInputsHidden();
        }
    });

    // Actualizar cantidad
    examenesTable.addEventListener('input', function(e) {
        if (e.target.classList.contains('cantidad-input')) {
            const index = parseInt(e.target.dataset.index);
            const cantidad = parseInt(e.target.value) || 1;
            const precio = preciosExamenes[index];

            cantidadesExamenes[index] = cantidad;

            // Actualizar subtotal de la fila
            const row = e.target.closest('tr');
            const subtotal = precio * cantidad;
            row.querySelector('.subtotal').textContent = formatNumber(subtotal);

            // Actualizar total general
            actualizarTotal();
            actualizarInputsHidden();
        }
    });

    function actualizarTotal() {
        const total = preciosExamenes.reduce((sum, precio, index) => {
            const cantidad = cantidadesExamenes[index] || 1;
            return sum + (precio * cantidad);
        }, 0);
        document.getElementById('totalGeneral').textContent = formatNumber(total);

        // Actualizar valor a pagar automáticamente
        const pagoParcialCheck = document.getElementById('pagoParcial');
        if (!pagoParcialCheck.checked) {
            document.getElementById('valor_pagado').value = total;
        }
    }

    function actualizarInputsHidden() {
        document.getElementById('examenes').value = JSON.stringify(examenesAgregados);
        // Guardar los subtotales (precio * cantidad) en el array de precios
        const subtotales = preciosExamenes.map((precio, index) => precio * (cantidadesExamenes[index] || 1));
        document.getElementById('precios').value = JSON.stringify(subtotales);
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('es-CO').format(num);
    }

    // Manejo del checkbox de pago parcial
    const pagoParcialCheck = document.getElementById('pagoParcial');
    const campoPagoParcial = document.getElementById('campoPagoParcial');
    const valorPagadoInput = document.getElementById('valor_pagado');
    const valorParcialInput = document.getElementById('valor_parcial');
    const errorPagoParcial = document.getElementById('errorPagoParcial');

    pagoParcialCheck.addEventListener('change', function() {
        if (this.checked) {
            campoPagoParcial.style.display = 'block';
            valorPagadoInput.value = 0;
        } else {
            campoPagoParcial.style.display = 'none';
            valorParcialInput.value = '';
            errorPagoParcial.textContent = '';
            valorParcialInput.classList.remove('is-invalid');
            // Restaurar el valor total
            const total = preciosExamenes.reduce((sum, precio, index) => {
                const cantidad = cantidadesExamenes[index] || 1;
                return sum + (precio * cantidad);
            }, 0);
            valorPagadoInput.value = total;
        }
    });

    // Validar el valor del pago parcial
    valorParcialInput.addEventListener('input', function() {
        const valorParcial = parseFloat(this.value) || 0;
        const total = preciosExamenes.reduce((sum, precio, index) => {
            const cantidad = cantidadesExamenes[index] || 1;
            return sum + (precio * cantidad);
        }, 0);

        if (valorParcial > total) {
            this.classList.add('is-invalid');
            errorPagoParcial.textContent = `El valor no puede ser mayor al total ($${formatNumber(total)})`;
            valorPagadoInput.value = 0;
        } else if (valorParcial <= 0) {
            this.classList.add('is-invalid');
            errorPagoParcial.textContent = 'El valor debe ser mayor a 0';
            valorPagadoInput.value = 0;
        } else {
            this.classList.remove('is-invalid');
            errorPagoParcial.textContent = '';
            valorPagadoInput.value = valorParcial;
        }
    });

    // Validación antes de enviar
    document.getElementById('servicioForm').addEventListener('submit', function(e) {
        if (examenesAgregados.length === 0) {
            e.preventDefault();
            alert('Debe agregar al menos un examen');
            return false;
        }

        if (!clienteIdInput.value) {
            e.preventDefault();
            alert('Debe seleccionar un cliente');
            buscarInput.focus();
            return false;
        }

        // Validar pago parcial
        if (pagoParcialCheck.checked) {
            const valorParcial = parseFloat(valorParcialInput.value) || 0;
            const total = preciosExamenes.reduce((sum, precio, index) => {
                const cantidad = cantidadesExamenes[index] || 1;
                return sum + (precio * cantidad);
            }, 0);

            if (valorParcial <= 0 || valorParcial > total) {
                e.preventDefault();
                alert('Debe ingresar un valor de pago parcial válido');
                valorParcialInput.focus();
                return false;
            }
        }
    });
});
</script>
@endpush
