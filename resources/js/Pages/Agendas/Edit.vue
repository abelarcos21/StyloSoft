<template>
  <AdminLayout title="Editar Agenda">
    <div class="card shadow-sm border-0">

      <!-- Encabezado -->
      <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
          <i class="fas fa-edit"></i> Editar Agenda
        </h3>

        <!-- Tipo Agenda -->
        <span class="badge px-3 py-2 fs-6"
          :class="form.tipo === 'flash' ? 'bg-danger' : 'bg-primary'">
          {{ form.tipo.toUpperCase() }}
        </span>
      </div>

      <form @submit.prevent="actualizar">
        <div class="card-body">

          <!-- CLIENTE -->
          <div class="form-group mb-3">
            <label><i class="fas fa-user"></i> Cliente</label>
            <select v-model="form.cliente_id"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.cliente_id }">

              <option value="">Seleccione un cliente...</option>
              <option v-for="c in clientes" :key="c.id" :value="c.id">
                {{ c.nombre }}
              </option>
            </select>
            <div class="invalid-feedback" v-if="form.errors.cliente_id">
              {{ form.errors.cliente_id }}
            </div>
          </div>

          <!-- EMPLEADO -->
          <div class="form-group mb-3">
            <label><i class="fas fa-user-tie"></i> Empleado</label>
            <select v-model="form.empleado_id"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.empleado_id }">

              <option value="">Seleccione un empleado...</option>
              <option v-for="e in empleados" :key="e.id" :value="e.id">
                {{ e.nombre }}
              </option>
            </select>
            <div class="invalid-feedback" v-if="form.errors.empleado_id">
              {{ form.errors.empleado_id }}
            </div>
          </div>

          <!-- SERVICIOS MULTIPLE con Select2 -->
          <div class="form-group mb-3">
            <label><i class="fas fa-cut"></i> Servicios</label>
            <select id="servicios-select"
                    multiple
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.servicios }">
              <option v-for="s in servicios" :key="s.id" :value="s.id">
                {{ s.nombre }} — ${{ s.precio }}
              </option>
            </select>
            <small class="text-muted">Puede seleccionar varios servicios con CTRL o clic.</small>
            <div class="invalid-feedback" v-if="form.errors.servicios">
              {{ form.errors.servicios }}
            </div>
          </div>

          <!-- Vista previa de servicios seleccionados -->
          <div v-if="form.servicios.length > 0" class="mb-3">
            <label><strong>Seleccionados:</strong></label><br>
            <span v-for="id in form.servicios" :key="id" class="badge bg-secondary px-3 py-2 me-1 mb-1">
              {{ nombreServicio(id) }}
            </span>
          </div>

          <!-- FECHA Y HORA -->
          <div class="form-group mb-3">
            <label><i class="fas fa-clock"></i> Fecha y Hora</label>
            <input type="datetime-local"
                   v-model="form.fecha_hora"
                   class="form-control"
                   :class="{ 'is-invalid': form.errors.fecha_hora }">
            <div class="invalid-feedback" v-if="form.errors.fecha_hora">
              {{ form.errors.fecha_hora }}
            </div>
          </div>

          <!-- ESTADO -->
          <div class="form-group mb-3">
            <label><i class="fas fa-flag"></i> Estado</label>
            <select v-model="form.estado" class="form-control">
              <option value="pendiente">Pendiente</option>
              <option value="confirmada">Confirmada</option>
              <option value="cancelada">Cancelada</option>
              <option value="completada">Completada</option>
            </select>
          </div>

        </div>

        <!-- Footer -->
        <div class="card-footer text-end">
          <Link href="/agendas" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left"></i> Cancelar
          </Link>

          <button type="submit" class="btn btn-warning" :disabled="form.processing">
            <i class="fas fa-save"></i> Actualizar
          </button>
        </div>

      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { onMounted } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  agenda: Object,
  clientes: Array,
  empleados: Array,
  servicios: Array
})

// Mostrar nombres en los badges seleccionados
const nombreServicio = (id) => {
  const srv = props.servicios.find(s => s.id === id)
  return srv ? srv.nombre : '—'
}

// Formulario Inertia
const form = useForm({
  cliente_id: props.agenda.cliente_id || '',
  empleado_id: props.agenda.empleado_id || '',
  servicios: props.agenda.servicios ? props.agenda.servicios.map(s => s.id) : [],
  fecha_hora: props.agenda.fecha_hora ? props.agenda.fecha_hora.substring(0, 16) : '',
  estado: props.agenda.estado || 'pendiente',
  tipo: props.agenda.tipo || 'local'
})

const actualizar = () => {
  form.put(`/agendas/${props.agenda.id}`)
}

// Inicializar Select2 y sincronizar con v-model
onMounted(() => {
  if (window.$ && $.fn.select2) {
    const select = $('#servicios-select')

    // Inicializar Select2
    select.select2({
      placeholder: 'Seleccione uno o varios servicios',
      width: '100%'
    })

    // Asignar los servicios ya seleccionados
    select.val(form.servicios).trigger('change')

    // Cada vez que cambie Select2, actualizamos el v-model
    select.on('change', () => {
      form.servicios = select.val() ? select.val().map(Number) : []
    })

    console.log('Select2 cargado ✅')
  } else {
    console.log('Select2 NO está cargado ❌')
  }
})
</script>
