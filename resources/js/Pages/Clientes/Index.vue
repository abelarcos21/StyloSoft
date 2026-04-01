<script setup>
import { ref, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from 'sweetalert2'
import debounce from 'lodash.debounce'

const props = defineProps({
  clientes: Object,
  filters: Object
})

const search = ref(props.filters.search || '')
const genero = ref(props.filters.genero || '')
const marketing = ref(props.filters.marketing || '')

// Filtrado con debounce para la búsqueda optimizada
const filtrar = debounce(() => {
  router.get('/clientes', {
    search: search.value,
    genero: genero.value,
    marketing: marketing.value
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
    confirmButton: 'btn btn-danger text-white rounded-pill px-4',
    cancelButton: 'btn btn-secondary rounded-pill px-4 ms-2'
  },
  buttonsStyling: false
})

function eliminarCliente(id) {
  swalEstilizado.fire({
    title: "¿Eliminar cliente?",
    text: "Esta acción no se puede deshacer.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then(result => {
    if (result.isConfirmed) {
      router.delete(`/clientes/${id}`, {
        onSuccess: () => {
          Swal.fire({
            title: "Eliminado",
            text: "Cliente eliminado exitosamente",
            icon: "success",
            confirmButtonColor: '#d84b72'
          })
        },
        onError: () => {
          Swal.fire({
            title: "Error",
            text: "No se pudo eliminar el cliente",
            icon: "error",
            confirmButtonColor: '#d84b72'
          })
        }
      })
    }
  })
}

function exportarClientes() {
  window.location.href = '/clientes-exportar?' + new URLSearchParams({
    search: search.value,
    genero: genero.value,
    marketing: marketing.value
  })
}

// Función auxiliar para obtener iniciales de forma segura
function getIniciales(nombre, apellido) {
  const n = nombre ? nombre.charAt(0).toUpperCase() : '';
  const a = apellido ? apellido.charAt(0).toUpperCase() : '';
  return `${n}${a}` || 'C';
}
</script>

<template>
  <AdminLayout title="Clientes">
    <div class="container-fluid px-0 px-md-3">

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h1 class="h4 brand-accent fw-bold mb-0">
          <i class="fas fa-users me-2"></i> Gestionar Clientes
        </h1>
        <div class="d-flex gap-2">
          <button @click="exportarClientes" class="btn btn-light rounded-pill px-4 shadow-sm text-success fw-medium">
            <i class="fas fa-file-excel me-2"></i> Exportar
          </button>
          <Link href="/clientes/create" class="btn btn-brand rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus me-2"></i> Nuevo Cliente
          </Link>
        </div>
      </div>

      <div class="card shadow-sm border-0 mb-4 rounded-4">
        <div class="card-body row g-3">
          <div class="col-12 col-md-4">
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0 rounded-start-pill">
                <i class="fas fa-search text-muted"></i>
              </span>
              <input
                class="form-control border-start-0 bg-light rounded-end-pill focus-ring-none"
                v-model="search"
                placeholder="Buscar por nombre, email o teléfono..."
              >
            </div>
          </div>

          <div class="col-6 col-md-3">
            <select v-model="genero" @change="filtrar" class="form-select bg-light rounded-pill border-0">
              <option value="">Todos los géneros</option>
              <option value="masculino">Masculino</option>
              <option value="femenino">Femenino</option>
              <option value="otro">Otro</option>
              <option value="prefiero_no_decir">Prefiero no decir</option>
            </select>
          </div>

          <div class="col-6 col-md-3">
            <select v-model="marketing" @change="filtrar" class="form-select bg-light rounded-pill border-0">
              <option value="">Marketing (Todos)</option>
              <option value="1">Acepta Marketing</option>
              <option value="0">No acepta Marketing</option>
            </select>
          </div>

          <div class="col-12 col-md-2">
            <button @click="search = ''; genero = ''; marketing = ''; filtrar()" class="btn btn-light rounded-pill w-100 text-muted">
              <i class="fas fa-redo me-1"></i> Limpiar
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
                <th class="border-0 font-weight-semibold">Cliente</th>
                <th class="border-0 font-weight-semibold">Contacto</th>
                <th class="text-center border-0 font-weight-semibold">Género</th>
                <th class="text-center border-0 font-weight-semibold">Puntos</th>
                <th class="text-center border-0 font-weight-semibold">Marketing</th>
                <th class="text-center border-0 font-weight-semibold">Última Visita</th>
                <th class="text-center border-0 font-weight-semibold">Acciones</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="cliente in clientes.data" :key="cliente.id">
                <td class="text-center text-muted">{{ cliente.id }}</td>

                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar-circle me-3">
                      {{ getIniciales(cliente.nombre, cliente.apellido) }}
                    </div>
                    <div>
                      <div class="fw-bold text-dark">{{ cliente.nombre_completo }}</div>
                      <div class="small text-muted" v-if="cliente.fecha_nacimiento">
                        <i class="fas fa-birthday-cake fa-xs me-1"></i>{{ cliente.fecha_nacimiento }}
                      </div>
                    </div>
                  </div>
                </td>

                <td>
                  <div class="text-dark" v-if="cliente.telefono">
                    <i class="fas fa-phone fa-xs text-muted me-2"></i>{{ cliente.telefono }}
                  </div>
                  <div class="small text-muted mt-1" v-if="cliente.email">
                    <i class="fas fa-envelope fa-xs me-2"></i>{{ cliente.email }}
                  </div>
                  <span v-if="!cliente.telefono && !cliente.email" class="small text-muted fst-italic">
                    Sin contacto
                  </span>
                </td>

                <td class="text-center">
                  <span class="badge rounded-pill bg-light border text-secondary fw-medium px-3" v-if="cliente.genero">
                    {{ cliente.genero.charAt(0).toUpperCase() + cliente.genero.slice(1).replace(/_/g, ' ') }}
                  </span>
                  <span v-else class="text-muted">-</span>
                </td>

                <td class="text-center">
                  <span class="badge rounded-pill bg-warning-subtle text-warning fw-bold px-3">
                    <i class="fas fa-star me-1"></i>{{ cliente.puntos_fidelidad }}
                  </span>
                </td>

                <td class="text-center">
                  <span
                    class="badge rounded-pill fw-medium px-3"
                    :class="cliente.acepta_marketing ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'"
                  >
                    {{ cliente.acepta_marketing ? 'Sí' : 'No' }}
                  </span>
                </td>

                <td class="text-center">
                  <span class="fw-medium text-dark small" v-if="cliente.ultima_visita">
                    {{ cliente.ultima_visita }}
                  </span>
                  <span v-else class="text-muted">-</span>
                </td>

                <td class="text-center">
                  <div class="d-flex justify-content-center gap-2">
                    <Link
                      :href="`/clientes/${cliente.id}`"
                      class="btn btn-sm btn-light text-info rounded-circle action-btn"
                      title="Ver detalles"
                    >
                      <i class="fas fa-eye"></i>
                    </Link>
                    <Link
                      :href="`/clientes/${cliente.id}/edit`"
                      class="btn btn-sm btn-light text-warning rounded-circle action-btn"
                      title="Editar"
                    >
                      <i class="fas fa-edit"></i>
                    </Link>
                    <button
                      @click="eliminarCliente(cliente.id)"
                      class="btn btn-sm btn-light text-danger rounded-circle action-btn"
                      title="Eliminar"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="!clientes.data.length">
                <td colspan="8" class="text-center py-5">
                  <div class="text-muted d-flex flex-column align-items-center">
                    <i class="fas fa-users-slash fa-3x mb-3 text-light"></i>
                    <h5 class="fw-medium text-secondary">Sin resultados</h5>
                    <p class="mb-0 small">No hay clientes registrados con los filtros seleccionados.</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <nav class="mt-4" v-if="clientes.data.length">
        <ul class="pagination justify-content-center flex-wrap">
          <li
            v-for="(link, i) in props.clientes.links"
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

/* Avatar del cliente */
.avatar-circle {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.95rem;
  background-color: rgba(216, 75, 114, 0.1); /* Fondo translúcido del color brand */
  color: #d84b72; /* Texto color brand */
  border: 1px solid rgba(216, 75, 114, 0.2);
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
