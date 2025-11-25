<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  servicio: Object
})

function duplicarServicio() {
  Swal.fire({
    title: "¿Duplicar servicio?",
    text: "Se creará una copia de este servicio",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, duplicar",
    cancelButtonText: "Cancelar",
  }).then(result => {
    if (result.isConfirmed) {
      router.post(`/servicios/${props.servicio.id}/duplicar`, {}, {
        onSuccess: () => {
          Swal.fire("Duplicado", "Servicio duplicado exitosamente", "success")
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
</script>

<template>
  <AdminLayout title="Detalle de Servicio">
    <div class="container-fluid px-3">

      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 text-primary fw-bold">
          <i class="fas fa-cut me-2"></i> Detalle del Servicio
        </h1>
        <div class="btn-group">
          <a href="/servicios" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Volver
          </a>
          <a :href="`/servicios/${servicio.id}/edit`" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i> Editar
          </a>
          <button @click="duplicarServicio" class="btn btn-info">
            <i class="fas fa-copy me-2"></i> Duplicar
          </button>
        </div>
      </div>

      <div class="row g-3">

        <!-- Información del Servicio -->
        <div class="col-lg-4">
          
          <!-- Datos Principales -->
          <div class="card shadow-sm border-0 mb-3">
            <div class="card-body text-center">
              <div class="service-icon bg-primary text-white mx-auto mb-3">
                <i class="fas fa-scissors fa-3x"></i>
              </div>
              <h4 class="mb-1">{{ servicio.nombre }}</h4>
              <p class="text-muted mb-2" v-if="servicio.categoria">
                <i class="fas fa-folder me-1"></i>{{ servicio.categoria }}
              </p>
              
              <!-- Estado -->
              <div class="mb-3">
                <span 
                  class="badge px-4 py-2 fs-6" 
                  :class="servicio.activo ? 'bg-success' : 'bg-danger'"
                >
                  {{ servicio.activo ? 'ACTIVO' : 'INACTIVO' }}
                </span>
              </div>

              <!-- Orden -->
              <small class="text-muted">Orden de visualización: {{ servicio.orden }}</small>
            </div>
          </div>

          <!-- Precio y Duración -->
          <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-success text-white">
              <h6 class="mb-0">
                <i class="fas fa-tags me-2"></i> Precio y Duración
              </h6>
            </div>
            <div class="card-body">
              <!-- Precio -->
              <div class="text-center mb-3">
                <label class="text-muted small fw-semibold d-block">PRECIO</label>
                <h1 class="display-4 text-success mb-0">${{ servicio.precio }}</h1>
              </div>

              <hr>

              <!-- Duración -->
              <div class="text-center mb-3">
                <label class="text-muted small fw-semibold d-block">DURACIÓN ESTIMADA</label>
                <h3 class="text-info mb-0">
                  <i class="fas fa-clock me-2"></i>{{ servicio.duracion_horas }}
                </h3>
                <small class="text-muted">{{ servicio.duracion_minutos }} minutos</small>
              </div>

              <!-- Depósito -->
              <div v-if="servicio.requiere_deposito" class="alert alert-warning mb-0">
                <strong><i class="fas fa-hand-holding-usd me-2"></i>Requiere Depósito</strong>
                <div v-if="servicio.deposito_minimo">
                  Mínimo: <strong>${{ servicio.deposito_minimo }}</strong>
                </div>
              </div>
            </div>
          </div>

          <!-- Descripción -->
          <div class="card shadow-sm border-0" v-if="servicio.descripcion">
            <div class="card-header bg-info text-white">
              <h6 class="mb-0">
                <i class="fas fa-info-circle me-2"></i> Descripción
              </h6>
            </div>
            <div class="card-body">
              <p class="mb-0">{{ servicio.descripcion }}</p>
            </div>
          </div>

        </div>

        <!-- Estadísticas e Historial -->
        <div class="col-lg-8">

          <!-- Estadísticas -->
          <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-primary text-white">
              <h6 class="mb-0">
                <i class="fas fa-chart-bar me-2"></i> Estadísticas del Servicio
              </h6>
            </div>
            <div class="card-body">
              <div class="row text-center">
                <div class="col-md-4 mb-3">
                  <div class="stat-card">
                    <h2 class="text-primary mb-0">{{ servicio.total_agendado }}</h2>
                    <small class="text-muted">Veces Agendado</small>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="stat-card">
                    <h2 class="text-success mb-0">{{ servicio.total_completado }}</h2>
                    <small class="text-muted">Servicios Completados</small>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="stat-card">
                    <h2 class="text-info mb-0">${{ servicio.ingresos_generados }}</h2>
                    <small class="text-muted">Ingresos Generados</small>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Historial de Agendas -->
          <div class="card shadow-sm border-0">
            <div class="card-header bg-secondary text-white">
              <h6 class="mb-0">
                <i class="fas fa-history me-2"></i> Historial de Agendas
              </h6>
            </div>
            <div class="card-body p-0">
              <div v-if="servicio.ultimas_agendas.length" class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>ID</th>
                      <th>Fecha</th>
                      <th>Cliente</th>
                      <th>Empleado</th>
                      <th class="text-center">Estado</th>
                      <th class="text-end">Precio</th>
                      <th class="text-center">Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="agenda in servicio.ultimas_agendas" :key="agenda.id">
                      <td>
                        <span class="badge bg-secondary">{{ agenda.id }}</span>
                      </td>
                      <td>
                        <small>{{ agenda.fecha }}</small>
                      </td>
                      <td>
                        <i class="fas fa-user text-primary me-1"></i>
                        {{ agenda.cliente }}
                      </td>
                      <td>
                        <i class="fas fa-user-tie text-success me-1"></i>
                        {{ agenda.empleado }}
                      </td>
                      <td class="text-center">
                        <span 
                          class="badge px-2 py-1" 
                          :class="getEstadoClass(agenda.estado)"
                        >
                          {{ agenda.estado }}
                        </span>
                      </td>
                      <td class="text-end fw-semibold text-success">
                        ${{ agenda.precio }}
                      </td>
                      <td class="text-center">
                        <a 
                          :href="`/agendas/${agenda.id}`" 
                          class="btn btn-sm btn-outline-info"
                          title="Ver agenda"
                        >
                          <i class="fas fa-eye"></i>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div v-else class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">No hay historial de agendas para este servicio</p>
              </div>
            </div>
          </div>

          <!-- Información Adicional -->
          <div class="card shadow-sm border-0 mt-3">
            <div class="card-header bg-light">
              <h6 class="mb-0">
                <i class="fas fa-info me-2"></i> Información Adicional
              </h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <small class="text-muted">Creado el:</small>
                  <p class="fw-semibold">{{ servicio.created_at }}</p>
                </div>
                <div class="col-md-6">
                  <small class="text-muted">ID del Servicio:</small>
                  <p class="fw-semibold">#{{ servicio.id }}</p>
                </div>
              </div>
            </div>
          </div>

        </div>

      </div>

    </div>
  </AdminLayout>
</template>

<style scoped>
.service-icon {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-card {
  padding: 1.5rem;
  border-radius: 0.5rem;
  background-color: #f8f9fa;
  border: 2px solid #e9ecef;
}

.card {
  transition: transform 0.2s;
}

.badge {
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.table tbody tr {
  transition: background-color 0.2s;
}

.table tbody tr:hover {
  background-color: rgba(0, 123, 255, 0.05);
}
</style>