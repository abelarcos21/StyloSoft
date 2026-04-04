<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  empleado: Object
})

function getEstadoClass(estado) {
  const classes = {
    'activo': 'bg-success text-white',
    'inactivo': 'bg-danger text-white',
    'vacaciones': 'bg-warning text-dark'
  }
  return classes[estado] || 'bg-secondary text-white'
}

function getEstadoCitaClass(estado) {
  const classes = {
    'pendiente': 'bg-warning text-dark',
    'confirmada': 'bg-info text-white',
    'en_proceso': 'bg-primary text-white',
    'completada': 'bg-success text-white',
    'cancelada': 'bg-danger text-white',
    'no_asistio': 'bg-dark text-white'
  }
  return classes[estado] || 'bg-secondary text-white'
}
</script>

<template>
  <AdminLayout title="Detalle de Empleado">
    <div class="container-fluid px-0 px-md-3">

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h1 class="h4 brand-accent fw-bold mb-0">
          <i class="fas fa-id-card me-2"></i> Perfil del Empleado
        </h1>
        <div class="d-flex flex-wrap gap-2">
          <Link href="/empleados" class="btn btn-light border rounded-pill px-3 shadow-sm text-muted fw-medium">
            <i class="fas fa-arrow-left me-1"></i> Volver
          </Link>
          <Link :href="`/empleados/${empleado.id}/edit`" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm fw-medium">
            <i class="fas fa-edit me-1"></i> Editar
          </Link>
          <Link :href="`/agendas/create?empleado_id=${empleado.id}`" class="btn btn-brand rounded-pill px-3 shadow-sm fw-medium">
            <i class="fas fa-calendar-plus me-1"></i> Nueva Cita
          </Link>
        </div>
      </div>

      <div class="row g-4">

        <div class="col-lg-4">

          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body text-center p-4">
              <div class="avatar-large brand-gradient text-white mx-auto mb-3 shadow-sm">
                {{ empleado.nombre.charAt(0) }}{{ empleado.apellido.charAt(0) }}
              </div>
              <h4 class="mb-1 fw-bold text-dark">{{ empleado.nombre_completo }}</h4>
              <p class="text-muted mb-3">{{ empleado.puesto }}</p>

              <span
                class="badge rounded-pill px-4 py-2 fs-6 shadow-sm mb-3"
                :class="getEstadoClass(empleado.estado)"
              >
                {{ empleado.estado.toUpperCase() }}
              </span>

              <div class="mt-2 border-top pt-3">
                <small class="text-muted fw-bold tracking-wide">ID EMPLEADO: #{{ empleado.id }}</small>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-chart-line text-primary me-2"></i> Rendimiento
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="stat-item rounded-3 mb-2 px-3 py-2 bg-light d-flex justify-content-between align-items-center">
                <span class="text-muted fw-medium">Total Citas</span>
                <strong class="fs-5 text-dark">{{ empleado.total_citas }}</strong>
              </div>
              <div class="stat-item rounded-3 mb-2 px-3 py-2 bg-light d-flex justify-content-between align-items-center">
                <span class="text-muted fw-medium">Completadas</span>
                <strong class="fs-5 text-success">{{ empleado.citas_completadas }}</strong>
              </div>
              <div class="stat-item rounded-3 mb-2 px-3 py-2 bg-light d-flex justify-content-between align-items-center">
                <span class="text-muted fw-medium">Total Ventas</span>
                <strong class="fs-5 brand-accent">${{ empleado.total_ventas }}</strong>
              </div>
              <div class="stat-item rounded-3 px-3 py-2 bg-light d-flex justify-content-between align-items-center">
                <span class="text-muted fw-medium">Comisiones</span>
                <strong class="fs-5 text-warning">${{ empleado.comisiones_ganadas }}</strong>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-address-book text-success me-2"></i> Contacto
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="mb-3" v-if="empleado.telefono">
                <label class="text-muted small fw-bold text-uppercase tracking-wide d-block">Teléfono</label>
                <a :href="`tel:${empleado.telefono}`" class="text-decoration-none text-dark fw-medium fs-6 hover-brand">
                  <i class="fas fa-phone text-muted me-2"></i>{{ empleado.telefono }}
                </a>
              </div>

              <div class="mb-3" v-if="empleado.email">
                <label class="text-muted small fw-bold text-uppercase tracking-wide d-block">Email</label>
                <a :href="`mailto:${empleado.email}`" class="text-decoration-none text-dark fw-medium fs-6 hover-brand">
                  <i class="fas fa-envelope text-muted me-2"></i>{{ empleado.email }}
                </a>
              </div>

              <div v-if="empleado.direccion">
                <label class="text-muted small fw-bold text-uppercase tracking-wide d-block">Dirección</label>
                <p class="mb-0 text-dark fw-medium">
                  <i class="fas fa-map-marker-alt text-muted me-2"></i>{{ empleado.direccion }}
                </p>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-clock text-info me-2"></i> Horario Laboral
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="mb-3" v-if="empleado.hora_entrada && empleado.hora_salida">
                <label class="text-muted small fw-bold text-uppercase tracking-wide d-block mb-1">Horario Fijo</label>
                <div class="d-inline-flex align-items-center bg-light rounded-pill px-3 py-2">
                  <i class="fas fa-clock text-info me-2"></i>
                  <span class="fw-semibold">{{ empleado.hora_entrada }} - {{ empleado.hora_salida }}</span>
                </div>
              </div>

              <div v-if="empleado.dias_laborales && empleado.dias_laborales.length">
                <label class="text-muted small fw-bold text-uppercase tracking-wide d-block mb-2">Días Laborales</label>
                <div class="d-flex flex-wrap gap-2">
                  <span
                    v-for="dia in empleado.dias_laborales"
                    :key="dia"
                    class="badge border text-dark fw-medium px-2 py-1"
                  >
                    {{ dia.charAt(0).toUpperCase() + dia.slice(1) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="col-lg-8">

          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-briefcase text-warning me-2"></i> Información Laboral
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="row g-4">

                <div class="col-sm-6 col-md-4">
                  <label class="text-muted small fw-bold text-uppercase tracking-wide d-block">Ingreso</label>
                  <p class="fw-semibold text-dark fs-6 mb-0">
                    <i class="fas fa-calendar-check text-muted me-2"></i>
                    {{ empleado.fecha_ingreso || 'No registrada' }}
                  </p>
                </div>

                <div class="col-sm-6 col-md-4" v-if="empleado.fecha_salida">
                  <label class="text-muted small fw-bold text-uppercase tracking-wide d-block">Salida</label>
                  <p class="fw-semibold text-danger fs-6 mb-0">
                    <i class="fas fa-calendar-times me-2"></i>
                    {{ empleado.fecha_salida }}
                  </p>
                </div>

                <div class="col-sm-6 col-md-4">
                  <label class="text-muted small fw-bold text-uppercase tracking-wide d-block">Salario Base</label>
                  <p class="fw-semibold text-dark fs-6 mb-0">
                    <i class="fas fa-dollar-sign text-success me-2"></i>{{ empleado.salario }}
                  </p>
                </div>

                <div class="col-sm-6 col-md-4">
                  <label class="text-muted small fw-bold text-uppercase tracking-wide d-block">Comisión</label>
                  <p class="fw-semibold text-dark fs-6 mb-0">
                    <i class="fas fa-percentage text-info me-2"></i>{{ empleado.comision_porcentaje }}%
                  </p>
                </div>

                <div class="col-12" v-if="empleado.especialidades">
                  <label class="text-muted small fw-bold text-uppercase tracking-wide d-block">Especialidades</label>
                  <p class="fw-medium text-dark bg-light rounded-3 p-3 mb-0 border">
                    <i class="fas fa-star text-warning me-2"></i>
                    {{ empleado.especialidades }}
                  </p>
                </div>

              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-history brand-accent me-2"></i> Citas Recientes
              </h5>
            </div>
            <div class="card-body p-0">
              <div v-if="empleado.ultimas_citas.length" class="table-responsive">
                <table class="table table-hover align-middle mb-0 custom-table">
                  <thead class="table-light text-muted small text-uppercase tracking-wide">
                    <tr>
                      <th class="ps-4 fw-bold border-0">Fecha</th>
                      <th class="fw-bold border-0">Cliente</th>
                      <th class="fw-bold border-0">Servicios</th>
                      <th class="text-center fw-bold border-0">Estado</th>
                      <th class="text-end fw-bold border-0">Total</th>
                      <th class="text-center fw-bold border-0 pe-4">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="cita in empleado.ultimas_citas" :key="cita.id">
                      <td class="ps-4">
                        <span class="fw-medium text-dark">{{ cita.fecha }}</span>
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="avatar-small bg-light text-secondary rounded-circle me-2 d-flex justify-content-center align-items-center fw-bold" style="width: 32px; height: 32px;">
                            <i class="fas fa-user fs-6"></i>
                          </div>
                          <span class="fw-medium">{{ cita.cliente }}</span>
                        </div>
                      </td>
                      <td>
                        <span class="text-muted small">{{ cita.servicios }}</span>
                      </td>
                      <td class="text-center">
                        <span
                          class="badge rounded-pill px-3 py-1 fw-medium"
                          :class="getEstadoCitaClass(cita.estado)"
                        >
                          {{ cita.estado.replace('_', ' ').toUpperCase() }}
                        </span>
                      </td>
                      <td class="text-end fw-bold text-dark">${{ cita.total }}</td>
                      <td class="text-center pe-4">
                        <Link
                          :href="`/agendas/${cita.id}`"
                          class="btn btn-sm btn-light text-primary rounded-circle shadow-sm"
                          title="Ver detalles"
                        >
                          <i class="fas fa-eye"></i>
                        </Link>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div v-else class="text-center py-5">
                <div class="d-inline-flex justify-content-center align-items-center rounded-circle bg-light mb-3" style="width: 80px; height: 80px;">
                  <i class="fas fa-calendar-times fa-2x text-muted"></i>
                </div>
                <h6 class="fw-bold text-dark">Sin historial</h6>
                <p class="text-muted mb-0 small">Este empleado aún no tiene citas registradas.</p>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
              <div class="d-flex flex-wrap align-items-center text-muted small">
                <div class="me-4 mb-2 mb-md-0">
                  <i class="fas fa-info-circle me-1"></i>
                  <span class="fw-bold text-uppercase tracking-wide">Registro en sistema:</span>
                  <span class="ms-1">{{ empleado.created_at }}</span>
                </div>
                <div>
                  <i class="fas fa-hashtag me-1"></i>
                  <span class="fw-bold text-uppercase tracking-wide">ID Interno:</span>
                  <span class="ms-1">{{ empleado.id }}</span>
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
/* Variables y colores corporativos */
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
  box-shadow: 0 4px 10px rgba(216, 75, 114, 0.3) !important;
}

.brand-gradient {
  background: linear-gradient(135deg, #d84b72 0%, #ff7ea5 100%);
}

.hover-brand:hover {
  color: #d84b72 !important;
}

/* Tipografía de apoyo */
.tracking-wide {
  letter-spacing: 0.05em;
}

/* Avatares */
.avatar-large {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 2.5rem;
}

/* Tarjetas y transiciones */
.card {
  transition: box-shadow 0.2s ease;
}

.card:hover {
  box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
}

/* Tablas */
.custom-table tbody tr {
  transition: background-color 0.2s;
}

.custom-table tbody tr:hover {
  background-color: #f8f9fa;
}

.custom-table td {
  padding: 1rem 0.5rem;
  border-bottom: 1px solid #f1f1f1;
}

.custom-table tbody tr:last-child td {
  border-bottom: none;
}
</style>
