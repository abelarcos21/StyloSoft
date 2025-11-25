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

const form = useForm({
  cliente_id: props.agenda.cliente_id,
  empleado_id: props.agenda.empleado_id,
  fecha_hora_inicio: props.agenda.fecha_hora_inicio,
  tipo: props.agenda.tipo,
  estado: props.agenda.estado,
  servicios: props.agenda.servicios_ids,
  notas: props.agenda.notas || ''
})

const serviciosSeleccionados = ref(
  props.servicios.filter((s) =>
    (props.agenda.servicios_ids || []).includes(s.id)
  )
);

const duracionTotal = computed(() => {
  return serviciosSeleccionados.value.reduce((sum, s) => sum + s.duracion_minutos, 0)
})

const totalGeneral = computed(() => {
  return serviciosSeleccionados.value.reduce((sum, s) => sum + parseFloat(s.precio), 0)
})

function submit() {
  form.servicios = serviciosSeleccionados.value.map(s => s.id)
  form.put(`/agendas/${props.agenda.id}`)
}

onMounted(() => {
  $('#cliente-select').select2({
    placeholder: "Seleccione un cliente",
    allowClear: true,
    width: '100%',
    data: props.clientes.map(c => ({
        id: c.id,
        text: c.nombre_completo
    }))
  }).val(props.agenda.cliente_id).trigger('change')
    .on('change', function() {
      form.cliente_id = $(this).val()
    })

  $('#empleado-select').select2({
    placeholder: 'Seleccione un empleado',
    allowClear: true,
    width: '100%',
    data: props.empleados.map(e => ({
        id: e.id,
        text: e.nombre_completo
    }))
  }).val(props.agenda.empleado_id).trigger('change')
    .on('change', function() {
      form.empleado_id = $(this).val()
    })

  $('#servicios-select').select2({
    placeholder: 'Seleccione uno o más servicios',
    allowClear: true,
    width: '100%',
    multiple: true,
    data: props.servicios.map(s => ({
        id: s.id,
        text: `${s.nombre} - ${s.duracion_minutos} min - $${s.precio}`
    }))
  }).val(props.agenda.servicios_ids).trigger('change')
    .on('change', function() {
      const ids = $(this).val() || []
      serviciosSeleccionados.value = props.servicios.filter(s => ids.includes(String(s.id)))
    })
})
</script>

