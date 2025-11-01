<template>
  <AdminLayout title="Nueva Agenda">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-calendar-plus"></i> Crear Nueva Agenda</h3>
      </div>

      <form @submit.prevent="guardar">
        <div class="card-body">
          <div class="form-group">
            <label>Cliente</label>
            <select v-model="form.cliente_id" class="form-control" required>
              <option value="">Seleccione un cliente</option>
              <option v-for="c in clientes" :key="c.id" :value="c.id">{{ c.nombre }}</option>
            </select>
          </div>

          <div class="form-group">
            <label>Empleado</label>
            <select v-model="form.empleado_id" class="form-control" required>
              <option value="">Seleccione un empleado</option>
              <option v-for="e in empleados" :key="e.id" :value="e.id">{{ e.nombre }}</option>
            </select>
          </div>

          <div class="form-group">
            <label>Servicio</label>
            <select v-model="form.servicio_id" class="form-control" required>
              <option value="">Seleccione un servicio</option>
              <option v-for="s in servicios" :key="s.id" :value="s.id">{{ s.nombre }}</option>
            </select>
          </div>

          <div class="form-group">
            <label>Fecha y Hora</label>
            <input type="datetime-local" v-model="form.fecha_hora" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Estado</label>
            <select v-model="form.estado" class="form-control" required>
              <option value="pendiente">Pendiente</option>
              <option value="confirmada">Confirmada</option>
              <option value="cancelada">Cancelada</option>
              <option value="completada">Completada</option>
            </select>
          </div>
        </div>

        <div class="card-footer text-right">
          <Link href="/agendas" class="btn btn-secondary">Cancelar</Link>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
  clientes: Array,
  empleados: Array,
  servicios: Array
})

const form = useForm({
  cliente_id: '',
  empleado_id: '',
  servicio_id: '',
  fecha_hora: '',
  estado: 'pendiente'
})

const guardar = () => {
  form.post('/agendas')
}
</script>
