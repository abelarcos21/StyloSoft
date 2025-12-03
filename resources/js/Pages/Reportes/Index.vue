<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref, computed } from 'vue'
import axios from 'axios'

defineProps({
  empleados: Array
})

// Estado
const tabActiva = ref('ventas')
const cargando = ref(false)

// Filtros compartidos
const fechaInicio = ref(new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().slice(0, 10))
const fechaFin = ref(new Date().toISOString().slice(0, 10))

// Filtros específicos de ventas
const empleadoId = ref('')
const metodoPago = ref('')

// Datos de reportes
const reporteVentas = ref(null)
const reporteServicios = ref(null)
const reporteProductos = ref(null)
const reporteEmpleados = ref(null)

// Funciones
async function generarReporteVentas() {
  cargando.value = true
  try {
    const { data } = await axios.post('/reportes/ventas', {
      fecha_inicio: fechaInicio.value,
      fecha_fin: fechaFin.value,
      empleado_id: empleadoId.value || null,
      metodo_pago: metodoPago.value || null,
    })
    reporteVentas.value = data
  } catch (error) {
    console.error(error)
    alert('Error al generar el reporte')
  } finally {
    cargando.value = false
  }
}

async function generarReporteServicios() {
  cargando.value = true
  try {
    const { data } = await axios.post('/reportes/servicios', {
      fecha_inicio: fechaInicio.value,
      fecha_fin: fechaFin.value,
    })
    reporteServicios.value = data
  } catch (error) {
    console.error(error)
    alert('Error al generar el reporte')
  } finally {
    cargando.value = false
  }
}

async function generarReporteProductos() {
  cargando.value = true
  try {
    const { data } = await axios.post('/reportes/productos', {
      fecha_inicio: fechaInicio.value,
      fecha_fin: fechaFin.value,
    })
    reporteProductos.value = data
  } catch (error) {
    console.error(error)
    alert('Error al generar el reporte')
  } finally {
    cargando.value = false
  }
}

async function generarReporteEmpleados() {
  cargando.value = true
  try {
    const { data } = await axios.post('/reportes/empleados', {
      fecha_inicio: fechaInicio.value,
      fecha_fin: fechaFin.value,
    })
    reporteEmpleados.value = data
  } catch (error) {
    console.error(error)
    alert('Error al generar el reporte')
  } finally {
    cargando.value = false
  }
}

function formatCurrency(value) {
  const num = typeof value === 'string' ? parseFloat(value.replace(',', '')) : value
  return new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN'
  }).format(num)
}

function cambiarTab(tab) {
  tabActiva.value = tab
}
</script>

