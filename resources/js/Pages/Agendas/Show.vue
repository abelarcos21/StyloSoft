<script setup>
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  agenda: Object
})

function cancelarAgenda() {
  Swal.fire({
    title: "¿Cancelar esta agenda?",
    text: "El estado cambiará a 'cancelada'",
    icon: "warning",
    input: 'textarea',
    inputPlaceholder: 'Razón de cancelación',
    showCancelButton: true,
    confirmButtonText: "Sí, cancelar",
    cancelButtonText: "No",
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
  }).then(result => {
    if (result.isConfirmed) {
      router.put(`/agendas/${props.agenda.id}/cancelar`, {
        razon: result.value || 'Sin razón',
        cancelado_por: 'sistema'
      }, {
        onSuccess: () => {
          Swal.fire("Cancelada", "Agenda cancelada exitosamente", "success")
        }
      })
    }
  })
}

function confirmarAgenda() {
  Swal.fire({
    title: "¿Confirmar cita?",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, confirmar",
    confirmButtonColor: '#198754',
    cancelButtonColor: '#6c757d',
  }).then(result => {
    if (result.isConfirmed) {
      router.put(`/agendas/${props.agenda.id}/confirmar`, {}, {
        onSuccess: () => {
          Swal.fire("Confirmada", "Cita confirmada", "success")
        }
      })
    }
  })
}

function completarAgenda() {
  Swal.fire({
    title: "¿Marcar como completada?",
    text: "Esto actualizará el historial del cliente",
    icon: "success",
    showCancelButton: true,
    confirmButtonText: "Sí, completar",
    confirmButtonColor: '#0d6efd',
    cancelButtonColor: '#6c757d',
  }).then(result => {
    if (result.isConfirmed) {
      router.put(`/agendas/${props.agenda.id}/completar`, {}, {
        onSuccess: () => {
          Swal.fire("Completada", "Agenda completada", "success")
        }
      })
    }
  })
}

function noAsistio() {
  Swal.fire({
    title: "¿Cliente no asistió?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, no asistió",
    confirmButtonColor: '#212529',
    cancelButtonColor: '#6c757d',
  }).then(result => {
    if (result.isConfirmed) {
      router.put(`/agendas/${props.agenda.id}/no-asistio`, {}, {
        onSuccess: () => {
          Swal.fire("Registrado", "Marcado como 'No asistió'", "info")
        }
      })
    }
  })
}

function getEstadoClass(estado) {
  const classes = {
    'pendiente': 'bg-warning-subtle text-warning-emphasis',
    'confirmada': 'bg-info-subtle text-info-emphasis',
    'en_proceso': 'bg-primary-subtle text-primary-emphasis',
    'completada': 'bg-success-subtle text-success-emphasis',
    'cancelada': 'bg-danger-subtle text-danger-emphasis',
    'no_asistio': 'bg-secondary-subtle text-secondary-emphasis'
  }
  return classes[estado] || 'bg-secondary-subtle text-secondary'
}

function getTipoClass(tipo) {
  return tipo === 'express' ? 'bg-warning-subtle text-warning-emphasis' : 'bg-brand-subtle brand-accent'
}
</script>

