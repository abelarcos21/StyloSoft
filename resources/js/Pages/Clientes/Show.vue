<script setup>
import { router } from '@inertiajs/vue3'
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
          Swal.fire("¡Listo!", `Se agregaron ${result.value} puntos`, "success")
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
    <div class="container-fluid px-3">

      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 text-primary fw-bold">
          <i class="fas fa-user-circle me-2"></i> Perfil del Cliente
        </h1>
        <div class="btn-group">
          <a href="/clientes" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Volver
          </a>
          <a :href="`/clientes/${cliente.id}/edit`" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i> Editar
          </a>
          <a :href="`/agendas/create?cliente_id=${cliente.id}`" class="btn btn-success">
            <i class="fas fa-calendar-plus me-2"></i> Nueva Cita
          </a>
        </div>
      </div>

      <div class="row g-3">

        <!-- Información del Cliente -->
        <div class="col-lg-4">
          
          <!-- Datos Personales -->
          <div class="card shadow-sm border-0 mb-3">
            <div class="card-body text-center">
              <div class="avatar-large bg-primary text-white mx-auto mb-3">
                {{ cliente.nombre.charAt(0) }}{{ cliente.apellido.charAt(0) }}
              </div>
              <h4 class="mb-1">{{ cliente.nombre_completo }}</h4>
              <p class="text-muted mb-3">Cliente #{{ cliente.id }}</p>

              <!-- Puntos de Fidelidad -->
              <div class="d-flex justify-content-center gap-3 mb-3">
                <div class="text-center">
                  <div class="badge bg-warning text-dark px-4 py-2 fs-5">
                    <i class="fas fa-star me-2"></i>{{ cliente.puntos_fidelidad }}
                  </div>
                  <small class="d-block text-muted mt-1">Puntos</small>
                </div>
              </div>

              <button @click="agregarPuntos" class="btn btn-sm btn-outline-warning w-100">
                <i class="fas fa-plus me-2"></i> Agregar Puntos
              </button>
            </div>
          </div>

          <!-- Estadísticas -->
          <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-primary text-white">
              <h6 class="mb-0">
                <i class="fas fa-chart-bar me-2"></i> Estadísticas
              </h6>
            </div>
            <div class="card-body">
              <div class="stat-item mb-3">
                <div class="d-flex justify-content-between align-items-center">
                  <span class="text-muted">Total Citas</span>
                  <strong class="fs-5 text-primary">{{ cliente.total_citas }}</strong>
                </div>
              </div>
              <div class="stat-item mb-3">
                <div class="d-flex justify-content-between align-items-center">
                  <span class="text-muted">Completadas</span>
                  <strong class="fs-5 text-success">{{ cliente.citas_completadas }}</strong>
                </div>
              </div>
              <div class="stat-item">
                <div class="d-flex justify-content-between align-items-center">
                  <span class="text-muted">Total Gastado</span>
                  <strong class="fs-5 text-info">${{ cliente.total_gastado }}</strong>
                </div>
              </div>
            </div>
          </div>

          <!-- Información de Contacto -->
          <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">
              <h6 class="mb-0">
                <i class="fas fa-address-book me-2"></i> Contacto
              </h6>
            </div>
            <div class="card-body">
              <div class="mb-3" v-if="cliente.telefono">
                <label class="text-muted small fw-semibold d-block">TELÉFONO</label>
                <a :href="`tel:${cliente.telefono}`" class="text-decoration-none">
                  <i class="fas fa-phone text-success me-2"></i>{{ cliente.telefono }}
                </a>
              </div>

              <div class="mb-3" v-if="cliente.email">
                <label class="text-muted small fw-semibold d-block">EMAIL</label>
                <a :href="`mailto:${cliente.email}`" class="text-decoration-none">
                  <i class="fas fa-envelope text-primary me-2"></i>{{ cliente.email }}
                </a>
              </div>

              <div class="mb-3" v-if="cliente.direccion">
                <label class="text-muted small fw-semibold d-block">DIRECCIÓN</label>
                <p class="mb-0">
                  <i class="fas fa-map-marker-alt text-danger me-2"></i>{{ cliente.direccion }}
                </p>
              </div>

              <div class="mb-3" v-if="cliente.fecha_nacimiento">
                <label class="text-muted small fw-semibold d-block">FECHA DE NACIMIENTO</label>
                <p class="mb-0">
                  <i class="fas fa-birthday-cake text-info me-2"></i>
                  {{ cliente.fecha_nacimiento }}
                  <span v-if="cliente.edad" class="text-muted ms-2">({{ cliente.edad }} años)</span>
                </p>
              </div>

              <div class="mb-3" v-if="cliente.genero">
                <label class="text-muted small fw-semibold d-block">GÉNERO</label>
                <span class="badge bg-secondary">
                  {{ cliente.genero.charAt(0).toUpperCase() + cliente.genero.slice(1) }}
                </span>
              </div>

              <div v-if="cliente.ultima_visita">
                <label class="text-muted small fw-semibold d-block">ÚLTIMA VISITA</label>
                <p class="mb-0">
                  <i class="fas fa-clock text-warning me-2"></i>{{ cliente.ultima_visita }}
                </p>
              </div>
            </div>
          </div>

        </div>

        <!-- Historial y Notas -->
        <div class="col-lg-8">

          <!-- Notas -->
          <div class="card shadow-sm border-0 mb-3" v-if="cliente.notas">
            <div class="card-header bg-secondary text-white">
              <h6 class="mb-0">
                <i class="fas fa-sticky-note me-2"></i> Notas
              </h6>
            </div>
            <div class="card-body">
              <p class="mb-0">{{ cliente.notas }}</p>
            </div>
          </div>

          <!-- Historial de Citas -->
          <div class="card shadow-sm border-0">
            <div class="card-header bg-info text-white">
              <h6 class="mb-0">
                <i class="fas fa-history me-2"></i> Historial de Citas
              </h6>
            </div>
            <div class="card-body p-0">
              <div v-if="cliente.ultimas_citas.length" class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Fecha</th>
                      <th>Empleado</th>
                      <th>Servicios</th>
                      <th class="text-center">Estado</th>
                      <th class="text-end">Total</th>
                      <th class="text-center">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="cita in cliente.ultimas_citas" :key="cita.id">
                      <td>
                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                        {{ cita.fecha }}
                      </td>
                      <td>
                        <i class="fas fa-user-tie text-success me-2"></i>
                        {{ cita.empleado }}
                      </td>
                      <td>
                        <small>{{ cita.servicios }}</small>
                      </td>
                      <td class="text-center">
                        <span 
                          class="badge px-2 py-1" 
                          :class="getEstadoClass(cita.estado)"
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
                  <small class="text-muted">Cliente desde:</small>
                  <p class="fw-semibold">{{ cliente.created_at }}</p>
                </div>
                <div class="col-md-6">
                  <small class="text-muted">Marketing:</small>
                  <p>
                    <span 
                      class="badge" 
                      :class="cliente.acepta_marketing ? 'bg-success' : 'bg-secondary'"
                    >
                      {{ cliente.acepta_marketing ? 'Acepta Promociones' : 'No acepta promociones' }}
                    </span>
                  </p>
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
  font-size: 0.8rem;
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