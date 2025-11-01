<template>
  <AdminLayout title="Agendas">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Listado de Agendas</h3>
        <Link href="/agendas/create" class="btn btn-primary btn-sm">
          <i class="fas fa-plus"></i> Nueva Agenda
        </Link>
      </div>

      <div class="card-body table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>Cliente</th>
              <th>Empleado</th>
              <th>Servicio</th>
              <th>Fecha y Hora</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(agenda, index) in agendas" :key="agenda.id">
              <td>{{ index + 1 }}</td>
              <td>{{ agenda.cliente.nombre }}</td>
              <td>{{ agenda.empleado.nombre }}</td>
              <td>{{ agenda.servicio.nombre }}</td>
              <td>{{ new Date(agenda.fecha_hora).toLocaleString() }}</td>
              <td>
                <span class="badge"
                      :class="{
                        'badge-warning': agenda.estado === 'pendiente',
                        'badge-info': agenda.estado === 'confirmada',
                        'badge-success': agenda.estado === 'completada',
                        'badge-danger': agenda.estado === 'cancelada'
                      }">
                  {{ agenda.estado }}
                </span>
              </td>
              <td>
                <Link :href="`/agendas/${agenda.id}/edit`" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i> Editar</Link>
                <Link :href="`/agendas/${agenda.id}`" class="btn btn-sm btn-info me-1"><i class="fas fa-eye"></i> Ver Detalle</Link>
                <button @click="eliminar(agenda.id)" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Eliminar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({ agendas: Array })

const eliminar = (id) => {
  if (confirm('¿Deseas eliminar esta agenda?')) {
    router.delete(`/agendas/${id}`)
  }
}
</script>
