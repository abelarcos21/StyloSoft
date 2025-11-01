<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
  servicios: Array
})

function eliminarServicio(id) {
  if (confirm('¿Eliminar este servicio?')) {
    router.delete(`/servicios/${id}`)
  }
}
</script>

<template>
  <AdminLayout title="Servicios">
    <div class="d-flex justify-content-between mb-3">
      <h2>Servicios</h2>
      <Link href="/servicios/create" class="btn btn-primary">Nuevo Servicio</Link>
    </div>

    <div v-if="$page.props.flash?.success" class="alert alert-success">
      {{ $page.props.flash.success }}
    </div>

    <div class="card">
      <div class="card-body table-responsive p-0">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Descripcion</th>
              <th>Precio</th>
              <th>Duración (min)</th>
              <th>Activo</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="servicio in servicios" :key="servicio.id">
              <td>{{ servicio.id }}</td>
              <td>{{ servicio.nombre }}</td>
              <td>{{ servicio.descripcion }}</td>
              <td>${{ Number(servicio.precio).toFixed(2) }}</td>
              <td>{{ servicio.duracion_minutos }}</td>
              <td>
                <span :class="servicio.activo ? 'badge bg-success' : 'badge bg-danger'">
                  {{ servicio.activo ? 'Sí' : 'No' }}
                </span>
              </td>
              <td>
                <Link :href="`/servicios/${servicio.id}/edit`" class="btn btn-sm btn-warning me-1">
                  Editar
                </Link>
                <button @click.prevent="eliminarServicio(servicio.id)" class="btn btn-sm btn-danger">
                  Eliminar
                </button>
              </td>
            </tr>
            <tr v-if="servicios.length === 0">
              <td colspan="6" class="text-center py-3">No hay servicios registrados.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>
