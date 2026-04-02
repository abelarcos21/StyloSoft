<script setup>
import { ref, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from 'sweetalert2'
import debounce from 'lodash.debounce'

const props = defineProps({
  tickets: Object,
  filters: Object
})

const search = ref(props.filters.search || '')
const estado_pago = ref(props.filters.estado_pago || '')
const metodo_pago = ref(props.filters.metodo_pago || '')
const fecha_inicio = ref(props.filters.fecha_inicio || '')
const fecha_fin = ref(props.filters.fecha_fin || '')

// Filtrado con debounce para optimizar peticiones
const filtrar = debounce(() => {
  router.get('/tickets', {
    search: search.value,
    estado_pago: estado_pago.value,
    metodo_pago: metodo_pago.value,
    fecha_inicio: fecha_inicio.value,
    fecha_fin: fecha_fin.value
  }, {
    preserveState: true,
    replace: true,
  })
}, 300)

// Observador para disparar la búsqueda automáticamente al cambiar cualquier filtro
watch([search, estado_pago, metodo_pago, fecha_inicio, fecha_fin], () => filtrar())

// SweetAlert estilizado para coincidir con el diseño corporativo
const swalEstilizado = Swal.mixin({
  customClass: {
    confirmButton: 'btn btn-brand text-white rounded-pill px-4',
    cancelButton: 'btn btn-secondary rounded-pill px-4 ms-2'
  },
  buttonsStyling: false
})

function eliminarTicket(id) {
  swalEstilizado.fire({
    title: "¿Eliminar ticket?",
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
      router.delete(`/tickets/${id}`, {
        onSuccess: () => {
          Swal.fire({
            title: "Eliminado",
            text: "Ticket eliminado exitosamente",
            icon: "success",
            confirmButtonColor: '#d84b72'
          })
        }
      })
    }
  })
}

// Clases sutiles para los estados de pago
function getEstadoPagoClass(estado) {
  const classes = {
    'pagado': 'bg-success-subtle text-success',
    'pendiente': 'bg-warning-subtle text-warning',
    'parcial': 'bg-info-subtle text-info'
  }
  return classes[estado] || 'bg-secondary-subtle text-secondary'
}

function getMetodoPagoIcon(metodo) {
  const icons = {
    'efectivo': 'fa-money-bill-wave',
    'tarjeta': 'fa-credit-card',
    'transferencia': 'fa-exchange-alt',
    'mixto': 'fa-coins'
  }
  return icons[metodo] || 'fa-dollar-sign'
}

function limpiarFiltros() {
  search.value = ''
  estado_pago.value = ''
  metodo_pago.value = ''
  fecha_inicio.value = ''
  fecha_fin.value = ''
  filtrar()
}
</script>

