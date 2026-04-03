<script setup>
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  cliente: Object
})

function agregarPuntos() {
  Swal.fire({
    title: "Agregar Puntos de Fidelidad",
    input: "number",
    inputLabel: "¿Cuántos puntos deseas agregar?",
    inputPlaceholder: "Ej: 50",
    inputAttributes: {
      min: 1,
      max: 1000
    },
    showCancelButton: true,
    confirmButtonColor: '#d84b72',
    confirmButtonText: "Agregar",
    cancelButtonText: "Cancelar",
    inputValidator: (value) => {
      if (!value || value < 1) {
        return "Debes ingresar al menos 1 punto"
      }
    }
  }).then(result => {
    if (result.isConfirmed) {
      router.post(`/clientes/${props.cliente.id}/puntos`, {
        puntos: parseInt(result.value)
      }, {
        onSuccess: () => {
          Swal.fire({
            title: "¡Listo!",
            text: `Se agregaron ${result.value} puntos`,
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
  <AdminLayout title="Detalle de Cliente">
    <div class="container-fluid px-0 px-md-3">

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h1 class="h4 brand-accent fw-bold mb-0">
          <i class="fas fa-user-circle me-2"></i> Perfil del Cliente
        </h1>
        <div class="d-flex flex-wrap gap-2">
          <Link href="/clientes" class="btn btn-light rounded-pill px-3 shadow-sm text-muted fw-medium">
            <i class="fas fa-arrow-left me-1"></i> Volver
          </Link>
          <Link :href="`/clientes/${cliente.id}/edit`" class="btn btn-light border rounded-pill px-3 shadow-sm text-muted fw-medium">
            <i class="fas fa-edit me-1"></i> Editar
          </Link>
          <Link :href="`/agendas/create?cliente_id=${cliente.id}`" class="btn btn-brand rounded-pill px-3 shadow-sm fw-medium">
            <i class="fas fa-calendar-plus me-1"></i> Nueva Cita
          </Link>
        </div>
      </div>

      <div class="row g-4">

        <div class="col-lg-4">

          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body text-center p-4">
              <div class="avatar-large text-white mx-auto mb-3 shadow-sm" style="background-color: #d84b72;">
                {{ cliente.nombre.charAt(0) }}{{ cliente.apellido.charAt(0) }}
              </div>
              <h4 class="mb-1 fw-bold text-dark">{{ cliente.nombre_completo }}</h4>
              <p class="text-muted small mb-4 tracking-wide text-uppercase">Cliente #{{ cliente.id }}</p>

              <div class="bg-brand-subtle rounded-4 p-3 mb-4 border border-white shadow-sm">
                <div class="text-center">
                  <div class="fs-2 fw-bold brand-accent">
                    <i class="fas fa-star me-2 fs-4"></i>{{ cliente.puntos_fidelidad }}
                  </div>
                  <small class="d-block text-muted fw-semibold tracking-wide text-uppercase mt-1">Puntos Acumulados</small>
                </div>
              </div>

              <button @click="agregarPuntos" class="btn btn-outline-brand rounded-pill w-100 fw-medium">
                <i class="fas fa-plus me-2"></i> Agregar Puntos
              </button>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-chart-pie brand-accent me-2"></i> Estadísticas
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="stat-item mb-3">
                <div class="d-flex justify-content-between align-items-center">
                  <span class="text-muted fw-medium"><i class="fas fa-calendar-check me-2 text-secondary"></i>Total Citas</span>
                  <strong class="fs-5 text-dark">{{ cliente.total_citas }}</strong>
                </div>
              </div>
              <div class="stat-item mb-3">
                <div class="d-flex justify-content-between align-items-center">
                  <span class="text-muted fw-medium"><i class="fas fa-check-circle me-2 text-success"></i>Completadas</span>
                  <strong class="fs-5 text-success">{{ cliente.citas_completadas }}</strong>
                </div>
              </div>
              <div class="stat-item">
                <div class="d-flex justify-content-between align-items-center">
                  <span class="text-muted fw-medium"><i class="fas fa-wallet me-2 text-info"></i>Total Gastado</span>
                  <strong class="fs-5 text-dark">${{ cliente.total_gastado }}</strong>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-address-book brand-accent me-2"></i> Contacto
              </h5>
            </div>
            <div class="card-body p-4">

              <div class="mb-3" v-if="cliente.telefono">
                <label class="text-muted small fw-bold text-uppercase tracking-wide mb-1 d-block">Teléfono</label>
                <a :href="`tel:${cliente.telefono}`" class="text-decoration-none text-dark fw-medium d-flex align-items-center">
                  <div class="icon-box bg-light text-secondary rounded-circle me-2 d-flex align-items-center justify-content-center">
                    <i class="fas fa-phone"></i>
                  </div>
                  {{ cliente.telefono }}
                </a>
              </div>

              <div class="mb-3" v-if="cliente.email">
                <label class="text-muted small fw-bold text-uppercase tracking-wide mb-1 d-block">Email</label>
                <a :href="`mailto:${cliente.email}`" class="text-decoration-none text-dark fw-medium d-flex align-items-center">
                  <div class="icon-box bg-light text-secondary rounded-circle me-2 d-flex align-items-center justify-content-center">
                    <i class="fas fa-envelope"></i>
                  </div>
                  {{ cliente.email }}
                </a>
              </div>

              <div class="mb-3" v-if="cliente.direccion">
                <label class="text-muted small fw-bold text-uppercase tracking-wide mb-1 d-block">Dirección</label>
                <p class="mb-0 text-dark fw-medium d-flex align-items-center">
                  <div class="icon-box bg-light text-secondary rounded-circle me-2 d-flex align-items-center justify-content-center flex-shrink-0">
                    <i class="fas fa-map-marker-alt"></i>
                  </div>
                  {{ cliente.direccion }}
                </p>
              </div>

              <div class="mb-3" v-if="cliente.fecha_nacimiento">
                <label class="text-muted small fw-bold text-uppercase tracking-wide mb-1 d-block">Nacimiento</label>
                <p class="mb-0 text-dark fw-medium d-flex align-items-center">
                  <div class="icon-box bg-light text-secondary rounded-circle me-2 d-flex align-items-center justify-content-center">
                    <i class="fas fa-birthday-cake"></i>
                  </div>
                  {{ cliente.fecha_nacimiento }}
                  <span v-if="cliente.edad" class="text-muted ms-1 fw-normal">({{ cliente.edad }} años)</span>
                </p>
              </div>

              <div class="mb-3" v-if="cliente.genero">
                <label class="text-muted small fw-bold text-uppercase tracking-wide mb-1 d-block">Género</label>
                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-medium">
                  {{ cliente.genero.charAt(0).toUpperCase() + cliente.genero.slice(1) }}
                </span>
              </div>

              <div v-if="cliente.ultima_visita">
                <label class="text-muted small fw-bold text-uppercase tracking-wide mb-1 d-block">Última Visita</label>
                <p class="mb-0 text-dark fw-medium d-flex align-items-center">
                  <div class="icon-box bg-light text-secondary rounded-circle me-2 d-flex align-items-center justify-content-center">
                    <i class="fas fa-clock"></i>
                  </div>
                  {{ cliente.ultima_visita }}
                </p>
              </div>

            </div>
          </div>

        </div>

        <div class="col-lg-8">

          <div class="card shadow-sm border-0 rounded-4 mb-4" v-if="cliente.notas">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-sticky-note text-warning me-2"></i> Notas y Observaciones
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="bg-light rounded-4 p-3 border-start border-4 border-warning">
                <p class="mb-0 text-dark">{{ cliente.notas }}</p>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-history brand-accent me-2"></i> Historial de Citas
              </h5>
            </div>
            <div class="card-body p-4">
              <div v-if="cliente.ultimas_citas.length" class="table-responsive rounded-4 border">
                <table class="table table-hover align-middle mb-0">
                  <thead class="bg-light">
                    <tr>
                      <th class="text-muted small fw-bold text-uppercase tracking-wide border-0 py-3 ps-3">Fecha</th>
                      <th class="text-muted small fw-bold text-uppercase tracking-wide border-0 py-3">Empleado</th>
                      <th class="text-muted small fw-bold text-uppercase tracking-wide border-0 py-3">Servicios</th>
                      <th class="text-muted small fw-bold text-uppercase tracking-wide border-0 py-3 text-center">Estado</th>
                      <th class="text-muted small fw-bold text-uppercase tracking-wide border-0 py-3 text-end">Total</th>
                      <th class="text-muted small fw-bold text-uppercase tracking-wide border-0 py-3 text-center pe-3">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="cita in cliente.ultimas_citas" :key="cita.id">
                      <td class="ps-3 fw-medium">
                        <i class="fas fa-calendar-alt text-secondary me-2"></i>
                        {{ cita.fecha }}
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <i class="fas fa-user-tie text-secondary me-2"></i>
                          <span>{{ cita.empleado }}</span>
                        </div>
                      </td>
                      <td>
                        <span class="text-muted small">{{ cita.servicios }}</span>
                      </td>
                      <td class="text-center">
                        <span
                          class="badge rounded-pill px-3 py-2 fw-medium"
                          :class="getEstadoClass(cita.estado)"
                        >
                          {{ cita.estado }}
                        </span>
                      </td>
                      <td class="text-end fw-bold text-dark">${{ cita.total }}</td>
                      <td class="text-center pe-3">
                        <Link
                          :href="`/agendas/${cita.id}`"
                          class="btn btn-sm btn-light rounded-circle shadow-sm"
                          title="Ver detalles"
                        >
                          <i class="fas fa-eye brand-accent"></i>
                        </Link>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div v-else class="text-center py-5 bg-light rounded-4 border border-dashed">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3 opacity-50"></i>
                <h6 class="text-dark fw-bold">Sin historial de citas</h6>
                <p class="text-muted mb-0 small">Este cliente aún no ha registrado ninguna cita en el sistema.</p>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
              <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-info-circle text-info me-2"></i> Información Adicional
              </h5>
            </div>
            <div class="card-body p-4">
              <div class="row g-4">
                <div class="col-md-6">
                  <div class="d-flex align-items-start">
                    <div class="icon-box bg-light text-secondary rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                      <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                      <label class="text-muted small fw-bold text-uppercase tracking-wide d-block mb-1">Cliente desde</label>
                      <span class="fw-medium text-dark">{{ cliente.created_at }}</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="d-flex align-items-start">
                    <div class="icon-box bg-light text-secondary rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                      <i class="fas fa-bullhorn"></i>
                    </div>
                    <div>
                      <label class="text-muted small fw-bold text-uppercase tracking-wide d-block mb-1">Marketing</label>
                      <span
                        class="badge rounded-pill px-3 py-2 fw-medium"
                        :class="cliente.acepta_marketing ? 'bg-success text-white' : 'bg-light text-muted border'"
                      >
                        {{ cliente.acepta_marketing ? 'Acepta Promociones' : 'No acepta promociones' }}
                      </span>
                    </div>
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

.btn-outline-brand {
  color: #d84b72;
  border-color: #d84b72;
  transition: all 0.3s ease;
}

.btn-outline-brand:hover {
  background-color: #d84b72;
  color: white;
}

/* Tipografía de apoyo */
.tracking-wide {
  letter-spacing: 0.05em;
}

/* Elementos visuales */
.avatar-large {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 2.2rem;
  letter-spacing: 2px;
}

.icon-box {
  width: 35px;
  height: 35px;
  font-size: 0.9rem;
}

/* Cajas de Estadísticas */
.stat-item {
  padding: 1rem;
  border-radius: 1rem;
  background-color: #f8f9fa;
  border: 1px solid #f1f2f4;
  transition: background-color 0.2s ease;
}

.stat-item:hover {
  background-color: #eef0f3;
}

/* Tablas */
.table > :not(caption) > * > * {
  padding: 1rem 0.75rem;
}

.table tbody tr {
  transition: background-color 0.2s;
}

.border-dashed {
  border-style: dashed !important;
  border-width: 2px !important;
  border-color: #dee2e6 !important;
}

/* Hover de tarjetas */
.card {
  transition: box-shadow 0.2s ease;
}
.card:hover {
  box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
}
</style>
