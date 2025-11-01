<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'

const props = defineProps({
  productos: Object, // con paginación
  filters: Object
})

// Filtros reactivos
const search = ref(props.filters.search || '')
const estado = ref(props.filters.estado || '')

function filtrar() {
  router.get('/productos', { search: search.value, estado: estado.value }, {
    preserveState: true,
    replace: true,
  })
}

function eliminarProducto(id) {
  if (confirm('¿Deseas eliminar este producto?')) {
    router.delete(`/productos/${id}`)
  }
}
</script>

<template>
  <AdminLayout title="Productos">
    <div class="container-fluid px-3">

      <!-- Encabezado -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
        <h1 class="h4 mb-0 text-primary fw-bold">
          <i class="fas fa-boxes me-2"></i> Productos
        </h1>
        <a href="/productos/create" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
          <i class="fas fa-plus"></i> Nuevo producto
        </a>
      </div>

      <!-- Filtros -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <div class="row g-2">
            <div class="col-12 col-md-6 col-lg-4">
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                <input
                  type="search"
                  v-model="search"
                  @input="filtrar"
                  class="form-control"
                  placeholder="Buscar por nombre o código..."
                />
              </div>
            </div>
            <div class="col-12 col-md-4 col-lg-3">
              <select v-model="estado" @change="filtrar" class="form-select">
                <option value="">Todos los estados</option>
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabla -->
      <div class="card border-0 shadow-sm">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-center">
              <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Código</th>
                <th>Precio Venta</th>
                <th>Precio Compra</th>
                <th>Stock</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="producto in props.productos.data"
                :key="producto.id"
                class="text-center"
              >
                <td>{{ producto.id }}</td>
                <td class="text-start">
                  <strong>{{ producto.nombre }}</strong><br />
                  <small class="text-muted">{{ producto.descripcion || 'Sin descripción' }}</small>
                </td>
                <td>{{ producto.codigo || '-' }}</td>
                <td>${{ Number(producto.precio_venta ?? 0).toFixed(2) }}</td>
                <td>${{ Number(producto.precio_compra ?? 0).toFixed(2) }}</td>
                <td>{{ producto.stock }}</td>
                <td>
                  <span
                    class="badge px-3 py-2 rounded-pill"
                    :class="producto.activo ? 'bg-success' : 'bg-danger'"
                  >
                    {{ producto.activo ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <a
                      :href="`/productos/${producto.id}/edit`"
                      class="btn btn-sm btn-outline-warning"
                      title="Editar"
                    >
                      <i class="fas fa-edit"></i>
                    </a>
                    <button
                      @click.prevent="eliminarProducto(producto.id)"
                      class="btn btn-sm btn-outline-danger"
                      title="Eliminar"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="!props.productos.data.length">
                <td colspan="8" class="text-center text-muted py-4">
                  <i class="fas fa-box-open fa-2x mb-2"></i><br />
                  No hay productos registrados.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Paginación -->
      <nav class="mt-4">
        <ul class="pagination justify-content-center flex-wrap">
          <li
            v-for="(link, i) in props.productos.links"
            :key="i"
            class="page-item"
            :class="{ active: link.active, disabled: !link.url }"
          >
            <button
              class="page-link"
              v-html="link.label"
              @click="link.url && router.get(link.url, {}, { preserveState: true })"
            />
          </li>
        </ul>
      </nav>

    </div>
  </AdminLayout>
</template>
