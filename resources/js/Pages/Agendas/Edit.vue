<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  agenda: Object,
  clientes: Array,
  empleados: Array,
  servicios: Array
})

// Inicializamos el formulario con los datos que vienen del backend
const form = useForm({
  cliente_id: props.agenda.cliente_id || null,
  empleado_id: props.agenda.empleado_id || null,
  fecha_hora_inicio: props.agenda.fecha_hora_inicio || '',
  tipo: props.agenda.tipo || 'estandar',
  estado: props.agenda.estado || 'pendiente',
  servicios: props.agenda.servicios_ids || [], // Array de IDs seleccionados
  notas: props.agenda.notas || ''
})

const serviciosSeleccionados = ref([])

const duracionTotal = computed(() => {
  return serviciosSeleccionados.value.reduce((sum, s) => sum + s.duracion_minutos, 0)
})

const totalGeneral = computed(() => {
  return serviciosSeleccionados.value.reduce((sum, s) => sum + parseFloat(s.precio), 0)
})

function submit() {
  form.put(`/agendas/${props.agenda.id}`)
}

onMounted(() => {
  // Inicializar y precargar Cliente
  const $clienteSelect = $('#cliente-select');
  $clienteSelect.select2({
    placeholder: 'Buscar o Seleccione un cliente',
    allowClear: true,
    width: '100%',
    data: props.clientes.map((e) => ({
        id: e.id,
        text: e.nombre_completo,
    })),
  }).on('change', function() {
    form.cliente_id = $(this).val() || null;
  });
  if (form.cliente_id) $clienteSelect.val(form.cliente_id).trigger('change');

  // Inicializar y precargar Empleado
  const $empleadoSelect = $('#empleado-select');
  $empleadoSelect.select2({
    width: '100%',
    placeholder: 'Buscar o Seleccione un empleado',
    allowClear: true,
    data: props.empleados.map((e) => ({
        id: e.id,
        text: e.nombre_completo,
    })),
  }).on('change', function() {
    form.empleado_id = $(this).val() || null;
  });
  if (form.empleado_id) $empleadoSelect.val(form.empleado_id).trigger('change');

  // Inicializar y precargar Servicios
  const $serviciosSelect = $("#servicios-select");
  $serviciosSelect.select2({
    width: "100%",
    placeholder: 'Buscar o Seleccione servicios',
    multiple: true,
    data: props.servicios.map((s) => ({
      id: s.id,
      text: `${s.nombre} - ${s.duracion_minutos} min - $${s.precio}`,
    })),
  }).on("change", () => {
    const ids = $serviciosSelect.val() || [];
    form.servicios = ids.map(id => Number(id)); // Mantener el array de IDs en el form
    serviciosSeleccionados.value = props.servicios.filter((s) =>
      ids.includes(String(s.id))
    );
  });

  if (form.servicios.length > 0) {
    $serviciosSelect.val(form.servicios).trigger('change');
  }
})
</script>

