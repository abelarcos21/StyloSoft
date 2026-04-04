<script setup>
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  ticket: Object
})

// Helper de Moneda para mantener consistencia
const formatoMoneda = (valor) => {
  if (valor === undefined || valor === null) return '$0.00'
  return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(valor)
}

function eliminarTicket() {
  Swal.fire({
    title: "¿Eliminar ticket?",
    text: "Esta acción no se puede deshacer",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#dc3545", // Color danger de Bootstrap
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
    customClass: {
      confirmButton: 'btn btn-danger rounded-pill px-4 me-2',
      cancelButton: 'btn btn-secondary rounded-pill px-4'
    },
    buttonsStyling: false
  }).then(result => {
    if (result.isConfirmed) {
      router.delete(`/tickets/${props.ticket.id}`, {
        onSuccess: () => {
          Swal.fire({
            title: "Eliminado",
            text: "Ticket eliminado exitosamente",
            icon: "success",
            confirmButtonClass: "btn btn-brand rounded-pill px-4",
            buttonsStyling: false
          })
        }
      })
    }
  })
}

function getEstadoPagoClass(estado) {
  const classes = {
    'pagado': 'bg-success bg-opacity-10 text-success border border-success',
    'pendiente': 'bg-warning bg-opacity-10 text-warning border border-warning',
    'parcial': 'bg-info bg-opacity-10 text-info border border-info'
  }
  return classes[estado] || 'bg-secondary bg-opacity-10 text-secondary border'
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

function imprimirTicket() {
  window.open(`/tickets/${props.ticket.id}/imprimir`, '_blank')
}
</script>

<template>
  <AdminLayout title="Ver Ticket">
    <div class="container-fluid px-0 px-md-3 mb-5">

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
          <h1 class="h4 brand-accent fw-bold mb-0">
            <i class="fas fa-receipt me-2"></i> Detalle del Ticket
          </h1>
          <p class="text-muted mb-0 mt-1 fw-medium">Folio: <span class="text-dark">#{{ ticket.numero_ticket }}</span></p>
        </div>
        <div class="d-flex flex-wrap gap-2">
          <Link href="/tickets" class="btn btn-light border rounded-pill px-3 shadow-sm text-muted fw-medium order-4 order-md-1">
            <i class="fas fa-arrow-left me-1"></i> Volver
          </Link>
          <button @click="imprimirTicket" class="btn btn-light border rounded-pill px-3 fw-medium order-1 order-md-2 text-dark">
            <i class="fas fa-print me-1"></i> Imprimir
          </button>
          <button @click="eliminarTicket" class="btn btn-outline-danger rounded-pill px-3 fw-medium order-2 order-md-3">
            <i class="fas fa-trash me-1"></i> Eliminar
          </button>
          <Link :href="`/tickets/${ticket.id}/edit`" class="btn btn-brand rounded-pill px-4 fw-bold shadow-sm order-3 order-md-4">
            <i class="fas fa-edit me-1"></i> Editar
          </Link>
        </div>
      </div>

      <div class="row g-4">

        <div class="col-lg-8">

          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom pt-4 pb-3 px-4">
              <h6 class="mb-0 fw-bold text-dark text-uppercase tracking-wide">
                <i class="fas fa-info-circle text-primary me-2"></i> Información General
              </h6>
            </div>
            <div class="card-body p-4">
              <div class="row g-4">

                <div class="col-md-6">
                  <div class="d-flex align-items-start">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary me-3">
                      <i class="fas fa-user fa-lg"></i>
                    </div>
                    <div>
                      <label class="text-muted small fw-bold text-uppercase tracking-wide mb-1">Cliente</label>
                      <div v-if="ticket.cliente">
                        <strong class="d-block text-dark">{{ ticket.cliente.nombre_completo }}</strong>
                        <small class="text-muted d-block" v-if="ticket.cliente.telefono">
                          <i class="fas fa-phone me-1"></i> {{ ticket.cliente.telefono }}
                        </small>
                        <small class="text-muted d-block" v-if="ticket.cliente.email">
                          <i class="fas fa-envelope me-1"></i> {{ ticket.cliente.email }}
                        </small>
                      </div>
                      <span v-else class="text-muted fw-medium">Cliente General</span>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="d-flex align-items-start">
                    <div class="icon-box bg-success bg-opacity-10 text-success me-3">
                      <i class="fas fa-user-tie fa-lg"></i>
                    </div>
                    <div>
                      <label class="text-muted small fw-bold text-uppercase tracking-wide mb-1">Atendido por</label>
                      <div v-if="ticket.empleado">
                        <strong class="d-block text-dark">{{ ticket.empleado.nombre_completo }}</strong>
                        <small class="text-muted" v-if="ticket.empleado.puesto">
                          {{ ticket.empleado.puesto }}
                        </small>
                      </div>
                      <span v-else class="text-muted fw-medium">No asignado</span>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="d-flex align-items-start">
                    <div class="icon-box bg-info bg-opacity-10 text-info me-3">
                      <i class="fas fa-calendar-alt fa-lg"></i>
                    </div>
                    <div>
                      <label class="text-muted small fw-bold text-uppercase tracking-wide mb-1">Fecha y Hora</label>
                      <strong class="d-block text-dark">{{ ticket.fecha }}</strong>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="d-flex align-items-start">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning me-3">
                      <i :class="['fas', getMetodoPagoIcon(ticket.metodo_pago), 'fa-lg']"></i>
                    </div>
                    <div>
                      <label class="text-muted small fw-bold text-uppercase tracking-wide mb-1">Método de Pago</label>
                      <strong class="d-block text-dark text-capitalize">{{ ticket.metodo_pago }}</strong>
                    </div>
                  </div>
                </div>

              </div>

              <div class="mt-4 pt-4 border-top">
                <div class="d-flex align-items-center">
                  <label class="text-muted small fw-bold text-uppercase tracking-wide mb-0 me-3">Estado del Ticket:</label>
                  <span 
                    class="badge rounded-pill px-3 py-2 fw-bold tracking-wide text-uppercase"
                    :class="getEstadoPagoClass(ticket.estado_pago)"
                  >
                    <i class="fas fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i>
                    {{ ticket.estado_pago }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-bottom pt-4 pb-3 px-4">
              <h6 class="mb-0 fw-bold text-dark text-uppercase tracking-wide">
                <i class="fas fa-shopping-bag text-brand me-2"></i> Desglose de Venta
              </h6>
            </div>
            <div class="table-responsive">
              <table class="table table-hover mb-0 border-transparent">
                <thead class="table-light">
                  <tr>
                    <th class="text-muted small text-uppercase tracking-wide border-0 py-3 ps-4" style="width: 40%">Artículo / Servicio</th>
                    <th class="text-muted small text-uppercase tracking-wide border-0 py-3 text-center" style="width: 15%">Cant.</th>
                    <th class="text-muted small text-uppercase tracking-wide border-0 py-3 text-end" style="width: 15%">Precio</th>
                    <th class="text-muted small text-uppercase tracking-wide border-0 py-3 text-end" style="width: 15%">Desc.</th>
                    <th class="text-muted small text-uppercase tracking-wide border-0 py-3 text-end pe-4" style="width: 15%">Importe</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="detalle in ticket.detalles" :key="detalle.id">
                    <td class="ps-4 py-3">
                      <div class="d-flex align-items-center">
                        <div 
                          class="icon-badge me-3"
                          :class="detalle.tipo === 'Servicio' ? 'bg-info bg-opacity-10 text-info' : 'bg-warning bg-opacity-10 text-warning'"
                        >
                          <i :class="detalle.tipo === 'Servicio' ? 'fas fa-cut' : 'fas fa-box'"></i>
                        </div>
                        <div>
                          <strong class="d-block text-dark">{{ detalle.nombre }}</strong>
                          <small class="text-muted">{{ detalle.tipo }}</small>
                        </div>
                      </div>
                    </td>
                    <td class="text-center align-middle py-3">
                      <span class="badge bg-light text-dark border px-2 py-1">{{ detalle.cantidad }}</span>
                    </td>
                    <td class="text-end align-middle text-muted py-3">
                      {{ formatoMoneda(detalle.precio_unitario) }}
                    </td>
                    <td class="text-end align-middle py-3">
                      <span class="text-danger fw-medium" v-if="parseFloat(detalle.descuento) > 0">
                        -{{ formatoMoneda(detalle.descuento) }}
                      </span>
                      <span v-else class="text-muted opacity-50">-</span>
                    </td>
                    <td class="text-end align-middle pe-4 py-3">
                      <strong class="text-dark">{{ formatoMoneda(detalle.subtotal) }}</strong>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>

        <div class="col-lg-4">

          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom pt-4 pb-3 px-4">
              <h6 class="mb-0 fw-bold text-dark text-uppercase tracking-wide">
                <i class="fas fa-calculator text-success me-2"></i> Totales
              </h6>
            </div>
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted fw-medium">Subtotal</span>
                <strong class="text-dark">{{ formatoMoneda(ticket.subtotal) }}</strong>
              </div>

              <div class="d-flex justify-content-between align-items-center mb-3" v-if="parseFloat(ticket.descuento) > 0">
                <span class="text-danger fw-medium">Descuento Global</span>
                <strong class="text-danger">-{{ formatoMoneda(ticket.descuento) }}</strong>
              </div>

              <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted fw-medium">IVA (16%)</span>
                <strong class="text-dark">{{ formatoMoneda(ticket.impuesto) }}</strong>
              </div>

              <hr class="my-4 border-light">

              <div class="d-flex justify-content-between align-items-center">
                <span class="h5 mb-0 text-dark fw-bold text-uppercase">Total</span>
                <span class="h3 mb-0 brand-accent fw-bold">{{ formatoMoneda(ticket.total) }}</span>
              </div>
            </div>
          </div>

          <div v-if="ticket.notas" class="card shadow-sm border-0 rounded-4 mb-4 bg-light">
            <div class="card-body p-4">
              <h6 class="fw-bold text-dark text-uppercase tracking-wide mb-3">
                <i class="fas fa-sticky-note text-warning me-2"></i> Notas / Observaciones
              </h6>
              <p class="mb-0 text-muted fst-italic" style="white-space: pre-wrap;">"{{ ticket.notas }}"</p>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
              <div class="d-flex align-items-center mb-2">
                <i class="fas fa-clock text-muted me-2"></i>
                <small class="text-muted fw-bold text-uppercase tracking-wide">Registro en Sistema</small>
              </div>
              <small class="text-muted ms-4 d-block">{{ ticket.created_at }}</small>
            </div>
          </div>

        </div>

      </div>

    </div>
  </AdminLayout>
</template>

<style scoped>
/* Variables Brand */
.brand-accent {
  color: #d84b72;
}

.text-brand {
  color: #d84b72 !important;
}

.btn-brand {
  background-color: #d84b72;
  border-color: #d84b72;
  color: white;
  transition: all 0.2s ease;
}

.btn-brand:hover {
  background-color: #c03e61;
  border-color: #c03e61;
  transform: translateY(-2px);
  color: white;
}

.btn-outline-danger {
  transition: all 0.2s ease;
}
.btn-outline-danger:hover {
  transform: translateY(-2px);
}

/* Tipografía */
.tracking-wide { 
  letter-spacing: 0.05em; 
}

/* Tarjetas */
.card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
  box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
}

/* Iconos */
.icon-box {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 0.75rem; /* Más cuadrado/moderno */
  flex-shrink: 0;
}

.icon-badge {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 0.5rem;
  font-size: 1rem;
  flex-shrink: 0;
}

/* Tablas */
.table tbody tr {
  transition: background-color 0.2s;
}

.table tbody tr:hover {
  background-color: #f8f9fa; /* Hover más sutil */
}
</style>