<template>
  <AdminLayout title="Detalle de Agenda">
    <div class="container-fluid px-0 px-md-3">

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h1 class="h4 brand-accent fw-bold mb-0">
          <i class="fas fa-calendar-check me-2"></i> Detalle de Agenda #{{ agenda.id }}
        </h1>
        <div class="d-flex gap-2">
          <Link href="/agendas" class="btn btn-light rounded-pill px-4 shadow-sm text-muted fw-medium">
            <i class="fas fa-arrow-left me-2"></i> Volver
          </Link>
          <Link :href="`/agendas/${agenda.id}/edit`" class="btn btn-brand rounded-pill px-4 shadow-sm fw-medium">
            <i class="fas fa-edit me-2"></i> Editar
          </Link>
        </div>
      </div>

      <div class="row g-4">

        <div class="col-lg-8">

          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-info-circle brand-accent me-2"></i> Información de la Cita
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="row g-4">

                <div class="col-md-6">
                  <div class="p-3 bg-light rounded-4 h-100 border-start border-4 border-primary">
                    <label class="text-muted small fw-bold text-uppercase tracking-wide mb-2">Cliente</label>
                    <h5 class="mb-1 text-dark fw-bold">{{ agenda.cliente.nombre_completo }}</h5>
                    <p class="mb-1 text-muted small">
                      <i class="fas fa-phone me-2 text-primary"></i> {{ agenda.cliente.telefono }}
                    </p>
                    <p class="mb-0 text-muted small" v-if="agenda.cliente.email">
                      <i class="fas fa-envelope me-2 text-primary"></i> {{ agenda.cliente.email }}
                    </p>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="p-3 bg-light rounded-4 h-100 border-start border-4 border-success">
                    <label class="text-muted small fw-bold text-uppercase tracking-wide mb-2">Empleado</label>
                    <h5 class="mb-1 text-dark fw-bold">{{ agenda.empleado.nombre_completo }}</h5>
                    <p class="mb-1 text-muted small">
                      <i class="fas fa-briefcase me-2 text-success"></i> {{ agenda.empleado.puesto }}
                    </p>
                    <p class="mb-0 text-muted small" v-if="agenda.empleado.telefono">
                      <i class="fas fa-phone me-2 text-success"></i> {{ agenda.empleado.telefono }}
                    </p>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="p-3 bg-light rounded-4 h-100 border-start border-4 border-info">
                    <label class="text-muted small fw-bold text-uppercase tracking-wide mb-2">Fecha y Horario</label>
                    <h5 class="mb-1 text-dark fw-bold">{{ agenda.fecha_formato }}</h5>
                    <p class="mb-0 text-muted">
                      <i class="fas fa-clock me-2 text-info"></i>
                      {{ agenda.hora_inicio }} - {{ agenda.hora_fin }}
                      <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-2">
                        {{ agenda.duracion_total }} min
                      </span>
                    </p>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="p-3 bg-light rounded-4 h-100 border-start border-4" style="border-color: #d84b72 !important;">
                    <label class="text-muted small fw-bold text-uppercase tracking-wide mb-2">Estado y Tipo</label>
                    <div class="d-flex flex-wrap gap-2 mt-1">
                      <span
                        class="badge rounded-pill px-3 py-2 fw-semibold"
                        :class="getEstadoClass(agenda.estado)"
                      >
                        {{ agenda.estado.replace('_', ' ').toUpperCase() }}
                      </span>
                      <span
                        class="badge rounded-pill px-3 py-2 fw-semibold"
                        :class="getTipoClass(agenda.tipo)"
                      >
                        {{ agenda.tipo.toUpperCase() }}
                      </span>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-cut brand-accent me-2"></i> Servicios Agendados
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="border rounded-4 overflow-hidden">
                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0 custom-table">
                    <thead class="bg-light text-muted">
                      <tr>
                        <th class="border-0 font-weight-semibold ps-4">Servicio</th>
                        <th class="text-center border-0 font-weight-semibold">Duración</th>
                        <th class="text-end border-0 font-weight-semibold pe-4">Precio</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="servicio in agenda.servicios" :key="servicio.id">
                        <td class="ps-4 fw-medium text-dark">
                          <i class="fas fa-circle text-muted" style="font-size: 0.5rem; margin-right: 8px;"></i>
                          {{ servicio.nombre }}
                        </td>
                        <td class="text-center">
                          <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2 fw-medium">
                            {{ servicio.duracion_minutos }} min
                          </span>
                        </td>
                        <td class="text-end pe-4 text-dark fw-medium">${{ parseFloat(servicio.precio).toFixed(2) }}</td>
                      </tr>
                    </tbody>
                    <tfoot class="bg-light">
                      <tr>
                        <td class="fw-bold ps-4 text-dark">TOTAL</td>
                        <td class="text-center fw-bold text-dark">{{ agenda.duracion_total }} min</td>
                        <td class="text-end pe-4 fw-bold brand-accent fs-5">${{ parseFloat(agenda.total).toFixed(2) }}</td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4" v-if="agenda.notas || agenda.razon_cancelacion">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-sticky-note text-secondary me-2"></i> Notas y Observaciones
              </h5>
            </div>
            <div class="card-body p-4">
              <div v-if="agenda.notas" class="p-3 bg-light rounded-4 mb-3">
                <label class="text-muted small fw-bold text-uppercase tracking-wide mb-2">Notas del Cliente</label>
                <p class="mb-0 text-dark">{{ agenda.notas }}</p>
              </div>

              <div v-if="agenda.razon_cancelacion" class="p-3 bg-danger-subtle text-danger-emphasis rounded-4">
                <label class="small fw-bold text-uppercase tracking-wide mb-1">
                  <i class="fas fa-exclamation-circle me-1"></i> Razón de Cancelación
                </label>
                <p class="mb-1 fw-medium">{{ agenda.razon_cancelacion }}</p>
                <small class="opacity-75">Cancelado por: {{ agenda.cancelado_por }}</small>
              </div>
            </div>
          </div>

        </div>

        <div class="col-lg-4">

          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-bolt text-warning me-2"></i> Acciones Rápidas
              </h5>
            </div>
            <div class="card-body p-4 d-flex flex-column gap-3">

              <button
                v-if="agenda.estado === 'pendiente'"
                @click="confirmarAgenda"
                class="btn btn-outline-success rounded-pill fw-medium py-2 d-flex align-items-center justify-content-center"
              >
                <i class="fas fa-check me-2"></i> Confirmar Cita
              </button>

              <button
                v-if="['confirmada', 'en_proceso'].includes(agenda.estado)"
                @click="completarAgenda"
                class="btn btn-outline-primary rounded-pill fw-medium py-2 d-flex align-items-center justify-content-center"
              >
                <i class="fas fa-check-double me-2"></i> Marcar Completada
              </button>

              <button
                v-if="['confirmada', 'pendiente'].includes(agenda.estado)"
                @click="noAsistio"
                class="btn btn-outline-dark rounded-pill fw-medium py-2 d-flex align-items-center justify-content-center"
              >
                <i class="fas fa-user-slash me-2"></i> Cliente No Asistió
              </button>

              <button
                v-if="['pendiente', 'confirmada'].includes(agenda.estado)"
                @click="cancelarAgenda"
                class="btn btn-outline-danger rounded-pill fw-medium py-2 d-flex align-items-center justify-content-center"
              >
                <i class="fas fa-ban me-2"></i> Cancelar Agenda
              </button>

              <div class="border-top my-1" v-if="['pendiente', 'confirmada', 'en_proceso'].includes(agenda.estado)"></div>

              <Link
                :href="`/agendas/${agenda.id}/edit`"
                class="btn btn-brand rounded-pill fw-medium py-2 d-flex align-items-center justify-content-center shadow-sm"
              >
                <i class="fas fa-edit me-2"></i> Modificar Agenda
              </Link>

            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
              <h6 class="mb-0 fw-bold text-dark">
                <i class="fas fa-info text-secondary me-2"></i> Detalles del Registro
              </h6>
            </div>
            <div class="card-body p-4">
              <ul class="list-unstyled mb-0">
                <li class="d-flex justify-content-between align-items-center mb-3">
                  <span class="text-muted small">Registrado el:</span>
                  <span class="fw-semibold text-dark small">{{ agenda.created_at }}</span>
                </li>
                <li class="d-flex justify-content-between align-items-center">
                  <span class="text-muted small">ID Sistema:</span>
                  <span class="badge bg-light border text-dark">#{{ agenda.id }}</span>
                </li>
              </ul>
            </div>
          </div>

        </div>

      </div>

    </div>
  </AdminLayout>
</template>

<style scoped>
/* Variables y colores corporativos */
.brand-accent {
  color: #d84b72;
}

.bg-brand-subtle {
  background-color: #fce8ee;
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
  box-shadow: 0 4px 10px rgba(216, 75, 114, 0.3) !important;
}

/* Hover de tarjetas */
.card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.card:hover {
  box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
}

/* Badges e Identificadores */
.bg-secondary-subtle { background-color: #e2e3e5 !important; }
.tracking-wide { letter-spacing: 0.05em; }

/* Botones Sidebar (efecto sutil) */
.col-lg-4 .btn {
  transition: all 0.2s ease;
}
.col-lg-4 .btn:active {
  transform: scale(0.98);
}

/* Tabla de servicios */
.custom-table th {
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
}
</style>
