<script setup>
import AdminLayout from '../Layouts/AdminLayout.vue'
import { usePage } from '@inertiajs/vue3'

const { props } = usePage()
const user = props.auth?.user || {}

// Props desde el controlador
defineProps({
  turnosHoy: Number,
  ticketsPendientes: Number,
  ingresosHoy: Number,
  ingresosMes: Number,
  ultimasAgendas: Array,
  serviciosMasVendidos: Array,
  productosMasVendidos: Array,
})

function formatCurrency(value) {
  return new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN'
  }).format(value)
}

function getEstadoBadgeClass(estado) {
  // Mantenemos colores semánticos solo para estados, para indicar acciones claras
  const classes = {
    'pendiente': 'bg-warning text-dark',
    'confirmada': 'bg-primary',
    'en_proceso': 'bg-dark',
    'completada': 'bg-success',
    'cancelada': 'bg-danger border',
    'no_asistio': 'bg-secondary'
  }
  return classes[estado] || 'bg-light text-dark border'
}
</script>

<template>
  <AdminLayout>
    <div class="container-fluid px-3 py-4">

      <div class="row mb-4">
        <div class="col-12">
          <h2 class="h4 text-dark fw-bold mb-1">
            Dashboard
          </h2>
          <p class="text-muted mb-0">
            Bienvenido de nuevo, <strong>{{ user?.name || 'Usuario' }}</strong> 👋
          </p>
        </div>
      </div>

      <div class="row g-4 mb-5">

        <div class="col-md-3 col-sm-6">
          <div class="card border-0 shadow-sm h-100 summary-card">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <p class="text-muted mb-1 text-uppercase custom-label">Turnos Hoy</p>
                  <h3 class="mb-0 fw-bold text-dark">{{ turnosHoy || 0 }}</h3>
                </div>
                <div class="info-icon bg-light text-dark">
                  <i class="fas fa-calendar-check fs-4"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3 col-sm-6">
          <div class="card border-0 shadow-sm h-100 summary-card">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <p class="text-muted mb-1 text-uppercase custom-label">Por Cobrar</p>
                  <h3 class="mb-0 fw-bold text-dark">{{ ticketsPendientes || 0 }}</h3>
                </div>
                <div class="info-icon bg-light text-dark">
                  <i class="fas fa-receipt fs-4"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3 col-sm-6">
          <div class="card border-0 shadow-sm h-100 summary-card">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <p class="text-muted mb-1 text-uppercase custom-label">Ingresos Hoy</p>
                  <h3 class="mb-0 fw-bold text-dark">{{ formatCurrency(ingresosHoy || 0) }}</h3>
                </div>
                <div class="info-icon bg-light text-success">
                  <i class="fas fa-dollar-sign fs-4"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3 col-sm-6">
          <div class="card border-0 shadow-sm h-100 summary-card">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <p class="text-muted mb-1 text-uppercase custom-label">Mes Actual</p>
                  <h3 class="mb-0 fw-bold text-dark">{{ formatCurrency(ingresosMes || 0) }}</h3>
                </div>
                <div class="info-icon bg-light text-dark">
                  <i class="fas fa-chart-line fs-4"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <div class="row g-4">

        <div class="col-lg-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
              <h6 class="mb-0 fw-bold text-dark">
                Últimas Agendas
              </h6>
              <a href="/agendas" class="btn btn-sm btn-light border">Ver todas</a>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-borderless table-hover mb-0 align-middle custom-table">
                  <thead class="border-bottom">
                    <tr>
                      <th class="text-muted fw-normal px-4 py-3">Cliente</th>
                      <th class="text-muted fw-normal text-center py-3">Fecha</th>
                      <th class="text-muted fw-normal text-center py-3">Estado</th>
                      <th class="text-muted fw-normal text-end px-4 py-3">Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="agenda in ultimasAgendas" :key="agenda.id" class="border-bottom">
                      <td class="px-4 py-3">
                        <strong class="d-block text-dark">{{ agenda.cliente }}</strong>
                        <small class="text-muted">{{ agenda.empleado }}</small>
                      </td>
                      <td class="text-center py-3">
                        <span class="d-block text-dark">{{ agenda.fecha }}</span>
                        <small class="text-muted">{{ agenda.hora }}</small>
                      </td>
                      <td class="text-center py-3">
                        <span class="badge rounded-pill" :class="getEstadoBadgeClass(agenda.estado)">
                          {{ agenda.estado_texto }}
                        </span>
                      </td>
                      <td class="text-end px-4 py-3">
                        <strong class="text-dark">{{ formatCurrency(agenda.total) }}</strong>
                      </td>
                    </tr>
                    <tr v-if="!ultimasAgendas || ultimasAgendas.length === 0">
                      <td colspan="4" class="text-center text-muted py-5">
                        <i class="fas fa-calendar-times fs-2 mb-2 d-block opacity-50"></i>
                        No hay agendas recientes
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
              <h6 class="mb-0 fw-bold text-dark">
                Servicios Top
              </h6>
              <a href="/servicios" class="btn btn-sm btn-light border">Catálogo</a>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-borderless table-hover mb-0 align-middle custom-table">
                  <thead class="border-bottom">
                    <tr>
                      <th class="text-muted fw-normal px-4 py-3">Servicio</th>
                      <th class="text-muted fw-normal text-center py-3">Cant.</th>
                      <th class="text-muted fw-normal text-end px-4 py-3">Ingreso</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(servicio, index) in serviciosMasVendidos" :key="index" class="border-bottom">
                      <td class="px-4 py-3">
                        <div class="d-flex align-items-center">
                          <span class="text-muted me-3 fw-bold">#{{ index + 1 }}</span>
                          <div>
                            <strong class="text-dark d-block">{{ servicio.nombre }}</strong>
                            <small class="text-muted">{{ formatCurrency(servicio.precio) }}</small>
                          </div>
                        </div>
                      </td>
                      <td class="text-center py-3">
                        <span class="badge bg-light text-dark border">{{ servicio.cantidad }}</span>
                      </td>
                      <td class="text-end px-4 py-3">
                        <strong class="text-dark">{{ formatCurrency(servicio.total) }}</strong>
                      </td>
                    </tr>
                    <tr v-if="!serviciosMasVendidos || serviciosMasVendidos.length === 0">
                      <td colspan="3" class="text-center text-muted py-5">
                        <i class="fas fa-cut fs-2 mb-2 d-block opacity-50"></i>
                        Sin datos suficientes
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-12">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
              <h6 class="mb-0 fw-bold text-dark">
                Productos Más Vendidos
              </h6>
              <a href="/productos" class="btn btn-sm btn-light border">Ver inventario</a>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-borderless table-hover mb-0 align-middle custom-table">
                  <thead class="border-bottom">
                    <tr>
                      <th class="text-muted fw-normal px-4 py-3" style="width: 5%">#</th>
                      <th class="text-muted fw-normal py-3" style="width: 40%">Producto</th>
                      <th class="text-muted fw-normal text-center py-3" style="width: 15%">Vendidos</th>
                      <th class="text-muted fw-normal text-end py-3" style="width: 15%">Precio</th>
                      <th class="text-muted fw-normal text-end py-3" style="width: 15%">Total</th>
                      <th class="text-muted fw-normal text-center px-4 py-3" style="width: 10%">Stock</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(producto, index) in productosMasVendidos" :key="index" class="border-bottom">
                      <td class="text-center px-4 py-3 fw-bold text-muted">
                        {{ index + 1 }}
                      </td>
                      <td class="py-3">
                        <strong class="text-dark d-block">{{ producto.nombre }}</strong>
                        <small class="text-muted" v-if="producto.descripcion">{{ producto.descripcion }}</small>
                      </td>
                      <td class="text-center py-3">
                        <span class="badge bg-light text-dark border">{{ producto.cantidad }}</span>
                      </td>
                      <td class="text-end py-3 text-muted">
                        {{ formatCurrency(producto.precio) }}
                      </td>
                      <td class="text-end py-3">
                        <strong class="text-dark">{{ formatCurrency(producto.total) }}</strong>
                      </td>
                      <td class="text-center px-4 py-3">
                        <span
                          class="badge rounded-pill"
                          :class="producto.stock > 10 ? 'bg-success bg-opacity-10 text-success border border-success' : producto.stock > 0 ? 'bg-warning bg-opacity-10 text-warning border border-warning' : 'bg-danger text-white'"
                        >
                          {{ producto.stock }}
                        </span>
                      </td>
                    </tr>
                    <tr v-if="!productosMasVendidos || productosMasVendidos.length === 0">
                      <td colspan="6" class="text-center text-muted py-5">
                        <i class="fas fa-box-open fs-2 mb-2 d-block opacity-50"></i>
                        No hay productos vendidos
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
/* Iconos circulares limpios */
.info-icon {
  width: 52px;
  height: 52px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

/* Tarjetas globales */
.card {
  border-radius: 12px;
}

/* Efecto hover suave solo para las tarjetas de resumen */
.summary-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.summary-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
}

/* Tipografía de etiquetas pequeñas */
.custom-label {
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  font-weight: 600;
}

/* Tablas limpias */
.custom-table tbody tr:last-child {
  border-bottom: none !important;
}

.table-hover tbody tr:hover {
  background-color: #f8f9fa; /* Un gris muy sutil, en lugar del azul anterior */
}

/* Badges un poco más amplios */
.badge {
  font-size: 0.75rem;
  font-weight: 600;
  padding: 0.45rem 0.8rem;
}
</style>
