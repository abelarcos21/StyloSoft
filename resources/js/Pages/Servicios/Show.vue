<script setup>
import { router, Link } from '@inertiajs/vue3'
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
    confirmButtonColor: '#d84b72',
    cancelButtonColor: '#6c757d',
  }).then(result => {
    if (result.isConfirmed) {
      router.post(`/servicios/${props.servicio.id}/duplicar`, {}, {
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

function getEstadoClass(estado) {
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
  <AdminLayout title="Detalle de Servicio">
    <div class="container-fluid px-0 px-md-3">

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h1 class="h4 brand-accent fw-bold mb-0">
          <i class="fas fa-cut me-2"></i> Detalle del Servicio
        </h1>
        <div class="d-flex flex-wrap gap-2">
          <Link href="/servicios" class="btn btn-light border rounded-pill px-4 shadow-sm text-muted fw-medium">
            <i class="fas fa-arrow-left me-1"></i> Volver
          </Link>
          <button @click="duplicarServicio" class="btn btn-info rounded-pill px-4 shadow-sm text-white fw-medium">
            <i class="fas fa-copy me-1"></i> Duplicar
          </button>
          <Link :href="`/servicios/${servicio.id}/edit`" class="btn btn-warning rounded-pill px-4 shadow-sm fw-medium text-dark">
            <i class="fas fa-edit me-1"></i> Editar
          </Link>
        </div>
      </div>

      <div class="row g-4 mb-5">

        <div class="col-lg-4">

          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body text-center p-4">
              <div class="service-icon bg-brand-light brand-accent mx-auto mb-3 shadow-sm">
                <i class="fas fa-scissors fa-3x"></i>
              </div>
              <h4 class="mb-1 fw-bold text-dark">{{ servicio.nombre }}</h4>
              <p class="text-muted mb-3 fw-medium" v-if="servicio.categoria">
                <i class="fas fa-folder text-warning me-1"></i> {{ servicio.categoria }}
              </p>

              <div class="mb-4">
                <span
                  class="badge rounded-pill px-4 py-2 fs-6 tracking-wide"
                  :class="servicio.activo ? 'bg-success' : 'bg-danger'"
                >
                  <i :class="servicio.activo ? 'fas fa-check-circle me-1' : 'fas fa-times-circle me-1'"></i>
                  {{ servicio.activo ? 'ACTIVO' : 'INACTIVO' }}
                </span>
              </div>

              <div class="bg-light rounded-3 py-2 px-3 d-inline-block">
                <small class="text-muted fw-semibold text-uppercase tracking-wide">
                  <i class="fas fa-sort-numeric-down me-1"></i> Orden: {{ servicio.orden }}
                </small>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-tags text-success me-2"></i> Precio y Duración
              </h5>
            </div>
            <div class="card-body p-4">

              <div class="text-center mb-4">
                <label class="text-muted small fw-bold text-uppercase tracking-wide mb-1">Costo del Servicio</label>
                <h1 class="display-5 text-success mb-0 fw-bold">${{ servicio.precio }}</h1>
              </div>

              <hr class="text-muted opacity-25">

              <div class="text-center mb-4 mt-3">
                <label class="text-muted small fw-bold text-uppercase tracking-wide mb-1">Duración Estimada</label>
                <div class="d-flex align-items-center justify-content-center text-info">
                  <i class="fas fa-clock fa-2x me-2"></i>
                  <h3 class="mb-0 fw-bold">{{ servicio.duracion_horas }}</h3>
                </div>
                <small class="text-muted d-block mt-1">{{ servicio.duracion_minutos }} minutos totales</small>
              </div>

              <div v-if="servicio.requiere_deposito" class="alert alert-warning border-0 d-flex flex-column align-items-center mb-0 rounded-3 text-center">
                <span class="fw-bold mb-1"><i class="fas fa-hand-holding-usd me-1"></i> Requiere Depósito</span>
                <span class="fs-5" v-if="servicio.deposito_minimo">
                  Mínimo: <strong>${{ servicio.deposito_minimo }}</strong>
                </span>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4" v-if="servicio.descripcion">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-info-circle text-info me-2"></i> Descripción
              </h5>
            </div>
            <div class="card-body p-4">
              <p class="mb-0 text-secondary lh-lg">{{ servicio.descripcion }}</p>
            </div>
          </div>

        </div>

        <div class="col-lg-8">

          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-chart-bar text-primary me-2"></i> Rendimiento del Servicio
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="row g-3 text-center">
                <div class="col-md-4">
                  <div class="stat-card">
                    <div class="text-primary mb-2"><i class="fas fa-calendar-check fa-2x"></i></div>
                    <h2 class="text-dark fw-bold mb-0">{{ servicio.total_agendado }}</h2>
                    <small class="text-muted fw-bold text-uppercase tracking-wide">Veces Agendado</small>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="stat-card">
                    <div class="text-success mb-2"><i class="fas fa-check-double fa-2x"></i></div>
                    <h2 class="text-dark fw-bold mb-0">{{ servicio.total_completado }}</h2>
                    <small class="text-muted fw-bold text-uppercase tracking-wide">Completados</small>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="stat-card border-brand">
                    <div class="brand-accent mb-2"><i class="fas fa-hand-holding-usd fa-2x"></i></div>
                    <h2 class="text-dark fw-bold mb-0">${{ servicio.ingresos_generados }}</h2>
                    <small class="brand-accent fw-bold text-uppercase tracking-wide">Ingresos Totales</small>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-history text-secondary me-2"></i> Últimas Citas
              </h5>
            </div>
            <div class="card-body p-0">
              <div v-if="servicio.ultimas_agendas && servicio.ultimas_agendas.length" class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th class="ps-4 text-muted small fw-bold text-uppercase tracking-wide">ID</th>
                      <th class="text-muted small fw-bold text-uppercase tracking-wide">Fecha</th>
                      <th class="text-muted small fw-bold text-uppercase tracking-wide">Cliente</th>
                      <th class="text-muted small fw-bold text-uppercase tracking-wide">Empleado</th>
                      <th class="text-center text-muted small fw-bold text-uppercase tracking-wide">Estado</th>
                      <th class="text-end text-muted small fw-bold text-uppercase tracking-wide">Precio</th>
                      <th class="text-center pe-4 text-muted small fw-bold text-uppercase tracking-wide">Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="agenda in servicio.ultimas_agendas" :key="agenda.id">
                      <td class="ps-4">
                        <span class="text-muted fw-semibold">#{{ agenda.id }}</span>
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <i class="far fa-calendar-alt text-muted me-2"></i>
                          <small class="fw-medium">{{ agenda.fecha }}</small>
                        </div>
                      </td>
                      <td>
                        <span class="fw-medium text-dark">{{ agenda.cliente }}</span>
                      </td>
                      <td>
                        <span class="text-muted">{{ agenda.empleado }}</span>
                      </td>
                      <td class="text-center">
                        <span
                          class="badge rounded-pill fw-medium"
                          :class="getEstadoClass(agenda.estado)"
                        >
                          {{ agenda.estado }}
                        </span>
                      </td>
                      <td class="text-end fw-bold text-success">
                        ${{ agenda.precio }}
                      </td>
                      <td class="text-center pe-4">
                        <Link
                          :href="`/agendas/${agenda.id}`"
                          class="btn btn-sm btn-light border text-primary rounded-circle action-btn"
                          title="Ver detalle"
                        >
                          <i class="fas fa-eye"></i>
                        </Link>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div v-else class="text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                  <i class="fas fa-calendar-times fa-2x text-muted"></i>
                </div>
                <h6 class="fw-bold text-dark">Sin registros</h6>
                <p class="text-muted mb-0 small">Este servicio aún no ha sido agendado.</p>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
              <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0 d-flex align-items-center">
                  <div class="bg-light p-3 rounded-3 me-3 text-secondary">
                    <i class="fas fa-calendar-plus fa-lg"></i>
                  </div>
                  <div>
                    <small class="text-muted fw-bold text-uppercase tracking-wide d-block mb-1">Fecha de Creación</small>
                    <span class="fw-semibold text-dark">{{ servicio.created_at }}</span>
                  </div>
                </div>
                <div class="col-md-6 d-flex align-items-center border-md-start ps-md-4">
                  <div class="bg-light p-3 rounded-3 me-3 text-secondary">
                    <i class="fas fa-fingerprint fa-lg"></i>
                  </div>
                  <div>
                    <small class="text-muted fw-bold text-uppercase tracking-wide d-block mb-1">ID del Sistema</small>
                    <span class="fw-semibold text-dark">#{{ servicio.id }}</span>
                  </div>
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

.bg-brand-light {
  background-color: rgba(216, 75, 114, 0.1);
}

.border-brand {
  border: 2px solid rgba(216, 75, 114, 0.2) !important;
  background-color: rgba(216, 75, 114, 0.02) !important;
}

/* Tipografía de apoyo */
.tracking-wide {
  letter-spacing: 0.05em;
}

/* Tarjetas y UI Components */
.card {
  transition: box-shadow 0.2s ease;
}

.card:hover {
  box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
}

.service-icon {
  width: 110px;
  height: 110px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-card {
  padding: 1.5rem;
  border-radius: 1rem;
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
  transition: transform 0.2s;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.stat-card:hover {
  transform: translateY(-3px);
}

.badge {
  font-size: 0.75rem;
}

/* Tablas */
.table th {
  font-size: 0.75rem;
  border-bottom: 2px solid #e9ecef;
}

.table tbody tr {
  transition: all 0.2s;
}

.table tbody tr:hover {
  background-color: #f8f9fa;
}

/* Botones circulares para tabla */
.action-btn {
  width: 32px;
  height: 32px;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.action-btn:hover {
  background-color: #e9ecef;
  transform: scale(1.1);
}

@media (min-width: 768px) {
  .border-md-start {
    border-left: 1px solid #e9ecef !important;
  }
}
</style>
