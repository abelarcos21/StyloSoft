<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
  productos: Object,
  filters: Object,
  categorias: Array
})

const search = ref(props.filters.search || '')
const categoria = ref(props.filters.categoria || '')
const activo = ref(props.filters.activo || '')
const stock = ref(props.filters.stock || '')

function filtrar() {
  router.get('/productos', {
    search: search.value,
    categoria: categoria.value,
    activo: activo.value,
    stock: stock.value
  }, {
    preserveState: true,
    replace: true,
  })
}

function eliminarProducto(id) {
  Swal.fire({
    title: "¿Eliminar producto?",
    text: "Esta acción no se puede deshacer",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then(result => {
    if (result.isConfirmed) {
      router.delete(`/productos/${id}`, {
        onSuccess: () => {
          Swal.fire("Eliminado", "Producto eliminado exitosamente", "success")
        },
        onError: () => {
          Swal.fire("Error", "No se pudo eliminar el producto", "error")
        }
      })
    }
  })
}

function exportarProductos() {
  window.location.href = '/productos-exportar?' + new URLSearchParams({
    search: search.value,
    categoria: categoria.value,
    activo: activo.value,
    stock: stock.value
  })
}

function getStockClass(producto) {
  if (producto.stock === 0) return 'text-danger fw-bold'
  if (producto.requiere_reorden) return 'text-warning fw-bold'
  return 'text-success'
}
</script>

<template>
  <AdminLayout title="Productos">
    <div class="container-fluid px-3">

      <!-- Header -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
        <h1 class="h4 text-primary fw-bold">
          <i class="fas fa-boxes me-2"></i> Gestionar Productos
        </h1>
        <div class="btn-group">
          <button @click="exportarProductos" class="btn btn-success">
            <i class="fas fa-file-excel me-2"></i> Exportar
          </button>
          <a href="/productos/create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Nuevo Producto
          </a>
        </div>
      </div>

      <!-- Filtros -->
      <div class="card shadow-sm border-0 mb-3">
        <div class="card-body row g-2">

          <div class="col-md-3">
            <div class="input-group">
              <span class="input-group-text bg-light">
                <i class="fas fa-search"></i>
              </span>
              <input 
                class="form-control" 
                v-model="search" 
                @input="filtrar" 
                placeholder="Buscar producto..."
              >
            </div>
          </div>

          <div class="col-md-2">
            <select v-model="categoria" @change="filtrar" class="form-select">
              <option value="">Todas las categorías</option>
              <option v-for="cat in categorias" :key="cat" :value="cat">{{ cat }}</option>
            </select>
          </div>

          <div class="col-md-2">
            <select v-model="stock" @change="filtrar" class="form-select">
              <option value="">Todo el stock</option>
              <option value="bajo">Stock bajo</option>
              <option value="sin_stock">Sin stock</option>
            </select>
          </div>

          <div class="col-md-2">
            <select v-model="activo" @change="filtrar" class="form-select">
              <option value="">Todos</option>
              <option value="1">Activos</option>
              <option value="0">Inactivos</option>
            </select>
          </div>

          <div class="col-md-3">
            <button @click="search = ''; categoria = ''; activo = ''; stock = ''; filtrar()" class="btn btn-secondary w-100">
              <i class="fas fa-redo me-2"></i> Limpiar Filtros
            </button>
          </div>

        </div>
      </div>

      <!-- Tabla -->
      <div class="card shadow-sm border-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="text-center">#</th>
                <th>Código</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th class="text-end">Precio Venta</th>
                <th class="text-center">Stock</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Margen</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="producto in productos.data" :key="producto.id">
                <td class="text-center">{{ producto.id }}</td>

                <!-- CÓDIGO -->
                <td>
                  <span class="badge bg-secondary">
                    {{ producto.codigo }}
                  </span>
                </td>

                <!-- PRODUCTO -->
                <td>
                  <div>
                    <strong>{{ producto.nombre }}</strong>
                    <div v-if="producto.marca">
                      <small class="text-muted">
                        <i class="fas fa-tag fa-xs me-1"></i>{{ producto.marca }}
                      </small>
                    </div>
                  </div>
                </td>

                <!-- CATEGORÍA -->
                <td>
                  <span v-if="producto.categoria" class="badge bg-info">
                    {{ producto.categoria }}
                  </span>
                  <span v-else class="text-muted">-</span>
                </td>

                <!-- PRECIO -->
                <td class="text-end">
                  <strong class="text-success">${{ producto.precio_venta }}</strong>
                  <div v-if="producto.precio_compra">
                    <small class="text-muted">Compra: ${{ producto.precio_compra }}</small>
                  </div>
                </td>

                <!-- STOCK -->
                <td class="text-center">
                  <div>
                    <span :class="getStockClass(producto)">
                      {{ producto.stock }}
                    </span>
                    <span class="text-muted small"> {{ producto.unidad_medida }}</span>
                  </div>
                  <div v-if="producto.requiere_reorden">
                    <small class="badge bg-warning text-dark">
                      <i class="fas fa-exclamation-triangle fa-xs"></i> Reordenar
                    </small>
                  </div>
                  <div v-if="producto.stock === 0">
                    <small class="badge bg-danger">
                      Sin Stock
                    </small>
                  </div>
                </td>

                <!-- ESTADO -->
                <td class="text-center">
                  <span 
                    class="badge px-3 py-2"
                    :class="producto.activo ? 'bg-success' : 'bg-secondary'"
                  >
                    {{ producto.activo ? 'ACTIVO' : 'INACTIVO' }}
                  </span>
                </td>

                <!-- MARGEN -->
                <td class="text-center">
                  <span v-if="producto.margen_ganancia" class="badge bg-primary">
                    {{ producto.margen_ganancia }}%
                  </span>
                  <span v-else class="text-muted">-</span>
                </td>

                <!-- ACCIONES -->
                <td class="text-center">
                  <div class="btn-group">
                    <a 
                      :href="`/productos/${producto.id}`" 
                      class="btn btn-sm btn-outline-info"
                      title="Ver detalles"
                    >
                      <i class="fas fa-eye"></i>
                    </a>
                    <a 
                      :href="`/productos/${producto.id}/edit`" 
                      class="btn btn-sm btn-outline-warning"
                      title="Editar"
                    >
                      <i class="fas fa-edit"></i>
                    </a>
                    <button 
                      @click="eliminarProducto(producto.id)" 
                      class="btn btn-sm btn-outline-danger"
                      title="Eliminar"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>

              </tr>

              <tr v-if="!productos.data.length">
                <td colspan="9" class="text-center text-muted py-5">
                  <i class="fas fa-box-open fa-3x mb-3 d-block"></i>
                  <p class="mb-0">No hay productos registrados con los filtros seleccionados.</p>
                </td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>

      <!-- Paginación -->
      <nav class="mt-4" v-if="productos.data.length">
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

<style scoped>
.badge {
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.btn-group .btn {
  padding: 0.25rem 0.5rem;
}

.table tbody tr {
  transition: background-color 0.2s;
}

.table tbody tr:hover {
  background-color: rgba(0, 123, 255, 0.05);
}
</style>