<template>
  <AdminLayout title="Editar Agenda">
    <div class="container-fluid px-3">

      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 text-primary fw-bold">
          <i class="fas fa-calendar-edit me-2"></i> Editar Agenda #{{ agenda.id }}
        </h1>
        <a href="/agendas" class="btn btn-secondary">
          <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
      </div>

      <form @submit.prevent="submit">
        <div class="row g-3">

          <!-- Cliente -->
          <div class="col-md-6">
            <div class="card shadow-sm border-0">
              <div class="card-body">
                <label class="form-label fw-semibold">
                  <i class="fas fa-user text-primary me-2"></i> Cliente *
                </label>
                <select 
                  id="cliente-select" 
                  class="form-select"
                  :class="{ 'is-invalid': form.errors.cliente_id }"
                ></select>
                <div v-if="form.errors.cliente_id" class="invalid-feedback d-block">
                  {{ form.errors.cliente_id }}
                </div>
              </div>
            </div>
          </div>

          <!-- Empleado -->
          <div class="col-md-6">
            <div class="card shadow-sm border-0">
              <div class="card-body">
                <label class="form-label fw-semibold">
                  <i class="fas fa-user-tie text-success me-2"></i> Empleado *
                </label>
                <select 
                  id="empleado-select" 
                  class="form-select"
                  :class="{ 'is-invalid': form.errors.empleado_id }"
                ></select>
                <div v-if="form.errors.empleado_id" class="invalid-feedback d-block">
                  {{ form.errors.empleado_id }}
                </div>
              </div>
            </div>
          </div>

          <!-- Fecha y Hora -->
          <div class="col-md-4">
            <div class="card shadow-sm border-0">
              <div class="card-body">
                <label class="form-label fw-semibold">
                  <i class="fas fa-calendar text-info me-2"></i> Fecha y Hora de Inicio *
                </label>
                <input 
                  type="datetime-local" 
                  v-model="form.fecha_hora_inicio" 
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.fecha_hora_inicio }"
                  required
                >
                <div v-if="form.errors.fecha_hora_inicio" class="invalid-feedback">
                  {{ form.errors.fecha_hora_inicio }}
                </div>
              </div>
            </div>
          </div>

          <!-- Tipo -->
          <div class="col-md-4">
            <div class="card shadow-sm border-0">
              <div class="card-body">
                <label class="form-label fw-semibold">
                  <i class="fas fa-tag text-warning me-2"></i> Tipo *
                </label>
                <select 
                  v-model="form.tipo" 
                  class="form-select"
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

          <!-- Estado -->
          <div class="col-md-4">
            <div class="card shadow-sm border-0">
              <div class="card-body">
                <label class="form-label fw-semibold">
                  <i class="fas fa-flag text-danger me-2"></i> Estado *
                </label>
                <select 
                  v-model="form.estado" 
                  class="form-select"
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

          <!-- Servicios -->
          <div class="col-12">
            <div class="card shadow-sm border-0">
              <div class="card-body">
                <label class="form-label fw-semibold">
                  <i class="fas fa-cut text-danger me-2"></i> Servicios *
                </label>
                <select 
                  id="servicios-select" 
                  class="form-select"
                  :class="{ 'is-invalid': form.errors.servicios }"
                ></select>
                <div v-if="form.errors.servicios" class="invalid-feedback d-block">
                  {{ form.errors.servicios }}
                </div>

                <!-- Tabla de servicios seleccionados -->
                <div v-if="serviciosSeleccionados.length" class="mt-3">
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                      <thead class="table-light">
                        <tr>
                          <th>Servicio</th>
                          <th class="text-center">Duración</th>
                          <th class="text-end">Precio</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="srv in serviciosSeleccionados" :key="srv.id">
                          <td>{{ srv.nombre }}</td>
                          <td class="text-center">{{ srv.duracion_minutos }} min</td>
                          <td class="text-end">${{ srv.precio }}</td>
                        </tr>
                        <tr class="table-active fw-bold">
                          <td>TOTAL</td>
                          <td class="text-center">{{ duracionTotal }} min</td>
                          <td class="text-end text-success">${{ totalGeneral.toFixed(2) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Notas -->
          <div class="col-12">
            <div class="card shadow-sm border-0">
              <div class="card-body">
                <label class="form-label fw-semibold">
                  <i class="fas fa-sticky-note text-secondary me-2"></i> Notas
                </label>
                <textarea 
                  v-model="form.notas" 
                  class="form-control" 
                  rows="3"
                  placeholder="Observaciones, alergias, preferencias..."
                  :class="{ 'is-invalid': form.errors.notas }"
                ></textarea>
                <div v-if="form.errors.notas" class="invalid-feedback">
                  {{ form.errors.notas }}
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Botones -->
        <div class="d-flex justify-content-end gap-2 mt-4">
          <a href="/agendas" class="btn btn-secondary">
            <i class="fas fa-times me-2"></i> Cancelar
          </a>
          <button 
            type="submit" 
            class="btn btn-primary"
            :disabled="form.processing"
          >
            <i class="fas fa-save me-2"></i>
            {{ form.processing ? 'Guardando...' : 'Actualizar Agenda' }}
          </button>
        </div>

      </form>

    </div>
  </AdminLayout>
</template>

<style scoped>
.card {
  transition: transform 0.2s;
}
.card:hover {
  transform: translateY(-2px);
}
</style>