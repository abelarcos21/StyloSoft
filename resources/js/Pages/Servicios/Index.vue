<script setup>
import { ref, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from 'sweetalert2'
import debounce from 'lodash.debounce'

const props = defineProps({
  servicios: Object,
  filters: Object,
  categorias: Array
})

const search = ref(props.filters.search || '')
const categoria = ref(props.filters.categoria || '')
const activo = ref(props.filters.activo || '')
const duracion = ref(props.filters.duracion || '')

// Filtrado con debounce para la búsqueda optimizada
const filtrar = debounce(() => {
  router.get('/servicios', {
    search: search.value,
    categoria: categoria.value,
    activo: activo.value,
    duracion: duracion.value
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
    cancelButton: 'btn btn-secondary rounded-pill px-4 ms-2',
    denyButton: 'btn btn-danger text-white rounded-pill px-4'
  },
  buttonsStyling: false
})

function eliminarServicio(id) {
  Swal.fire({
    title: "¿Eliminar servicio?",
    text: "Esta acción no se puede deshacer.",
    icon: "warning",
    showCancelButton: true,
    customClass: {
      confirmButton: 'btn btn-danger text-white rounded-pill px-4',
      cancelButton: 'btn btn-secondary rounded-pill px-4 ms-2'
    },
    buttonsStyling: false,
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then(result => {
    if (result.isConfirmed) {
      router.delete(`/servicios/${id}`, {
        onSuccess: () => {
          Swal.fire({
            title: "Eliminado",
            text: "Servicio eliminado exitosamente",
            icon: "success",
            confirmButtonColor: '#d84b72'
          })
        },
        onError: () => {
          Swal.fire({
            title: "Error",
            text: "No se pudo eliminar el servicio",
            icon: "error",
            confirmButtonColor: '#d84b72'
          })
        }
      })
    }
  })
}

function duplicarServicio(id) {
  swalEstilizado.fire({
    title: "¿Duplicar servicio?",
    text: "Se creará una copia idéntica del servicio.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, duplicar",
    cancelButtonText: "Cancelar",
  }).then(result => {
    if (result.isConfirmed) {
      router.post(`/servicios/${id}/duplicar`, {}, {
        onSuccess: () => {
          Swal.fire({
            title: "Duplicado",
            text: "Servicio duplicado exitosamente",
            icon: "success",
            confirmButtonColor: '#d84b72'
          })
        }
      })
    }
  })
}

function exportarServicios() {
  window.location.href = '/servicios-exportar?' + new URLSearchParams({
    search: search.value,
    categoria: categoria.value,
    activo: activo.value,
    duracion: duracion.value
  })
}

// Retorna clases sutiles corporativas según la duración
function getDuracionClass(minutos) {
  if (minutos <= 30) return 'badge rounded-pill bg-success-subtle text-success fw-medium px-3'
  if (minutos <= 60) return 'badge rounded-pill bg-warning-subtle text-warning fw-medium px-3'
  return 'badge rounded-pill bg-danger-subtle text-danger fw-medium px-3'
}
</script>

<template>
  <AdminLayout title="Servicios">
    <div class="container-fluid px-0 px-md-3">

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h1 class="h4 brand-accent fw-bold mb-0">
          <i class="fas fa-cut me-2"></i> Gestionar Servicios
        </h1>
        <div class="d-flex gap-2">
          <button @click="exportarServicios" class="btn btn-light rounded-pill px-4 shadow-sm text-success fw-medium">
            <i class="fas fa-file-excel me-2"></i> Exportar
          </button>
          <Link href="/servicios/create" class="btn btn-brand rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus me-2"></i> Nuevo Servicio
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
                placeholder="Buscar servicio..."
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
            <select v-model="duracion" @change="filtrar" class="form-select bg-light rounded-pill border-0">
              <option value="">Todas las duraciones</option>
              <option value="corta">Corta (≤30 min)</option>
              <option value="media">Media (31-60 min)</option>
              <option value="larga">Larga (>60 min)</option>
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
            <button @click="search = ''; categoria = ''; activo = ''; duracion = ''; filtrar()" class="btn btn-light rounded-pill w-100 text-muted">
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
                <th class="text-center border-0 font-weight-semibold">Orden</th>
                <th class="border-0 font-weight-semibold">Servicio</th>
                <th class="border-0 font-weight-semibold">Categoría</th>
                <th class="text-center border-0 font-weight-semibold">Duración</th>
                <th class="text-end border-0 font-weight-semibold">Precio</th>
                <th class="text-center border-0 font-weight-semibold">Depósito</th>
                <th class="text-center border-0 font-weight-semibold">Estado</th>
                <th class="text-center border-0 font-weight-semibold">Acciones</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="servicio in servicios.data" :key="servicio.id">

                <td class="text-center">
                  <span class="badge rounded-pill bg-light border text-secondary fw-medium px-3">
                    {{ servicio.orden }}
                  </span>
                </td>

                <td>
                  <div>
                    <strong class="text-dark">{{ servicio.nombre }}</strong>
                    <div v-if="servicio.descripcion" class="mt-1">
                      <small class="text-muted">
                        {{ servicio.descripcion.substring(0, 60) }}{{ servicio.descripcion.length > 60 ? '...' : '' }}
                      </small>
                    </div>
                  </div>
                </td>

                <td>
                  <span v-if="servicio.categoria" class="badge rounded-pill bg-info-subtle text-info fw-medium px-3">
                    {{ servicio.categoria }}
                  </span>
                  <span v-else class="text-muted small fst-italic">Sin categoría</span>
                </td>

                <td class="text-center">
                  <span :class="getDuracionClass(servicio.duracion_minutos)">
                    <i class="fas fa-clock fa-xs me-1"></i>{{ servicio.duracion_minutos }} min
                  </span>
                </td>

                <td class="text-end">
                  <strong class="text-success">${{ servicio.precio }}</strong>
                </td>

                <td class="text-center">
                  <div v-if="servicio.requiere_deposito">
                    <span class="badge rounded-pill bg-warning-subtle text-warning fw-medium px-3">
                      <i class="fas fa-lock fa-xs me-1"></i> Requiere
                    </span>
                    <div v-if="servicio.deposito_minimo" class="mt-1">
                      <small class="text-muted fw-semibold">${{ servicio.deposito_minimo }}</small>
                    </div>
                  </div>
                  <span v-else class="text-muted">-</span>
                </td>

                <td class="text-center">
                  <span
                    class="badge rounded-pill px-3 py-2 fw-medium"
                    :class="servicio.activo ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'"
                  >
                    {{ servicio.activo ? 'ACTIVO' : 'INACTIVO' }}
                  </span>
                </td>

                <td class="text-center">
                  <div class="d-flex justify-content-center gap-2">
                    <Link
                      :href="`/servicios/${servicio.id}`"
                      class="btn btn-sm btn-light text-info rounded-circle action-btn"
                      title="Ver detalles"
                    >
                      <i class="fas fa-eye"></i>
                    </Link>
                    <Link
                      :href="`/servicios/${servicio.id}/edit`"
                      class="btn btn-sm btn-light text-warning rounded-circle action-btn"
                      title="Editar"
                    >
                      <i class="fas fa-edit"></i>
                    </Link>
                    <button
                      @click="duplicarServicio(servicio.id)"
                      class="btn btn-sm btn-light text-primary rounded-circle action-btn"
                      title="Duplicar"
                    >
                      <i class="fas fa-copy"></i>
                    </button>
                    <button
                      @click="eliminarServicio(servicio.id)"
                      class="btn btn-sm btn-light text-danger rounded-circle action-btn"
                      title="Eliminar"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>

              </tr>

              <tr v-if="!servicios.data.length">
                <td colspan="8" class="text-center py-5">
                  <div class="text-muted d-flex flex-column align-items-center">
                    <i class="fas fa-scissors fa-3x mb-3 text-light"></i>
                    <h5 class="fw-medium text-secondary">Sin resultados</h5>
                    <p class="mb-0 small">No hay servicios registrados con los filtros seleccionados.</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <nav class="mt-4" v-if="servicios.data.length">
        <ul class="pagination justify-content-center flex-wrap">
          <li
            v-for="(link, i) in props.servicios.links"
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
