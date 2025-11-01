<template>
  <AdminLayout>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="mb-0">
        <i class="fas fa-boxes"></i> Productos
      </h2>
      <Link href="/productos/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Producto
      </Link>
    </div>

    <!-- Alerta de éxito -->
    <div v-if="$page.props.flash && $page.props.flash.success" class="alert alert-success">
      {{ $page.props.flash.success }}
    </div>

    <!-- Alerta de error -->
    <div v-if="$page.props.flash && $page.props.flash.error" class="alert alert-danger">
      {{ $page.props.flash.error }}
    </div>

    <div class="card shadow-sm">
      <div class="card-header bg-gradient-light d-flex justify-content-between align-items-center">
        <span><i class="fas fa-list"></i> Lista de Productos</span>
      </div>
      <div class="card-body p-0">
        <table class="table table-striped table-hover mb-0">
          <thead class="bg-gradient-info">
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Código</th>
              <th>Precio Venta</th>
              <th>Precio Compra</th>
              <th>Stock</th>
              <th>Estado</th>
              <th class="text-center" width="180">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="productos.length === 0">
              <td colspan="8" class="text-center text-muted py-3">No hay productos registrados</td>
            </tr>
            <tr v-for="producto in productos" :key="producto.id">
              <td>{{ producto.id }}</td>
              <td>{{ producto.nombre }}</td>
              <td>{{ producto.codigo }}</td>
              <td>${{ parseFloat(producto.precio_venta).toFixed(2) }}</td>
              <td>${{ producto.precio_compra ? parseFloat(producto.precio_compra).toFixed(2) : '-' }}</td>
              <td>{{ producto.stock }}</td>
              <td>
                <span :class="producto.activo ? 'badge bg-success' : 'badge bg-danger'">
                  {{ producto.activo ? 'Activo' : 'Inactivo' }}
                </span>
              </td>
              <td class="text-center">
                <Link
                  :href="`/productos/${producto.id}/edit`"
                  class="btn btn-sm btn-warning me-1"
                >
                  <i class="fas fa-edit"></i>
                </Link>
                <button
                  class="btn btn-sm btn-danger"
                  @click="eliminarProducto(producto.id)"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'

const { productos } = defineProps(['productos'])

// Confirmar y eliminar producto
function eliminarProducto(id) {
  if (confirm('¿Seguro que deseas eliminar este producto?')) {
    router.delete(`/productos/${id}`, {
      preserveScroll: true,
      onSuccess: () => {
        console.log('Producto eliminado correctamente')
      },
    })
  }
}
</script>

<style>

    .card-header {

        background-color: blueviolet;

    }

</style>
