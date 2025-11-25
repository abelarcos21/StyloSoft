<script setup>
import { router } from '@inertiajs/vue3'
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
    'pendiente': 'bg-warning text-dark',
    'confirmada': 'bg-info',
    'en_proceso': 'bg-primary',
    'completada': 'bg-success',
    'cancelada': 'bg-danger',
    'no_asistio': 'bg-dark'
  }
  return classes[estado] || 'bg-secondary'
}

function getTipoClass(tipo) {
  return tipo === 'express' ? 'bg-warning text-dark' : 'bg-info'
}
</script>

<template>
  <AdminLayout title="Detalle de Agenda">
    <div class="container-fluid px-3">

      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 text-primary fw-bold">
          <i class="fas fa-calendar-check me-2"></i> Detalle de Agenda #{{ agenda.id }}
        </h1>
        <div class="btn-group">
          <a href="/agendas" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Volver
          </a>
          <a :href="`/agendas/${agenda.id}/edit`" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i> Editar
          </a>
        </div>
      </div>

      <div class="row g-3">

        <!-- Información Principal -->
        <div class="col-lg-8">
          <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-light">
              <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i> Información de la Cita
              </h5>
            </div>
            <div class="card-body">
              <div class="row g-3">

                <!-- Cliente -->
                <div class="col-md-6">
                  <div class="border-start border-4 border-primary ps-3">
                    <label class="text-muted small fw-semibold">CLIENTE</label>
                    <h5 class="mb-1">{{ agenda.cliente.nombre_completo }}</h5>
                    <p class="mb-0 text-muted small">
                      <i class="fas fa-phone me-1"></i> {{ agenda.cliente.telefono }}
                    </p>
                    <p class="mb-0 text-muted small" v-if="agenda.cliente.email">
                      <i class="fas fa-envelope me-1"></i> {{ agenda.cliente.email }}
                    </p>
                  </div>
                </div>

                <!-- Empleado -->
                <div class="col-md-6">
                  <div class="border-start border-4 border-success ps-3">
                    <label class="text-muted small fw-semibold">EMPLEADO</label>
                    <h5 class="mb-1">{{ agenda.empleado.nombre_completo }}</h5>
                    <p class="mb-0 text-muted small">
                      <i class="fas fa-briefcase me-1"></i> {{ agenda.empleado.puesto }}
                    </p>
                    <p class="mb-0 text-muted small" v-if="agenda.empleado.telefono">
                      <i class="fas fa-phone me-1"></i> {{ agenda.empleado.telefono }}
                    </p>
                  </div>
                </div>

                <!-- Fecha y Hora -->
                <div class="col-md-6">
                  <div class="border-start border-4 border-info ps-3">
                    <label class="text-muted small fw-semibold">FECHA Y HORARIO</label>
                    <h5 class="mb-1">{{ agenda.fecha_formato }}</h5>
                    <p class="mb-0">
                      <i class="fas fa-clock me-1"></i> 
                      {{ agenda.hora_inicio }} - {{ agenda.hora_fin }}
                      <span class="badge bg-secondary ms-2">{{ agenda.duracion_total }} min</span>
                    </p>
                  </div>
                </div>

                <!-- Estado y Tipo -->
                <div class="col-md-6">
                  <div class="border-start border-4 border-warning ps-3">
                    <label class="text-muted small fw-semibold">ESTADO Y TIPO</label>
                    <div class="d-flex gap-2 mt-2">
                      <span 
                        class="badge px-3 py-2" 
                        :class="getEstadoClass(agenda.estado)"
                      >
                        {{ agenda.estado.replace('_', ' ').toUpperCase() }}
                      </span>
                      <span 
                        class="badge px-3 py-2" 
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

          <!-- Servicios -->
          <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-light text-white">
              <h5 class="mb-0">
                <i class="fas fa-cut me-2"></i> Servicios Agendados
              </h5>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Servicio</th>
                      <th class="text-center">Duración</th>
                      <th class="text-end">Precio</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="servicio in agenda.servicios" :key="servicio.id">
                      <td>
                        <i class="fas fa-scissors text-danger me-2"></i>
                        {{ servicio.nombre }}
                      </td>
                      <td class="text-center">
                        <span class="badge bg-secondary">
                          {{ servicio.duracion_minutos }} min
                        </span>
                      </td>
                      <td class="text-end fw-semibold">${{ servicio.precio }}</td>
                    </tr>
                  </tbody>
                  <tfoot class="table-active">
                    <tr>
                      <td class="fw-bold">TOTAL</td>
                      <td class="text-center fw-bold">{{ agenda.duracion_total }} min</td>
                      <td class="text-end fw-bold text-success fs-5">${{ agenda.total }}</td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>

          <!-- Notas -->
          <div class="card shadow-sm border-0" v-if="agenda.notas || agenda.razon_cancelacion">
            <div class="card-header bg-secondary text-white">
              <h5 class="mb-0">
                <i class="fas fa-sticky-note me-2"></i> Notas y Observaciones
              </h5>
            </div>
            <div class="card-body">
              <div v-if="agenda.notas" class="mb-3">
                <label class="fw-semibold text-muted small">NOTAS</label>
                <p class="mb-0">{{ agenda.notas }}</p>
              </div>

              <div v-if="agenda.razon_cancelacion" class="alert alert-danger mb-0">
                <strong>Razón de cancelación:</strong><br>
                {{ agenda.razon_cancelacion }}
                <br>
                <small class="text-muted">Cancelado por: {{ agenda.cancelado_por }}</small>
              </div>
            </div>
          </div>

        </div>

        <!-- Acciones Rápidas -->
        <div class="col-lg-4">
          <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-secondary text-white">
              <h5 class="mb-0">
                <i class="fas fa-bolt me-2"></i> Acciones Rápidas
              </h5>
            </div>
            <div class="card-body d-grid gap-2">

              <!-- Confirmar -->
              <button 
                v-if="agenda.estado === 'pendiente'"
                @click="confirmarAgenda" 
                class="btn btn-success"
              >
                <i class="fas fa-check me-2"></i> Confirmar Cita
              </button>

              <!-- Completar -->
              <button 
                v-if="['confirmada', 'en_proceso'].includes(agenda.estado)"
                @click="completarAgenda" 
                class="btn btn-primary"
              >
                <i class="fas fa-check-double me-2"></i> Marcar como Completada
              </button>

              <!-- No Asistió -->
              <button 
                v-if="['confirmada', 'pendiente'].includes(agenda.estado)"
                @click="noAsistio" 
                class="btn btn-dark"
              >
                <i class="fas fa-user-slash me-2"></i> Cliente No Asistió
              </button>

              <!-- Cancelar -->
              <button 
                v-if="['pendiente', 'confirmada'].includes(agenda.estado)"
                @click="cancelarAgenda" 
                class="btn btn-danger"
              >
                <i class="fas fa-ban me-2"></i> Cancelar Agenda
              </button>

              <hr>

              <!-- Editar -->
              <a 
                :href="`/agendas/${agenda.id}/edit`" 
                class="btn btn-warning"
              >
                <i class="fas fa-edit me-2"></i> Editar Agenda
              </a>

            </div>
          </div>

          <!-- Información adicional -->
          <div class="card shadow-sm border-0">
            <div class="card-header bg-light">
              <h6 class="mb-0">
                <i class="fas fa-info me-2"></i> Información Adicional
              </h6>
            </div>
            <div class="card-body">
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted small">Registrado:</span>
                <span class="fw-semibold small">{{ agenda.created_at }}</span>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-muted small">ID Agenda:</span>
                <span class="fw-semibold small">#{{ agenda.id }}</span>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>
  </AdminLayout>
</template>

<style scoped>
.border-start {
  border-width: 4px !important;
}

.card {
  transition: transform 0.2s;
}

.badge {
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.btn {
  transition: all 0.3s;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>