<template>
  <AdminLayout title="Tickets">
    <div class="container-fluid px-0 px-md-3">

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h1 class="h4 brand-accent fw-bold mb-0">
          <i class="fas fa-receipt me-2"></i> Tickets de Venta
        </h1>
        <Link href="/tickets/create" class="btn btn-brand rounded-pill px-4 shadow-sm">
          <i class="fas fa-plus me-2"></i> Nuevo Ticket
        </Link>
      </div>

      <div class="card shadow-sm border-0 mb-4 rounded-4">
        <div class="card-body row g-3 align-items-center">

          <div class="col-12 col-md-3">
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0 rounded-start-pill">
                <i class="fas fa-search text-muted"></i>
              </span>
              <input
                class="form-control border-start-0 bg-light rounded-end-pill focus-ring-none"
                v-model="search"
                placeholder="N° ticket o cliente..."
              >
            </div>
          </div>

          <div class="col-6 col-md-2">
            <select v-model="estado_pago" class="form-select bg-light rounded-pill border-0">
              <option value="">Estado (Todos)</option>
              <option value="pagado">Pagado</option>
              <option value="pendiente">Pendiente</option>
              <option value="parcial">Parcial</option>
            </select>
          </div>

          <div class="col-6 col-md-2">
            <select v-model="metodo_pago" class="form-select bg-light rounded-pill border-0">
              <option value="">Método (Todos)</option>
              <option value="efectivo">Efectivo</option>
              <option value="tarjeta">Tarjeta</option>
              <option value="transferencia">Transferencia</option>
              <option value="mixto">Mixto</option>
            </select>
          </div>

          <div class="col-6 col-md-2">
            <input
              type="date"
              v-model="fecha_inicio"
              class="form-control bg-light rounded-pill border-0 text-muted"
              title="Fecha Inicio"
            >
          </div>

          <div class="col-6 col-md-2">
            <input
              type="date"
              v-model="fecha_fin"
              class="form-control bg-light rounded-pill border-0 text-muted"
              title="Fecha Fin"
            >
          </div>

          <div class="col-12 col-md-1 text-end">
            <button @click="limpiarFiltros" class="btn btn-light rounded-circle w-100 p-2 text-muted shadow-sm" title="Limpiar Filtros">
              <i class="fas fa-redo"></i>
            </button>
          </div>

        </div>
      </div>

      <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 custom-table">
            <thead class="bg-light text-muted">
              <tr>
                <th class="border-0 font-weight-semibold">N° Ticket</th>
                <th class="border-0 font-weight-semibold">Cliente</th>
                <th class="border-0 font-weight-semibold">Empleado</th>
                <th class="text-center border-0 font-weight-semibold">Fecha</th>
                <th class="text-center border-0 font-weight-semibold">Método Pago</th>
                <th class="text-end border-0 font-weight-semibold">Subtotal</th>
                <th class="text-end border-0 font-weight-semibold">Total</th>
                <th class="text-center border-0 font-weight-semibold">Estado</th>
                <th class="text-center border-0 font-weight-semibold">Acciones</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="ticket in tickets.data" :key="ticket.id">

                <td>
                  <strong class="brand-accent">{{ ticket.numero_ticket }}</strong>
                </td>

                <td>
                  <div v-if="ticket.cliente" class="text-dark fw-medium">
                    <i class="fas fa-user text-muted me-1 fa-sm"></i>
                    {{ ticket.cliente.nombre_completo }}
                  </div>
                  <span v-else class="text-muted small fst-italic">Cliente General</span>
                </td>

                <td>
                  <div v-if="ticket.empleado" class="text-secondary small">
                    <i class="fas fa-user-tie me-1"></i>
                    {{ ticket.empleado.nombre_completo }}
                  </div>
                  <span v-else class="text-muted small">-</span>
                </td>

                <td class="text-center">
                  <span class="text-muted small">{{ ticket.fecha }}</span>
                </td>

                <td class="text-center">
                  <span class="badge rounded-pill bg-light border text-secondary fw-medium px-3">
                    <i :class="['fas', getMetodoPagoIcon(ticket.metodo_pago), 'me-1']"></i>
                    <span class="text-capitalize">{{ ticket.metodo_pago }}</span>
                  </span>
                </td>

                <td class="text-end">
                  <span class="text-muted small">${{ ticket.subtotal }}</span>
                </td>

                <td class="text-end">
                  <strong class="text-success">${{ ticket.total }}</strong>
                </td>

                <td class="text-center">
                  <span
                    class="badge rounded-pill px-3 py-2 fw-medium text-uppercase"
                    :class="getEstadoPagoClass(ticket.estado_pago)"
                  >
                    {{ ticket.estado_pago }}
                  </span>
                </td>

                <td class="text-center">
                  <div class="d-flex justify-content-center gap-2">
                    <Link
                      :href="`/tickets/${ticket.id}`"
                      class="btn btn-sm btn-light text-info rounded-circle action-btn"
                      title="Ver ticket"
                    >
                      <i class="fas fa-eye"></i>
                    </Link>
                    <a
                      :href="`/tickets/${ticket.id}/imprimir`"
                      class="btn btn-sm btn-light text-primary rounded-circle action-btn"
                      title="Imprimir"
                      target="_blank"
                    >
                      <i class="fas fa-print"></i>
                    </a>
                    <Link
                      :href="`/tickets/${ticket.id}/edit`"
                      class="btn btn-sm btn-light text-warning rounded-circle action-btn"
                      title="Editar"
                    >
                      <i class="fas fa-edit"></i>
                    </Link>
                    <button
                      @click="eliminarTicket(ticket.id)"
                      class="btn btn-sm btn-light text-danger rounded-circle action-btn"
                      title="Eliminar"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>

              </tr>

              <tr v-if="!tickets.data.length">
                <td colspan="9" class="text-center py-5">
                  <div class="text-muted d-flex flex-column align-items-center">
                    <i class="fas fa-receipt fa-3x mb-3 text-light"></i>
                    <h5 class="fw-medium text-secondary">Sin resultados</h5>
                    <p class="mb-0 small">No hay tickets registrados con los filtros seleccionados.</p>
                  </div>
                </td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>

      <nav class="mt-4" v-if="tickets.data.length">
        <ul class="pagination justify-content-center flex-wrap">
          <li
            v-for="(link, i) in props.tickets.links"
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

/* Clases sutiles para Bootstrap */
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