<template>
  <AdminLayout title="Reportes">
    <div class="container-fluid px-3">

      <!-- Header -->
      <div class="row mb-4">
        <div class="col-12">
          <h2 class="h4 text-pink fw-bold mb-1">
            <i class="fas fa-chart-line me-2"></i>Reportes y Estadísticas
          </h2>
          <p class="text-muted mb-0">Analiza el rendimiento de tu negocio</p>
        </div>
      </div>

      <!-- Tabs -->
      <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
          <button 
            class="nav-link" 
            :class="{ active: tabActiva === 'ventas' }"
            @click="cambiarTab('ventas')"
          >
            <i class="fas fa-cash-register me-2"></i>Ventas
          </button>
        </li>
        <li class="nav-item">
          <button 
            class="nav-link" 
            :class="{ active: tabActiva === 'servicios' }"
            @click="cambiarTab('servicios')"
          >
            <i class="fas fa-cut me-2"></i>Servicios
          </button>
        </li>
        <li class="nav-item">
          <button 
            class="nav-link" 
            :class="{ active: tabActiva === 'productos' }"
            @click="cambiarTab('productos')"
          >
            <i class="fas fa-box me-2"></i>Productos
          </button>
        </li>
        <li class="nav-item">
          <button 
            class="nav-link" 
            :class="{ active: tabActiva === 'empleados' }"
            @click="cambiarTab('empleados')"
          >
            <i class="fas fa-users me-2"></i>Empleados
          </button>
        </li>
      </ul>

      <!-- Reporte de Ventas -->
      <div v-show="tabActiva === 'ventas'">
        <div class="card shadow-sm border-0 mb-4">
          <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
              <i class="fas fa-filter me-2"></i>Filtros
            </h6>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label">Fecha Inicio</label>
                <input type="date" v-model="fechaInicio" class="form-control">
              </div>
              <div class="col-md-3">
                <label class="form-label">Fecha Fin</label>
                <input type="date" v-model="fechaFin" class="form-control">
              </div>
              <div class="col-md-3">
                <label class="form-label">Empleado</label>
                <select v-model="empleadoId" class="form-select">
                  <option value="">Todos</option>
                  <option v-for="emp in empleados" :key="emp.id" :value="emp.id">
                    {{ emp.text }}
                  </option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Método de Pago</label>
                <select v-model="metodoPago" class="form-select">
                  <option value="">Todos</option>
                  <option value="efectivo">Efectivo</option>
                  <option value="tarjeta">Tarjeta</option>
                  <option value="transferencia">Transferencia</option>
                  <option value="mixto">Mixto</option>
                </select>
              </div>
              <div class="col-12">
                <button 
                  @click="generarReporteVentas" 
                  class="btn btn-primary"
                  :disabled="cargando"
                >
                  <i class="fas fa-chart-bar me-2"></i>
                  {{ cargando ? 'Generando...' : 'Generar Reporte' }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Resultados Ventas -->
        <div v-if="reporteVentas">
          <!-- Resumen -->
          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <div class="d-flex justify-content-between">
                    <div>
                      <p class="text-muted mb-1">Total Ventas</p>
                      <h3 class="mb-0 text-success">{{ formatCurrency(reporteVentas.resumen.total_ventas) }}</h3>
                    </div>
                    <div class="info-icon bg-success bg-opacity-10 text-success">
                      <i class="fas fa-dollar-sign fa-2x"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <div class="d-flex justify-content-between">
                    <div>
                      <p class="text-muted mb-1">Total Tickets</p>
                      <h3 class="mb-0 text-primary">{{ reporteVentas.resumen.total_tickets }}</h3>
                    </div>
                    <div class="info-icon bg-primary bg-opacity-10 text-primary">
                      <i class="fas fa-receipt fa-2x"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <div class="d-flex justify-content-between">
                    <div>
                      <p class="text-muted mb-1">Ticket Promedio</p>
                      <h3 class="mb-0 text-info">{{ formatCurrency(reporteVentas.resumen.ticket_promedio) }}</h3>
                    </div>
                    <div class="info-icon bg-info bg-opacity-10 text-info">
                      <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Tabla de Tickets -->
          <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white">
              <h6 class="mb-0">Detalle de Tickets</h6>
            </div>
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>N° Ticket</th>
                    <th>Cliente</th>
                    <th>Empleado</th>
                    <th>Fecha</th>
                    <th>Método Pago</th>
                    <th>Estado</th>
                    <th class="text-end">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="ticket in reporteVentas.tickets" :key="ticket.id">
                    <td><strong>{{ ticket.numero_ticket }}</strong></td>
                    <td>{{ ticket.cliente }}</td>
                    <td>{{ ticket.empleado }}</td>
                    <td><small>{{ ticket.fecha }}</small></td>
                    <td><span class="badge bg-secondary">{{ ticket.metodo_pago }}</span></td>
                    <td>
                      <span 
                        class="badge"
                        :class="{
                          'bg-success': ticket.estado_pago === 'pagado',
                          'bg-warning': ticket.estado_pago === 'pendiente',
                          'bg-info': ticket.estado_pago === 'parcial'
                        }"
                      >
                        {{ ticket.estado_pago }}
                      </span>
                    </td>
                    <td class="text-end"><strong class="text-success">${{ ticket.total }}</strong></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Reporte de Servicios -->
      <div v-show="tabActiva === 'servicios'">
        <div class="card shadow-sm border-0 mb-4">
          <div class="card-header bg-success text-white">
            <h6 class="mb-0">
              <i class="fas fa-filter me-2"></i>Filtros
            </h6>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-5">
                <label class="form-label">Fecha Inicio</label>
                <input type="date" v-model="fechaInicio" class="form-control">
              </div>
              <div class="col-md-5">
                <label class="form-label">Fecha Fin</label>
                <input type="date" v-model="fechaFin" class="form-control">
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <button 
                  @click="generarReporteServicios" 
                  class="btn btn-success w-100"
                  :disabled="cargando"
                >
                  <i class="fas fa-chart-bar me-2"></i>Generar
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-if="reporteServicios">
          <!-- Resumen -->
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <p class="text-muted mb-1">Total Servicios Vendidos</p>
                  <h3 class="mb-0 text-success">{{ reporteServicios.resumen.total_servicios }}</h3>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <p class="text-muted mb-1">Total Ingresos por Servicios</p>
                  <h3 class="mb-0 text-success">{{ formatCurrency(reporteServicios.resumen.total_ingresos) }}</h3>
                </div>
              </div>
            </div>
          </div>

          <!-- Tabla -->
          <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">
              <h6 class="mb-0">Servicios Más Vendidos</h6>
            </div>
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Servicio</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-end">Precio Promedio</th>
                    <th class="text-end">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(servicio, index) in reporteServicios.servicios" :key="index">
                    <td><strong>{{ servicio.nombre }}</strong></td>
                    <td class="text-center"><span class="badge bg-primary">{{ servicio.cantidad }}</span></td>
                    <td class="text-end">${{ servicio.precio_promedio }}</td>
                    <td class="text-end"><strong class="text-success">${{ servicio.total }}</strong></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Reporte de Productos -->
      <div v-show="tabActiva === 'productos'">
        <div class="card shadow-sm border-0 mb-4">
          <div class="card-header bg-warning text-dark">
            <h6 class="mb-0">
              <i class="fas fa-filter me-2"></i>Filtros
            </h6>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-5">
                <label class="form-label">Fecha Inicio</label>
                <input type="date" v-model="fechaInicio" class="form-control">
              </div>
              <div class="col-md-5">
                <label class="form-label">Fecha Fin</label>
                <input type="date" v-model="fechaFin" class="form-control">
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <button 
                  @click="generarReporteProductos" 
                  class="btn btn-warning w-100"
                  :disabled="cargando"
                >
                  <i class="fas fa-chart-bar me-2"></i>Generar
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-if="reporteProductos">
          <!-- Resumen -->
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <p class="text-muted mb-1">Total Productos Vendidos</p>
                  <h3 class="mb-0 text-warning">{{ reporteProductos.resumen.total_productos }}</h3>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <p class="text-muted mb-1">Total Ingresos por Productos</p>
                  <h3 class="mb-0 text-warning">{{ formatCurrency(reporteProductos.resumen.total_ingresos) }}</h3>
                </div>
              </div>
            </div>
          </div>

          <!-- Tabla -->
          <div class="card shadow-sm border-0">
            <div class="card-header bg-warning text-dark">
              <h6 class="mb-0">Productos Más Vendidos</h6>
            </div>
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Marca</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-center">Stock</th>
                    <th class="text-end">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(producto, index) in reporteProductos.productos" :key="index">
                    <td><code>{{ producto.codigo }}</code></td>
                    <td><strong>{{ producto.nombre }}</strong></td>
                    <td>{{ producto.marca || '-' }}</td>
                    <td class="text-center"><span class="badge bg-primary">{{ producto.cantidad }}</span></td>
                    <td class="text-center">
                      <span 
                        class="badge"
                        :class="{
                          'bg-success': producto.stock_actual > 10,
                          'bg-warning text-dark': producto.stock_actual > 0 && producto.stock_actual <= 10,
                          'bg-danger': producto.stock_actual === 0
                        }"
                      >
                        {{ producto.stock_actual }}
                      </span>
                    </td>
                    <td class="text-end"><strong class="text-success">${{ producto.total }}</strong></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Reporte de Empleados -->
      <div v-show="tabActiva === 'empleados'">
        <div class="card shadow-sm border-0 mb-4">
          <div class="card-header bg-info text-white">
            <h6 class="mb-0">
              <i class="fas fa-filter me-2"></i>Filtros
            </h6>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-5">
                <label class="form-label">Fecha Inicio</label>
                <input type="date" v-model="fechaInicio" class="form-control">
              </div>
              <div class="col-md-5">
                <label class="form-label">Fecha Fin</label>
                <input type="date" v-model="fechaFin" class="form-control">
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <button 
                  @click="generarReporteEmpleados" 
                  class="btn btn-info w-100"
                  :disabled="cargando"
                >
                  <i class="fas fa-chart-bar me-2"></i>Generar
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-if="reporteEmpleados" class="card shadow-sm border-0">
          <div class="card-header bg-info text-white">
            <h6 class="mb-0">Rendimiento por Empleado</h6>
          </div>
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Empleado</th>
                  <th>Puesto</th>
                  <th class="text-center">Tickets</th>
                  <th class="text-center">Agendas</th>
                  <th class="text-end">Ticket Promedio</th>
                  <th class="text-end">Total Ventas</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(empleado, index) in reporteEmpleados.empleados" :key="index">
                  <td><strong>{{ empleado.nombre }}</strong></td>
                  <td><small class="text-muted">{{ empleado.puesto }}</small></td>
                  <td class="text-center"><span class="badge bg-primary">{{ empleado.total_tickets }}</span></td>
                  <td class="text-center"><span class="badge bg-success">{{ empleado.agendas_completadas }}</span></td>
                  <td class="text-end">${{ empleado.ticket_promedio }}</td>
                  <td class="text-end"><strong class="text-success">${{ empleado.total_ventas }}</strong></td>
                </tr>
              </tbody>
            </table>
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

.nav-tabs .nav-link {
  color: #6c757d;
  border: none;
  border-bottom: 3px solid transparent;
}

.nav-tabs .nav-link:hover {
  border-color: #dee2e6;
}

.nav-tabs .nav-link.active {
  color: #0d6efd;
  border-bottom-color: #0d6efd;
  background: none;
}

.table tbody tr {
  transition: background-color 0.2s;
}

.table tbody tr:hover {
  background-color: rgba(0, 123, 255, 0.05);
}
</style>