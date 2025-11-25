<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  ticket: Object
})

function eliminarTicket() {
  Swal.fire({
    title: "¿Eliminar ticket?",
    text: "Esta acción no se puede deshacer",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then(result => {
    if (result.isConfirmed) {
      router.delete(`/tickets/${props.ticket.id}`, {
        onSuccess: () => {
          Swal.fire("Eliminado", "Ticket eliminado exitosamente", "success")
        }
      })
    }
  })
}

function getEstadoPagoClass(estado) {
  const classes = {
    'pagado': 'bg-success',
    'pendiente': 'bg-warning text-dark',
    'parcial': 'bg-info'
  }
  return classes[estado] || 'bg-secondary'
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
    <div class="container-fluid px-3">

      <!-- Header -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
        <div>
          <h1 class="h4 text-primary fw-bold">
            <i class="fas fa-receipt me-2"></i> Ticket de Venta
          </h1>
          <p class="text-muted mb-0">{{ ticket.numero_ticket }}</p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-md-0">
          <button @click="imprimirTicket" class="btn btn-secondary">
            <i class="fas fa-print me-2"></i> Imprimir
          </button>
          <a :href="`/tickets/${ticket.id}/edit`" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i> Editar
          </a>
          <button @click="eliminarTicket" class="btn btn-danger">
            <i class="fas fa-trash me-2"></i> Eliminar
          </button>
          <a href="/tickets" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Volver
          </a>
        </div>
      </div>

      <div class="row g-3">

        <!-- Columna Izquierda: Información del Ticket -->
        <div class="col-lg-8">

          <!-- Información General -->
          <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-primary text-white">
              <h6 class="mb-0">
                <i class="fas fa-info-circle me-2"></i> Información General
              </h6>
            </div>
            <div class="card-body">
              <div class="row g-3">

                <!-- Cliente -->
                <div class="col-md-6">
                  <div class="d-flex align-items-start">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary me-3">
                      <i class="fas fa-user fa-lg"></i>
                    </div>
                    <div>
                      <label class="text-muted small mb-1">Cliente</label>
                      <div v-if="ticket.cliente">
                        <strong class="d-block">{{ ticket.cliente.nombre_completo }}</strong>
                        <small class="text-muted d-block" v-if="ticket.cliente.telefono">
                          <i class="fas fa-phone me-1"></i> {{ ticket.cliente.telefono }}
                        </small>
                        <small class="text-muted d-block" v-if="ticket.cliente.email">
                          <i class="fas fa-envelope me-1"></i> {{ ticket.cliente.email }}
                        </small>
                      </div>
                      <span v-else class="text-muted">Cliente General</span>
                    </div>
                  </div>
                </div>

                <!-- Empleado -->
                <div class="col-md-6">
                  <div class="d-flex align-items-start">
                    <div class="icon-box bg-success bg-opacity-10 text-success me-3">
                      <i class="fas fa-user-tie fa-lg"></i>
                    </div>
                    <div>
                      <label class="text-muted small mb-1">Empleado</label>
                      <div v-if="ticket.empleado">
                        <strong class="d-block">{{ ticket.empleado.nombre_completo }}</strong>
                        <small class="text-muted" v-if="ticket.empleado.puesto">
                          {{ ticket.empleado.puesto }}
                        </small>
                      </div>
                      <span v-else class="text-muted">No asignado</span>
                    </div>
                  </div>
                </div>

                <!-- Fecha -->
                <div class="col-md-6">
                  <div class="d-flex align-items-start">
                    <div class="icon-box bg-info bg-opacity-10 text-info me-3">
                      <i class="fas fa-calendar fa-lg"></i>
                    </div>
                    <div>
                      <label class="text-muted small mb-1">Fecha y Hora</label>
                      <strong class="d-block">{{ ticket.fecha }}</strong>
                    </div>
                  </div>
                </div>

                <!-- Método de Pago -->
                <div class="col-md-6">
                  <div class="d-flex align-items-start">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning me-3">
                      <i :class="['fas', getMetodoPagoIcon(ticket.metodo_pago), 'fa-lg']"></i>
                    </div>
                    <div>
                      <label class="text-muted small mb-1">Método de Pago</label>
                      <strong class="d-block text-capitalize">{{ ticket.metodo_pago }}</strong>
                    </div>
                  </div>
                </div>

              </div>

              <!-- Estado de Pago -->
              <div class="mt-3 pt-3 border-top">
                <div class="d-flex align-items-center">
                  <label class="text-muted small mb-0 me-3">Estado de Pago:</label>
                  <span 
                    class="badge px-3 py-2"
                    :class="getEstadoPagoClass(ticket.estado_pago)"
                  >
                    <i class="fas fa-check-circle me-1"></i>
                    {{ ticket.estado_pago.toUpperCase() }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Detalles de Venta -->
          <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white">
              <h6 class="mb-0">
                <i class="fas fa-list me-2"></i> Detalle de Venta
              </h6>
            </div>
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th style="width: 40%">Item</th>
                    <th class="text-center" style="width: 15%">Cantidad</th>
                    <th class="text-end" style="width: 20%">Precio Unit.</th>
                    <th class="text-end" style="width: 15%">Descuento</th>
                    <th class="text-end" style="width: 20%">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="detalle in ticket.detalles" :key="detalle.id">
                    <td>
                      <div class="d-flex align-items-center">
                        <div 
                          class="icon-badge me-2"
                          :class="detalle.tipo === 'Servicio' ? 'bg-info' : 'bg-warning'"
                        >
                          <i :class="detalle.tipo === 'Servicio' ? 'fas fa-cut' : 'fas fa-box'"></i>
                        </div>
                        <div>
                          <strong>{{ detalle.nombre }}</strong>
                          <small class="d-block text-muted">{{ detalle.tipo }}</small>
                        </div>
                      </div>
                    </td>
                    <td class="text-center align-middle">
                      <span class="badge bg-secondary">{{ detalle.cantidad }}</span>
                    </td>
                    <td class="text-end align-middle">
                      <span class="text-muted">${{ detalle.precio_unitario }}</span>
                    </td>
                    <td class="text-end align-middle">
                      <span class="text-danger" v-if="parseFloat(detalle.descuento) > 0">
                        -${{ detalle.descuento }}
                      </span>
                      <span v-else class="text-muted">-</span>
                    </td>
                    <td class="text-end align-middle">
                      <strong class="text-success">${{ detalle.subtotal }}</strong>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>

        <!-- Columna Derecha: Resumen y Notas -->
        <div class="col-lg-4">

          <!-- Resumen de Pagos -->
          <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-success text-white">
              <h6 class="mb-0">
                <i class="fas fa-calculator me-2"></i> Resumen de Pago
              </h6>
            </div>
            <div class="card-body">
              <div class="resumen-item">
                <span class="text-muted">Subtotal:</span>
                <strong>${{ ticket.subtotal }}</strong>
              </div>

              <div class="resumen-item" v-if="parseFloat(ticket.descuento) > 0">
                <span class="text-danger">Descuento:</span>
                <strong class="text-danger">-${{ ticket.descuento }}</strong>
              </div>

              <div class="resumen-item">
                <span class="text-muted">IVA (16%):</span>
                <strong>${{ ticket.impuesto }}</strong>
              </div>

              <hr class="my-3">

              <div class="resumen-item total">
                <span class="h5 mb-0">TOTAL:</span>
                <span class="h4 mb-0 text-success fw-bold">${{ ticket.total }}</span>
              </div>
            </div>
          </div>

          <!-- Notas -->
          <div v-if="ticket.notas" class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-info text-white">
              <h6 class="mb-0">
                <i class="fas fa-sticky-note me-2"></i> Notas
              </h6>
            </div>
            <div class="card-body">
              <p class="mb-0 text-muted" style="white-space: pre-wrap;">{{ ticket.notas }}</p>
            </div>
          </div>

          <!-- Información Adicional -->
          <div class="card shadow-sm border-0">
            <div class="card-header bg-secondary text-white">
              <h6 class="mb-0">
                <i class="fas fa-info-circle me-2"></i> Información Adicional
              </h6>
            </div>
            <div class="card-body">
              <div class="info-item">
                <i class="fas fa-clock text-muted me-2"></i>
                <small class="text-muted">Creado el:</small>
                <small class="d-block ms-4">{{ ticket.created_at }}</small>
              </div>
            </div>
          </div>

        </div>

      </div>

    </div>
  </AdminLayout>
</template>

<style scoped>
.icon-box {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 0.5rem;
  flex-shrink: 0;
}

.icon-badge {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  color: white;
  font-size: 0.875rem;
  flex-shrink: 0;
}

.resumen-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}

.resumen-item.total {
  padding-top: 0.5rem;
  margin-bottom: 0;
}

.info-item {
  padding: 0.5rem 0;
  border-bottom: 1px solid #e9ecef;
}

.info-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.badge {
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.card {
  transition: transform 0.2s;
}

.table tbody tr {
  transition: background-color 0.2s;
}

.table tbody tr:hover {
  background-color: rgba(0, 123, 255, 0.05);
}
</style>