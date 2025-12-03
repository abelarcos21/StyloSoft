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
  <AdminLayout>
    <div class="container-fluid px-3">

      <!-- Bienvenida -->
      <div class="row mb-4">
        <div class="col-12">
          <h2 class="h4 text-primary fw-bold mb-1">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
          </h2>
          <p class="text-muted mb-0">
            Bienvenido, <strong>{{ user?.name || 'Usuario' }}</strong> 👋
          </p>
        </div>
      </div>

      <!-- Info Boxes -->
      <div class="row g-3 mb-4">
        
        <!-- Turnos Hoy -->
        <div class="col-md-3 col-sm-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <p class="text-muted mb-1 small">Turnos Hoy</p>
                  <h3 class="mb-0 fw-bold">{{ turnosHoy || 0 }}</h3>
                  <small class="text-success">
                    <i class="fas fa-calendar-check me-1"></i>Agendadas
                  </small>
                </div>
                <div class="info-icon bg-pink bg-opacity-10 text-primary">
                  <i class="fas fa-calendar-alt fa-2x"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tickets Pendientes -->
        <div class="col-md-3 col-sm-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <p class="text-muted mb-1 small">Tickets Pendientes</p>
                  <h3 class="mb-0 fw-bold">{{ ticketsPendientes || 0 }}</h3>
                  <small class="text-warning">
                    <i class="fas fa-exclamation-circle me-1"></i>Por cobrar
                  </small>
                </div>
                <div class="info-icon bg-pink bg-opacity-10 text-warning">
                  <i class="fas fa-receipt fa-2x"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Ingresos Hoy -->
        <div class="col-md-3 col-sm-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <p class="text-muted mb-1 small">Ingresos Hoy</p>
                  <h3 class="mb-0 fw-bold">{{ formatCurrency(ingresosHoy || 0) }}</h3>
                  <small class="text-success">
                    <i class="fas fa-arrow-up me-1"></i>Ventas del día
                  </small>
                </div>
                <div class="info-icon bg-pink bg-opacity-10 text-success">
                  <i class="fas fa-dollar-sign fa-2x"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Ingresos del Mes -->
        <div class="col-md-3 col-sm-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <p class="text-muted mb-1 small">Ingresos del Mes</p>
                  <h3 class="mb-0 fw-bold">{{ formatCurrency(ingresosMes || 0) }}</h3>
                  <small class="text-info">
                    <i class="fas fa-chart-line me-1"></i>Mensual
                  </small>
                </div>
                <div class="info-icon bg-pink bg-opacity-10 text-info">
                  <i class="fas fa-coins fa-2x"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <div class="row g-3">

        <!-- Últimas Agendas -->
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-pink text-white">
              <h6 class="mb-0">
                <i class="fas fa-calendar-alt me-2"></i>Últimas Agendas
              </h6>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Cliente</th>
                      <th class="text-center">Fecha/Hora</th>
                      <th class="text-center">Estado</th>
                      <th class="text-center">Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="agenda in ultimasAgendas" :key="agenda.id">
                      <td>
                        <div>
                          <strong class="d-block">{{ agenda.cliente }}</strong>
                          <small class="text-muted">{{ agenda.empleado }}</small>
                        </div>
                      </td>
                      <td class="text-center">
                        <small class="d-block">{{ agenda.fecha }}</small>
                        <small class="text-muted">{{ agenda.hora }}</small>
                      </td>
                      <td class="text-center">
                        <span 
                          class="badge"
                          :class="getEstadoBadgeClass(agenda.estado)"
                        >
                          {{ agenda.estado_texto }}
                        </span>
                      </td>
                      <td class="text-center">
                        <strong class="text-success">{{ formatCurrency(agenda.total) }}</strong>
                      </td>
                    </tr>
                    <tr v-if="!ultimasAgendas || ultimasAgendas.length === 0">
                      <td colspan="4" class="text-center text-muted py-4">
                        <i class="fas fa-calendar-times fa-2x mb-2 d-block"></i>
                        No hay agendas recientes
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer bg-light">
              <a href="/agendas" class="btn btn-sm btn-primary">
                <i class="fas fa-eye me-2"></i>Ver todas las agendas
              </a>
            </div>
          </div>
        </div>

        <!-- Servicios Más Vendidos -->
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-pink text-white">
              <h6 class="mb-0">
                <i class="fas fa-cut me-2"></i>Servicios Más Vendidos
              </h6>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Servicio</th>
                      <th class="text-center">Cantidad</th>
                      <th class="text-end">Total Vendido</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(servicio, index) in serviciosMasVendidos" :key="index">
                      <td>
                        <div class="d-flex align-items-center">
                          <span class="badge bg-success me-2">{{ index + 1 }}</span>
                          <div>
                            <strong>{{ servicio.nombre }}</strong>
                            <small class="d-block text-muted">
                              {{ formatCurrency(servicio.precio) }} c/u
                            </small>
                          </div>
                        </div>
                      </td>
                      <td class="text-center">
                        <span class="badge bg-primary">{{ servicio.cantidad }}</span>
                      </td>
                      <td class="text-end">
                        <strong class="text-success">{{ formatCurrency(servicio.total) }}</strong>
                      </td>
                    </tr>
                    <tr v-if="!serviciosMasVendidos || serviciosMasVendidos.length === 0">
                      <td colspan="3" class="text-center text-muted py-4">
                        <i class="fas fa-cut fa-2x mb-2 d-block"></i>
                        No hay servicios vendidos
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer bg-light">
              <a href="/servicios" class="btn btn-sm btn-success">
                <i class="fas fa-list me-2"></i>Ver todos los servicios
              </a>
            </div>
          </div>
        </div>

        <!-- Productos Más Vendidos -->
        <div class="col-lg-12">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-pink">
              <h6 class="mb-0">
                <i class="fas fa-box me-2"></i>Productos Más Vendidos
              </h6>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th style="width: 5%">#</th>
                      <th style="width: 40%">Producto</th>
                      <th class="text-center" style="width: 15%">Cantidad</th>
                      <th class="text-end" style="width: 15%">Precio Unit.</th>
                      <th class="text-end" style="width: 15%">Total Vendido</th>
                      <th class="text-center" style="width: 10%">Stock</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(producto, index) in productosMasVendidos" :key="index">
                      <td class="text-center">
                        <span class="badge bg-warning text-dark">{{ index + 1 }}</span>
                      </td>
                      <td>
                        <strong>{{ producto.nombre }}</strong>
                        <small class="d-block text-muted" v-if="producto.descripcion">
                          {{ producto.descripcion }}
                        </small>
                      </td>
                      <td class="text-center">
                        <span class="badge bg-primary">{{ producto.cantidad }} vendidos</span>
                      </td>
                      <td class="text-end">
                        <span class="text-muted">{{ formatCurrency(producto.precio) }}</span>
                      </td>
                      <td class="text-end">
                        <strong class="text-success">{{ formatCurrency(producto.total) }}</strong>
                      </td>
                      <td class="text-center">
                        <span 
                          class="badge"
                          :class="producto.stock > 10 ? 'bg-success' : producto.stock > 0 ? 'bg-warning text-dark' : 'bg-danger'"
                        >
                          {{ producto.stock }}
                        </span>
                      </td>
                    </tr>
                    <tr v-if="!productosMasVendidos || productosMasVendidos.length === 0">
                      <td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-box-open fa-2x mb-2 d-block"></i>
                        No hay productos vendidos
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer bg-light">
              <a href="/productos" class="btn btn-sm btn-warning text-dark">
                <i class="fas fa-list me-2"></i>Ver todos los productos
              </a>
            </div>
          </div>
        </div>

      </div>

    </div>
  </AdminLayout>
</template>

<style scoped>
.info-icon {
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
}

.card {
  transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
}

.table tbody tr {
  transition: background-color 0.2s;
}

.table tbody tr:hover {
  background-color: rgba(0, 123, 255, 0.05);
}

.badge {
  font-size: 0.75rem;
  font-weight: 600;
  padding: 0.35rem 0.65rem;
}

h3 {
  color: #333;
}

small {
  font-size: 0.8rem;
}
</style>