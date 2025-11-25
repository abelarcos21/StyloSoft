<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  empleado: Object
})

function getEstadoClass(estado) {
  const classes = {
    'activo': 'bg-success',
    'inactivo': 'bg-danger',
    'vacaciones': 'bg-warning text-dark'
  }
  return classes[estado] || 'bg-secondary'
}

function getEstadoCitaClass(estado) {
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
  <AdminLayout title="Detalle de Empleado">
    <div class="container-fluid px-3">

      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 text-primary fw-bold">
          <i class="fas fa-id-card me-2"></i> Perfil del Empleado
        </h1>
        <div class="btn-group">
          <a href="/empleados" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Volver
          </a>
          <a :href="`/empleados/${empleado.id}/edit`" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i> Editar
          </a>
          <a :href="`/agendas/create?empleado_id=${empleado.id}`" class="btn btn-success">
            <i class="fas fa-calendar-plus me-2"></i> Nueva Cita
          </a>
        </div>
      </div>

      <div class="row g-3">

        <!-- Información del Empleado -->
        <div class="col-lg-4">
          
          <!-- Datos Personales -->
          <div class="card shadow-sm border-0 mb-3">
            <div class="card-body text-center">
              <div class="avatar-large bg-success text-white mx-auto mb-3">
                {{ empleado.nombre.charAt(0) }}{{ empleado.apellido.charAt(0) }}
              </div>
              <h4 class="mb-1">{{ empleado.nombre_completo }}</h4>
              <p class="text-muted mb-2">{{ empleado.puesto }}</p>
              
              <!-- Estado -->
              <span 
                class="badge px-4 py-2 fs-6" 
                :class="getEstadoClass(empleado.estado)"
              >
                {{ empleado.estado.toUpperCase() }}
              </span>

              <div class="mt-3">
                <small class="text-muted">Empleado #{{ empleado.id }}</small>
              </div>
            </div>
          </div>

          <!-- Estadísticas -->
          <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-primary text-white">
              <h6 class="mb-0">
                <i class="fas fa-chart-line me-2"></i> Rendimiento
              </h6>
            </div>
            <div class="card-body">
              <div class="stat-item mb-3">
                <div class="d-flex justify-content-between align-items-center">
                  <span class="text-muted">Total Citas</span>
                  <strong class="fs-5 text-primary">{{ empleado.total_citas }}</strong>
                </div>
              </div>
              <div class="stat-item mb-3">
                <div class="d-flex justify-content-between align-items-center">
                  <span class="text-muted">Completadas</span>
                  <strong class="fs-5 text-success">{{ empleado.citas_completadas }}</strong>
                </div>
              </div>
              <div class="stat-item mb-3">
                <div class="d-flex justify-content-between align-items-center">
                  <span class="text-muted">Total Ventas</span>
                  <strong class="fs-5 text-info">${{ empleado.total_ventas }}</strong>
                </div>
              </div>
              <div class="stat-item">
                <div class="d-flex justify-content-between align-items-center">
                  <span class="text-muted">Comisiones</span>
                  <strong class="fs-5 text-warning">${{ empleado.comisiones_ganadas }}</strong>
                </div>
              </div>
            </div>
          </div>

          <!-- Información de Contacto -->
          <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-success text-white">
              <h6 class="mb-0">
                <i class="fas fa-address-book me-2"></i> Contacto
              </h6>
            </div>
            <div class="card-body">
              <div class="mb-3" v-if="empleado.telefono">
                <label class="text-muted small fw-semibold d-block">TELÉFONO</label>
                <a :href="`tel:${empleado.telefono}`" class="text-decoration-none">
                  <i class="fas fa-phone text-success me-2"></i>{{ empleado.telefono }}
                </a>
              </div>

              <div class="mb-3" v-if="empleado.email">
                <label class="text-muted small fw-semibold d-block">EMAIL</label>
                <a :href="`mailto:${empleado.email}`" class="text-decoration-none">
                  <i class="fas fa-envelope text-primary me-2"></i>{{ empleado.email }}
                </a>
              </div>

              <div v-if="empleado.direccion">
                <label class="text-muted small fw-semibold d-block">DIRECCIÓN</label>
                <p class="mb-0">
                  <i class="fas fa-map-marker-alt text-danger me-2"></i>{{ empleado.direccion }}
                </p>
              </div>
            </div>
          </div>

          <!-- Horario Laboral -->
          <div class="card shadow-sm border-0">
            <div class="card-header bg-info text-white">
              <h6 class="mb-0">
                <i class="fas fa-clock me-2"></i> Horario Laboral
              </h6>
            </div>
            <div class="card-body">
              <div class="mb-3" v-if="empleado.hora_entrada && empleado.hora_salida">
                <label class="text-muted small fw-semibold d-block">HORARIO</label>
                <p class="mb-0">
                  <i class="fas fa-clock text-info me-2"></i>
                  {{ empleado.hora_entrada }} - {{ empleado.hora_salida }}
                </p>
              </div>

              <div v-if="empleado.dias_laborales && empleado.dias_laborales.length">
                <label class="text-muted small fw-semibold d-block mb-2">DÍAS LABORALES</label>
                <div class="d-flex flex-wrap gap-1">
                  <span 
                    v-for="dia in empleado.dias_laborales" 
                    :key="dia"
                    class="badge bg-secondary"
                  >
                    {{ dia.charAt(0).toUpperCase() + dia.slice(1) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Información Detallada -->
        <div class="col-lg-8">

          <!-- Información Laboral -->
          <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-warning text-dark">
              <h6 class="mb-0">
                <i class="fas fa-briefcase me-2"></i> Información Laboral
              </h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="text-muted small fw-semibold d-block">FECHA DE INGRESO</label>
                  <p class="fw-semibold mb-0">
                    <i class="fas fa-calendar-check text-success me-2"></i>
                    {{ empleado.fecha_ingreso || 'No registrada' }}
                  </p>
                </div>

                <div class="col-md-4 mb-3" v-if="empleado.fecha_salida">
                  <label class="text-muted small fw-semibold d-block">FECHA DE SALIDA</label>
                  <p class="fw-semibold mb-0">
                    <i class="fas fa-calendar-times text-danger me-2"></i>
                    {{ empleado.fecha_salida }}
                  </p>
                </div>

                <div class="col-md-4 mb-3">
                  <label class="text-muted small fw-semibold d-block">SALARIO BASE</label>
                  <p class="fw-semibold text-success mb-0">
                    <i class="fas fa-dollar-sign me-2"></i>${{ empleado.salario }}
                  </p>
                </div>

                <div class="col-md-4 mb-3">
                  <label class="text-muted small fw-semibold d-block">COMISIÓN</label>
                  <p class="fw-semibold text-info mb-0">
                    <i class="fas fa-percentage me-2"></i>{{ empleado.comision_porcentaje }}%
                  </p>
                </div>
              </div>

              <div v-if="empleado.especialidades" class="mt-3">
                <label class="text-muted small fw-semibold d-block">ESPECIALIDADES</label>
                <p class="mb-0">
                  <i class="fas fa-star text-warning me-2"></i>
                  {{ empleado.especialidades }}
                </p>
              </div>
            </div>
          </div>

          <!-- Historial de Citas -->
          <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
              <h6 class="mb-0">
                <i class="fas fa-history me-2"></i> Historial de Citas Recientes
              </h6>
            </div>
            <div class="card-body p-0">
              <div v-if="empleado.ultimas_citas.length" class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Fecha</th>
                      <th>Cliente</th>
                      <th>Servicios</th>
                      <th class="text-center">Estado</th>
                      <th class="text-end">Total</th>
                      <th class="text-center">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="cita in empleado.ultimas_citas" :key="cita.id">
                      <td>
                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                        {{ cita.fecha }}
                      </td>
                      <td>
                        <i class="fas fa-user text-success me-2"></i>
                        {{ cita.cliente }}
                      </td>
                      <td>
                        <small>{{ cita.servicios }}</small>
                      </td>
                      <td class="text-center">
                        <span 
                          class="badge px-2 py-1" 
                          :class="getEstadoCitaClass(cita.estado)"
                        >
                          {{ cita.estado }}
                        </span>
                      </td>
                      <td class="text-end fw-semibold">${{ cita.total }}</td>
                      <td class="text-center">
                        <a 
                          :href="`/agendas/${cita.id}`" 
                          class="btn btn-sm btn-outline-info"
                          title="Ver detalles"
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
                <p class="text-muted mb-0">Sin historial de citas</p>
              </div>
            </div>
          </div>

          <!-- Información Adicional -->
          <div class="card shadow-sm border-0 mt-3">
            <div class="card-header bg-light">
              <h6 class="mb-0">
                <i class="fas fa-info-circle me-2"></i> Información Adicional
              </h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <small class="text-muted">Registrado el:</small>
                  <p class="fw-semibold">{{ empleado.created_at }}</p>
                </div>
                <div class="col-md-6">
                  <small class="text-muted">ID del Empleado:</small>
                  <p class="fw-semibold">#{{ empleado.id }}</p>
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
.avatar-large {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 2.5rem;
}

.stat-item {
  padding: 0.75rem;
  border-radius: 0.5rem;
  background-color: #f8f9fa;
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