<template>
  <AdminLayout title="Editar Agenda">
    <div class="container-fluid px-0 px-md-3">

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h1 class="h4 brand-accent fw-bold mb-0">
          <i class="fas fa-calendar-edit me-2"></i> Editar Agenda #{{ agenda.id }}
        </h1>
        <Link href="/agendas" class="btn btn-light rounded-pill px-4 shadow-sm text-muted fw-medium">
          <i class="fas fa-arrow-left me-2"></i> Volver
        </Link>
      </div>

      <form @submit.prevent="submit">
        <div class="row g-3">

          <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
              <div class="card-body p-4">
                <label class="form-label fw-semibold text-dark">
                  <i class="fas fa-user text-primary me-2"></i> Cliente <span class="text-danger">*</span>
                </label>
                <select
                  id="cliente-select"
                  class="form-select bg-light border-0 rounded-3 focus-ring-none"
                  :class="{ 'is-invalid': form.errors.cliente_id }"
                ></select>
                <div v-if="form.errors.cliente_id" class="invalid-feedback d-block">
                  {{ form.errors.cliente_id }}
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
              <div class="card-body p-4">
                <label class="form-label fw-semibold text-dark">
                  <i class="fas fa-user-tie text-success me-2"></i> Empleado <span class="text-danger">*</span>
                </label>
                <select
                  id="empleado-select"
                  class="form-select bg-light border-0 rounded-3 focus-ring-none"
                  :class="{ 'is-invalid': form.errors.empleado_id }"
                ></select>
                <div v-if="form.errors.empleado_id" class="invalid-feedback d-block">
                  {{ form.errors.empleado_id }}
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
              <div class="card-body p-4">
                <label class="form-label fw-semibold text-dark">
                  <i class="fas fa-calendar-alt text-info me-2"></i> Fecha y Hora <span class="text-danger">*</span>
                </label>
                <input
                  type="datetime-local"
                  v-model="form.fecha_hora_inicio"
                  class="form-control bg-light border-0 rounded-3 focus-ring-none py-2"
                  :class="{ 'is-invalid': form.errors.fecha_hora_inicio }"
                  required
                >
                <div v-if="form.errors.fecha_hora_inicio" class="invalid-feedback">
                  {{ form.errors.fecha_hora_inicio }}
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
              <div class="card-body p-4">
                <label class="form-label fw-semibold text-dark">
                  <i class="fas fa-tag text-warning me-2"></i> Tipo de Cita <span class="text-danger">*</span>
                </label>
                <select
                  v-model="form.tipo"
                  class="form-select bg-light border-0 rounded-3 focus-ring-none py-2"
                  :class="{ 'is-invalid': form.errors.tipo }"
                >
                  <option value="estandar">Estándar</option>
                  <option value="express">Express</option>
                </select>
                <div v-if="form.errors.tipo" class="invalid-feedback">
                  {{ form.errors.tipo }}
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
              <div class="card-body p-4">
                <label class="form-label fw-semibold text-dark">
                  <i class="fas fa-flag text-danger me-2"></i> Estado <span class="text-danger">*</span>
                </label>
                <select
                  v-model="form.estado"
                  class="form-select bg-light border-0 rounded-3 focus-ring-none py-2"
                  :class="{ 'is-invalid': form.errors.estado }"
                >
                  <option value="pendiente">Pendiente</option>
                  <option value="confirmada">Confirmada</option>
                  <option value="en_proceso">En Proceso</option>
                  <option value="completada">Completada</option>
                  <option value="cancelada">Cancelada</option>
                  <option value="no_asistio">No asistió</option>
                </select>
                <div v-if="form.errors.estado" class="invalid-feedback">
                  {{ form.errors.estado }}
                </div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-body p-4">
                <label class="form-label fw-semibold text-dark">
                  <i class="fas fa-cut text-danger me-2"></i> Servicios <span class="text-danger">*</span>
                </label>
                <select
                  id="servicios-select"
                  class="form-select bg-light border-0 rounded-3 focus-ring-none"
                  :class="{ 'is-invalid': form.errors.servicios }"
                ></select>
                <div v-if="form.errors.servicios" class="invalid-feedback d-block">
                  {{ form.errors.servicios }}
                </div>

                <div v-if="serviciosSeleccionados.length" class="mt-4 border rounded-4 overflow-hidden">
                  <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 custom-table">
                      <thead class="bg-light text-muted">
                        <tr>
                          <th class="border-0 font-weight-semibold ps-4">Servicio</th>
                          <th class="text-center border-0 font-weight-semibold">Duración</th>
                          <th class="text-end border-0 font-weight-semibold pe-4">Precio</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="srv in serviciosSeleccionados" :key="srv.id">
                          <td class="ps-4 fw-medium text-dark">{{ srv.nombre }}</td>
                          <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2 fw-medium">
                              {{ srv.duracion_minutos }} min
                            </span>
                          </td>
                          <td class="text-end pe-4 text-dark fw-medium">${{ parseFloat(srv.precio).toFixed(2) }}</td>
                        </tr>
                      </tbody>
                      <tfoot class="bg-light">
                        <tr>
                          <td class="fw-bold ps-4 text-dark">TOTAL</td>
                          <td class="text-center fw-bold text-dark">{{ duracionTotal }} min</td>
                          <td class="text-end pe-4 fw-bold brand-accent fs-5">${{ totalGeneral.toFixed(2) }}</td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-body p-4">
                <label class="form-label fw-semibold text-dark">
                  <i class="fas fa-sticky-note text-secondary me-2"></i> Notas y Observaciones
                </label>
                <textarea
                  v-model="form.notas"
                  class="form-control bg-light border-0 rounded-4 focus-ring-none p-3"
                  rows="3"
                  placeholder="Observaciones, alergias, preferencias del cliente..."
                  :class="{ 'is-invalid': form.errors.notas }"
                ></textarea>
                <div v-if="form.errors.notas" class="invalid-feedback">
                  {{ form.errors.notas }}
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="d-flex justify-content-end align-items-center gap-3 mt-4 mb-5">
          <Link href="/agendas" class="btn btn-light rounded-pill px-4 fw-medium text-muted">
            <i class="fas fa-times me-2"></i> Cancelar
          </Link>
          <button
            type="submit"
            class="btn btn-brand rounded-pill px-4 shadow-sm fw-medium"
            :disabled="form.processing"
          >
            <i v-if="form.processing" class="fas fa-spinner fa-spin me-2"></i>
            <i v-else class="fas fa-save me-2"></i>
            {{ form.processing ? 'Guardando...' : 'Actualizar Agenda' }}
          </button>
        </div>

      </form>

    </div>
  </AdminLayout>
</template>

<style scoped>
/* Variables y colores corporativos */
.brand-accent {
  color: #d84b72;
}

.btn-brand {
  background-color: #d84b72;
  color: white;
  transition: all 0.3s ease;
}

.btn-brand:hover:not(:disabled) {
  background-color: #c03d61;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(216, 75, 114, 0.3) !important;
}

.btn-brand:disabled {
  background-color: #e587a1;
  border-color: #e587a1;
  cursor: not-allowed;
}

/* Hover de tarjetas */
.card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
}

/* Quitar bordes al hacer focus */
.focus-ring-none:focus {
  box-shadow: none;
  border-color: #dee2e6;
  background-color: #fff !important;
}

/* Tabla de servicios seleccionados */
.custom-table th {
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
}
.bg-secondary-subtle { background-color: #e2e3e5 !important; }

/* Estilos sobreescritos para integrar Select2 */
:deep(.select2-container--default .select2-selection--single),
:deep(.select2-container--default .select2-selection--multiple) {
  background-color: #f8f9fa;
  border: 1px solid transparent;
  border-radius: 0.5rem;
  min-height: 42px;
  padding: 4px 8px;
}

:deep(.select2-container--default.select2-container--focus .select2-selection--multiple),
:deep(.select2-container--default.select2-container--focus .select2-selection--single) {
  background-color: #fff;
  border-color: #dee2e6;
}

:deep(.select2-container--default .select2-selection--single .select2-selection__rendered) {
  line-height: 32px;
  color: #212529;
}

:deep(.select2-container--default .select2-selection--single .select2-selection__arrow) {
  height: 40px;
}

:deep(.select2-container--default .select2-selection--multiple .select2-selection__choice) {
  background-color: #fff;
  border: 1px solid #dee2e6;
  border-radius: 4px;
  padding: 2px 6px;
  margin-top: 4px;
}
</style>
