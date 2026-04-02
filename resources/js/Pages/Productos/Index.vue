<script setup>
import { ref, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from 'sweetalert2'
import debounce from 'lodash.debounce'

const props = defineProps({
  productos: Object,
  filters: Object,
  categorias: Array
})

const search = ref(props.filters.search || '')
const categoria = ref(props.filters.categoria || '')
const activo = ref(props.filters.activo || '')
const stock = ref(props.filters.stock || '')

// Filtrado con debounce para la búsqueda optimizada
const filtrar = debounce(() => {
  router.get('/productos', {
    search: search.value,
    categoria: categoria.value,
    activo: activo.value,
    stock: stock.value
  }, {
    preserveState: true,
    replace: true,
  })
}, 300)

// Observar cambios en búsqueda para disparar el filtro automáticamente
watch(search, () => filtrar())

// SweetAlert estilizado para coincidir con el diseño
const swalEstilizado = Swal.mixin({
  customClass: {
    confirmButton: 'btn btn-brand text-white rounded-pill px-4',
    cancelButton: 'btn btn-secondary rounded-pill px-4 ms-2'
  },
  buttonsStyling: false
})

function eliminarProducto(id) {
  swalEstilizado.fire({
    title: "¿Eliminar producto?",
    text: "Esta acción no se puede deshacer.",
    icon: "warning",
    showCancelButton: true,
    customClass: {
      confirmButton: 'btn btn-danger text-white rounded-pill px-4',
      cancelButton: 'btn btn-secondary rounded-pill px-4 ms-2'
    },
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then(result => {
    if (result.isConfirmed) {
      router.delete(`/productos/${id}`, {
        onSuccess: () => {
          Swal.fire({
            title: "Eliminado",
            text: "Producto eliminado exitosamente",
            icon: "success",
            confirmButtonColor: '#d84b72'
          })
        },
        onError: () => {
          Swal.fire({
            title: "Error",
            text: "No se pudo eliminar el producto",
            icon: "error",
            confirmButtonColor: '#d84b72'
          })
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

// Retorna clases corporativas sutiles según el estado del stock
function getStockClass(producto) {
  if (producto.stock === 0) return 'badge rounded-pill bg-danger-subtle text-danger fw-medium px-3'
  if (producto.requiere_reorden) return 'badge rounded-pill bg-warning-subtle text-warning fw-medium px-3'
  return 'badge rounded-pill bg-success-subtle text-success fw-medium px-3'
}
</script>

<template>
  <AdminLayout title="Productos">
    <div class="container-fluid px-0 px-md-3">

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h1 class="h4 brand-accent fw-bold mb-0">
          <i class="fas fa-boxes me-2"></i> Gestionar Productos
        </h1>
        <div class="d-flex gap-2">
          <button @click="exportarProductos" class="btn btn-light rounded-pill px-4 shadow-sm text-success fw-medium">
            <i class="fas fa-file-excel me-2"></i> Exportar
          </button>
          <Link href="/productos/create" class="btn btn-brand rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus me-2"></i> Nuevo Producto
          </Link>
        </div>
      </div>

      <div class="card shadow-sm border-0 mb-4 rounded-4">
        <div class="card-body row g-3">
          <div class="col-12 col-md-3">
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0 rounded-start-pill">
                <i class="fas fa-search text-muted"></i>
              </span>
              <input
                class="form-control border-start-0 bg-light rounded-end-pill focus-ring-none"
                v-model="search"
                placeholder="Buscar producto..."
              >
            </div>
          </div>

          <div class="col-6 col-md-2">
            <select v-model="categoria" @change="filtrar" class="form-select bg-light rounded-pill border-0">
              <option value="">Todas las categorías</option>
              <option v-for="cat in categorias" :key="cat" :value="cat">{{ cat }}</option>
            </select>
          </div>

          <div class="col-6 col-md-2">
            <select v-model="stock" @change="filtrar" class="form-select bg-light rounded-pill border-0">
              <option value="">Todo el stock</option>
              <option value="bajo">Stock bajo / Reorden</option>
              <option value="sin_stock">Sin stock</option>
            </select>
          </div>

          <div class="col-6 col-md-2">
            <select v-model="activo" @change="filtrar" class="form-select bg-light rounded-pill border-0">
              <option value="">Todos los estados</option>
              <option value="1">Activos</option>
              <option value="0">Inactivos</option>
            </select>
          </div>

          <div class="col-6 col-md-3">
            <button @click="search = ''; categoria = ''; activo = ''; stock = ''; filtrar()" class="btn btn-light rounded-pill w-100 text-muted">
              <i class="fas fa-redo me-1"></i> Limpiar Filtros
            </button>
          </div>
        </div>
      </div>

      <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 custom-table">
            <thead class="bg-light text-muted">
              <tr>
                <th class="text-center border-0 font-weight-semibold">#</th>
                <th class="border-0 font-weight-semibold">Código</th>
                <th class="border-0 font-weight-semibold">Producto</th>
                <th class="border-0 font-weight-semibold">Categoría</th>
                <th class="text-end border-0 font-weight-semibold">Precio Venta</th>
                <th class="text-center border-0 font-weight-semibold">Stock</th>
                <th class="text-center border-0 font-weight-semibold">Estado</th>
                <th class="text-center border-0 font-weight-semibold">Margen</th>
                <th class="text-center border-0 font-weight-semibold">Acciones</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="producto in productos.data" :key="producto.id">
                <td class="text-center text-muted small">{{ producto.id }}</td>

                <td>
                  <span class="badge rounded-pill bg-light border text-secondary fw-medium px-3">
                    {{ producto.codigo }}
                  </span>
                </td>

                <td>
                  <div>
                    <strong class="text-dark">{{ producto.nombre }}</strong>
                    <div v-if="producto.marca" class="mt-1">
                      <small class="text-muted">
                        <i class="fas fa-tag fa-xs me-1"></i>{{ producto.marca }}
                      </small>
                    </div>
                  </div>
                </td>

                <td>
                  <span v-if="producto.categoria" class="badge rounded-pill bg-info-subtle text-info fw-medium px-3">
                    {{ producto.categoria }}
                  </span>
                  <span v-else class="text-muted small fst-italic">Sin categoría</span>
                </td>

                <td class="text-end">
                  <strong class="text-success">${{ producto.precio_venta }}</strong>
                  <div v-if="producto.precio_compra" class="mt-1">
                    <small class="text-muted">Compra: ${{ producto.precio_compra }}</small>
                  </div>
                </td>

                <td class="text-center">
                  <div class="mb-1">
                    <span :class="getStockClass(producto)">
                      {{ producto.stock }}
                    </span>
                    <span class="text-muted small ms-1">{{ producto.unidad_medida }}</span>
                  </div>
                  <div v-if="producto.requiere_reorden && producto.stock > 0">
                    <small class="badge rounded-pill bg-warning-subtle text-warning mt-1">
                      <i class="fas fa-exclamation-triangle fa-xs"></i> Reordenar
                    </small>
                  </div>
                  <div v-if="producto.stock === 0">
                    <small class="badge rounded-pill bg-danger-subtle text-danger mt-1">
                      <i class="fas fa-times-circle fa-xs"></i> Agotado
                    </small>
                  </div>
                </td>

                <td class="text-center">
                  <span
                    class="badge rounded-pill px-3 py-2 fw-medium"
                    :class="producto.activo ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'"
                  >
                    {{ producto.activo ? 'ACTIVO' : 'INACTIVO' }}
                  </span>
                </td>

                <td class="text-center">
                  <span v-if="producto.margen_ganancia" class="badge rounded-pill bg-primary-subtle text-primary fw-medium px-3">
                    {{ producto.margen_ganancia }}%
                  </span>
                  <span v-else class="text-muted">-</span>
                </td>

                <td class="text-center">
                  <div class="d-flex justify-content-center gap-2">
                    <Link
                      :href="`/productos/${producto.id}`"
                      class="btn btn-sm btn-light text-info rounded-circle action-btn"
                      title="Ver detalles"
                    >
                      <i class="fas fa-eye"></i>
                    </Link>
                    <Link
                      :href="`/productos/${producto.id}/edit`"
                      class="btn btn-sm btn-light text-warning rounded-circle action-btn"
                      title="Editar"
                    >
                      <i class="fas fa-edit"></i>
                    </Link>
                    <button
                      @click="eliminarProducto(producto.id)"
                      class="btn btn-sm btn-light text-danger rounded-circle action-btn"
                      title="Eliminar"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>

              </tr>

              <tr v-if="!productos.data.length">
                <td colspan="9" class="text-center py-5">
                  <div class="text-muted d-flex flex-column align-items-center">
                    <i class="fas fa-box-open fa-3x mb-3 text-light"></i>
                    <h5 class="fw-medium text-secondary">Sin resultados</h5>
                    <p class="mb-0 small">No hay productos registrados con los filtros seleccionados.</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <nav class="mt-4" v-if="productos.data.length">
        <ul class="pagination justify-content-center flex-wrap">
          <li
            v-for="(link, i) in props.productos.links"
            :key="i"
            class="page-item"
            :class="{ active: link.active, disabled: !link.url }"
          >
            <button
              class="page-link shadow-sm border-0 mx-1 rounded-2"
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
/* Variables y colores principales */
.brand-accent {
  color: #d84b72;
}

.btn-brand {
  background-color: #d84b72;
  color: white;
  transition: all 0.3s ease;
}

.btn-brand:hover {
  background-color: #c03d61;
  color: white;
  transform: translateY(-2px);
}

/* Modificadores de inputs */
.focus-ring-none:focus {
  box-shadow: none;
  border-color: #dee2e6;
}

/* Tabla personalizada */
.custom-table th {
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
}

.custom-table tbody tr {
  transition: background-color 0.2s ease;
}

.custom-table tbody tr:hover {
  background-color: #f8f9fa;
}

/* Botones de acción */
.action-btn {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
  background-color: #f8f9fa;
}

.action-btn:hover {
  transform: scale(1.1);
  background-color: #e9ecef;
}

/* Clases sutiles para Bootstrap (Por si usas una versión anterior a 5.3) */
.bg-warning-subtle { background-color: #fff3cd !important; }
.bg-success-subtle { background-color: #d1e7dd !important; }
.bg-danger-subtle { background-color: #f8d7da !important; }
.bg-info-subtle { background-color: #cff4fc !important; }
.bg-secondary-subtle { background-color: #e2e3e5 !important; }
.bg-primary-subtle { background-color: #cfe2ff !important; }

/* Paginación corporativa */
.page-link {
  color: #6c757d;
}

.page-link:hover {
  color: #d84b72;
  background-color: #f8f9fa;
}

.page-item.active .page-link {
  background-color: #d84b72;
  color: white;
}
</style>
