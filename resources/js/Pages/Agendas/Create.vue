<template>
  <AdminLayout title="Nueva Agenda">
    <div class="card card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-calendar-plus"></i> Nueva Agenda</h3>
      </div>

      <form @submit.prevent="guardar">
        <div class="card-body">

          <!-- Tipo -->
          <div class="form-group mb-3">
            <label>Tipo de Agenda</label>
            <select v-model="form.tipo" class="form-control" required>
              <option value="local">Agenda Local</option>
              <option value="flash">Agenda Flash</option>
            </select>
          </div>

          <!-- Cliente -->
          <div class="form-group mb-3">
            <label>Cliente</label>
            <select v-model="form.cliente_id" class="form-control" required>
              <option value="">Seleccione</option>
              <option v-for="c in clientes" :key="c.id" :value="c.id">
                {{ c.nombre }} {{ c.apellido }}
              </option>
            </select>
          </div>

          <!-- Empleado -->
          <div class="form-group mb-3">
            <label>Empleado</label>
            <select v-model="form.empleado_id" class="form-control" required>
              <option value="">Seleccione</option>
              <option v-for="e in empleados" :key="e.id" :value="e.id">
                {{ e.nombre }}
              </option>
            </select>
          </div>

          <!-- Servicios múltiples -->
          <div class="form-group mb-3">
            <label>Servicios</label>
            <select
              id="servicios-select"
              v-model="form.servicios"
              multiple
              class="form-control"
              required
            >
              <option v-for="s in servicios" :key="s.id" :value="s.id">
                {{ s.nombre }}
              </option>
            </select>
            <small class="text-muted">Puede seleccionar varios servicios (CTRL + clic)</small>
          </div>

          <!-- Fecha solo si es local -->
          <div class="form-group mb-3" v-if="form.tipo === 'local'">
            <label>Fecha y Hora</label>
            <input type="datetime-local" v-model="form.fecha_hora" class="form-control" required>
          </div>

        </div>

        <div class="card-footer text-end">
          <Link href="/agendas" class="btn btn-secondary me-2">Cancelar</Link>
          <button type="submit" class="btn btn-primary">Guardar</button>
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
  clientes: Array,
  empleados: Array,
  servicios: Array
})

const form = useForm({
  tipo: 'local',
  cliente_id: '',
  empleado_id: '',
  servicios: [],
  fecha_hora: '',
  estado: 'pendiente'
})

const guardar = () => {
  // Si tipo flash, ponemos fecha actual automáticamente
  if (form.tipo === 'flash') {
    const now = new Date()
    form.fecha_hora = now.toISOString().substring(0,16)
  }

  form.post('/agendas', {
    onSuccess: () => form.reset()
  })
}

// Inicializar Select2 después de que el DOM esté listo
onMounted(() => {
  if (window.$ && $.fn.select2) {
    $('#servicios-select').select2({
      placeholder: 'Seleccione uno o varios servicios',
      width: '100%'
    })

    // Cuando cambie Select2, actualizamos el v-model
    $('#servicios-select').on('change', function() {
      form.servicios = $(this).val() || []
    })

    console.log('Select2 cargado ✅')
  } else {
    console.log('Select2 NO está cargado ❌')
  }
})
</script>
