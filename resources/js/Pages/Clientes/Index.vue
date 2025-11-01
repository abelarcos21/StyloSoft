<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  clientes: Array
})

function eliminarCliente(id) {
  if (confirm('¿Eliminar este cliente?')) {
    router.delete(`/clientes/${id}`)
  }
}
</script>

<template>
  <AdminLayout title="Clientes">
    <div class="d-flex justify-content-between mb-3">
      <h2>Clientes</h2>
      <Link href="/clientes/create" class="btn btn-primary">Nuevo Cliente</Link>
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
              <th>Apellido</th>
              <th>Email</th>
              <th>Teléfono</th>
              <th>Dirección</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="cliente in clientes" :key="cliente.id">
              <td>{{ cliente.id }}</td>
              <td>{{ cliente.nombre }}</td>
              <td>{{ cliente.apellido }}</td>
              <td>{{ cliente.email || '-' }}</td>
              <td>{{ cliente.telefono || '-' }}</td>
              <td>{{ cliente.direccion || '-' }}</td>
              <td>
                <Link :href="`/clientes/${cliente.id}/edit`" class="btn btn-sm btn-warning me-1">
                  Editar
                </Link>
                <button @click.prevent="eliminarCliente(cliente.id)" class="btn btn-sm btn-danger">
                  Eliminar
                </button>
              </td>
            </tr>
            <tr v-if="clientes.length === 0">
              <td colspan="7" class="text-center py-3">No hay clientes registrados.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>
