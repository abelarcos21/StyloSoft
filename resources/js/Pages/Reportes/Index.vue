<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref, computed } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

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

// Utilidad para errores
const mostrarError = () => {
  Swal.fire({
    icon: 'error',
    title: 'Error',
    text: 'Hubo un problema al generar el reporte. Por favor, intenta de nuevo.',
    confirmButtonColor: '#d84b72',
    customClass: { confirmButton: 'btn btn-brand rounded-pill px-4' }
  })
}

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
    mostrarError()
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
    mostrarError()
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
    mostrarError()
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
    mostrarError()
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
    <div class="container-fluid px-0 px-md-3">

      <div class="row mb-4">
        <div class="col-12">
          <h2 class="h4 brand-accent fw-bold mb-1">
            <i class="fas fa-chart-pie me-2"></i>Reportes y Estadísticas
          </h2>
          <p class="text-muted mb-0 small">Analiza el rendimiento general de tu negocio</p>
        </div>
      </div>

      <ul class="nav nav-tabs custom-tabs mb-4 border-0">
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

      <div v-show="tabActiva === 'ventas'" class="fade-in">
        <div class="card shadow-sm border-0 rounded-4 mb-4">
          <div class="card-body">
            <h6 class="text-muted fw-bold mb-3 text-uppercase small">Filtros de Búsqueda</h6>
            <div class="row g-3 align-items-end">
              <div class="col-md-3">
                <label class="form-label small text-muted">Fecha Inicio</label>
                <input type="date" v-model="fechaInicio" class="form-control bg-light rounded-pill border-0 focus-ring-none">
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted">Fecha Fin</label>
                <input type="date" v-model="fechaFin" class="form-control bg-light rounded-pill border-0 focus-ring-none">
              </div>
              <div class="col-md-2">
                <label class="form-label small text-muted">Empleado</label>
                <select v-model="empleadoId" class="form-select bg-light rounded-pill border-0 focus-ring-none">
                  <option value="">Todos</option>
                  <option v-for="emp in empleados" :key="emp.id" :value="emp.id">{{ emp.text }}</option>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label small text-muted">Método Pago</label>
                <select v-model="metodoPago" class="form-select bg-light rounded-pill border-0 focus-ring-none">
                  <option value="">Todos</option>
                  <option value="efectivo">Efectivo</option>
                  <option value="tarjeta">Tarjeta</option>
                  <option value="transferencia">Transferencia</option>
                  <option value="mixto">Mixto</option>
                </select>
              </div>
              <div class="col-md-2">
                <button @click="generarReporteVentas" class="btn btn-brand rounded-pill w-100" :disabled="cargando">
                  <i :class="cargando ? 'fas fa-spinner fa-spin' : 'fas fa-search'" class="me-2"></i> Generar
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-if="reporteVentas">
          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center">
                  <div class="info-icon bg-success-subtle text-success me-3 rounded-circle">
                    <i class="fas fa-dollar-sign fa-lg"></i>
                  </div>
                  <div>
                    <p class="text-muted mb-0 small text-uppercase fw-semibold">Total Ventas</p>
                    <h4 class="mb-0 text-success fw-bold">{{ formatCurrency(reporteVentas.resumen.total_ventas) }}</h4>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center">
                  <div class="info-icon bg-primary-subtle text-primary me-3 rounded-circle">
                    <i class="fas fa-receipt fa-lg"></i>
                  </div>
                  <div>
                    <p class="text-muted mb-0 small text-uppercase fw-semibold">Total Tickets</p>
                    <h4 class="mb-0 text-dark fw-bold">{{ reporteVentas.resumen.total_tickets }}</h4>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center">
                  <div class="info-icon bg-info-subtle text-info me-3 rounded-circle">
                    <i class="fas fa-chart-line fa-lg"></i>
                  </div>
                  <div>
                    <p class="text-muted mb-0 small text-uppercase fw-semibold">Ticket Promedio</p>
                    <h4 class="mb-0 text-dark fw-bold">{{ formatCurrency(reporteVentas.resumen.ticket_promedio) }}</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0 custom-table">
                <thead class="bg-light text-muted">
                  <tr>
                    <th class="border-0 font-weight-semibold">N° Ticket</th>
                    <th class="border-0 font-weight-semibold">Cliente</th>
                    <th class="border-0 font-weight-semibold">Empleado</th>
                    <th class="border-0 font-weight-semibold">Fecha</th>
                    <th class="border-0 font-weight-semibold text-center">Método Pago</th>
                    <th class="border-0 font-weight-semibold text-center">Estado</th>
                    <th class="border-0 font-weight-semibold text-end">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="ticket in reporteVentas.tickets" :key="ticket.id">
                    <td><strong class="brand-accent">{{ ticket.numero_ticket }}</strong></td>
                    <td class="text-dark">{{ ticket.cliente }}</td>
                    <td class="text-secondary small">{{ ticket.empleado }}</td>
                    <td><span class="text-muted small">{{ ticket.fecha }}</span></td>
                    <td class="text-center"><span class="badge bg-light text-secondary border rounded-pill px-3">{{ ticket.metodo_pago }}</span></td>
                    <td class="text-center">
                      <span class="badge rounded-pill px-3 py-2 text-uppercase fw-medium"
                        :class="{
                          'bg-success-subtle text-success': ticket.estado_pago === 'pagado',
                          'bg-warning-subtle text-warning': ticket.estado_pago === 'pendiente',
                          'bg-info-subtle text-info': ticket.estado_pago === 'parcial'
                        }">
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

      <div v-show="tabActiva === 'servicios'" class="fade-in">
        <div class="card shadow-sm border-0 rounded-4 mb-4">
          <div class="card-body">
            <h6 class="text-muted fw-bold mb-3 text-uppercase small">Filtros de Búsqueda</h6>
            <div class="row g-3 align-items-end">
              <div class="col-md-5">
                <label class="form-label small text-muted">Fecha Inicio</label>
                <input type="date" v-model="fechaInicio" class="form-control bg-light rounded-pill border-0 focus-ring-none">
              </div>
              <div class="col-md-5">
                <label class="form-label small text-muted">Fecha Fin</label>
                <input type="date" v-model="fechaFin" class="form-control bg-light rounded-pill border-0 focus-ring-none">
              </div>
              <div class="col-md-2">
                <button @click="generarReporteServicios" class="btn btn-brand rounded-pill w-100" :disabled="cargando">
                  <i :class="cargando ? 'fas fa-spinner fa-spin' : 'fas fa-search'" class="me-2"></i> Generar
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-if="reporteServicios">
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center">
                  <div class="info-icon bg-info-subtle text-info me-3 rounded-circle">
                    <i class="fas fa-cut fa-lg"></i>
                  </div>
                  <div>
                    <p class="text-muted mb-0 small text-uppercase fw-semibold">Servicios Realizados</p>
                    <h4 class="mb-0 text-dark fw-bold">{{ reporteServicios.resumen.total_servicios }}</h4>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center">
                  <div class="info-icon bg-success-subtle text-success me-3 rounded-circle">
                    <i class="fas fa-hand-holding-usd fa-lg"></i>
                  </div>
                  <div>
                    <p class="text-muted mb-0 small text-uppercase fw-semibold">Ingresos por Servicios</p>
                    <h4 class="mb-0 text-success fw-bold">{{ formatCurrency(reporteServicios.resumen.total_ingresos) }}</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0 custom-table">
                <thead class="bg-light text-muted">
                  <tr>
                    <th class="border-0 font-weight-semibold">Servicio</th>
                    <th class="border-0 font-weight-semibold text-center">Cantidad Realizada</th>
                    <th class="border-0 font-weight-semibold text-end">Precio Promedio</th>
                    <th class="border-0 font-weight-semibold text-end">Ingreso Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(servicio, index) in reporteServicios.servicios" :key="index">
                    <td><strong class="text-dark">{{ servicio.nombre }}</strong></td>
                    <td class="text-center"><span class="badge bg-primary-subtle text-primary rounded-pill px-3">{{ servicio.cantidad }}</span></td>
                    <td class="text-end text-muted">${{ servicio.precio_promedio }}</td>
                    <td class="text-end"><strong class="text-success">${{ servicio.total }}</strong></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div v-show="tabActiva === 'productos'" class="fade-in">
        <div class="card shadow-sm border-0 rounded-4 mb-4">
          <div class="card-body">
            <h6 class="text-muted fw-bold mb-3 text-uppercase small">Filtros de Búsqueda</h6>
            <div class="row g-3 align-items-end">
              <div class="col-md-5">
                <label class="form-label small text-muted">Fecha Inicio</label>
                <input type="date" v-model="fechaInicio" class="form-control bg-light rounded-pill border-0 focus-ring-none">
              </div>
              <div class="col-md-5">
                <label class="form-label small text-muted">Fecha Fin</label>
                <input type="date" v-model="fechaFin" class="form-control bg-light rounded-pill border-0 focus-ring-none">
              </div>
              <div class="col-md-2">
                <button @click="generarReporteProductos" class="btn btn-brand rounded-pill w-100" :disabled="cargando">
                  <i :class="cargando ? 'fas fa-spinner fa-spin' : 'fas fa-search'" class="me-2"></i> Generar
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-if="reporteProductos">
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center">
                  <div class="info-icon bg-warning-subtle text-warning me-3 rounded-circle">
                    <i class="fas fa-box-open fa-lg"></i>
                  </div>
                  <div>
                    <p class="text-muted mb-0 small text-uppercase fw-semibold">Productos Vendidos</p>
                    <h4 class="mb-0 text-dark fw-bold">{{ reporteProductos.resumen.total_productos }}</h4>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center">
                  <div class="info-icon bg-success-subtle text-success me-3 rounded-circle">
                    <i class="fas fa-coins fa-lg"></i>
                  </div>
                  <div>
                    <p class="text-muted mb-0 small text-uppercase fw-semibold">Ingresos por Productos</p>
                    <h4 class="mb-0 text-success fw-bold">{{ formatCurrency(reporteProductos.resumen.total_ingresos) }}</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0 custom-table">
                <thead class="bg-light text-muted">
                  <tr>
                    <th class="border-0 font-weight-semibold">Código</th>
                    <th class="border-0 font-weight-semibold">Producto</th>
                    <th class="border-0 font-weight-semibold">Marca</th>
                    <th class="border-0 font-weight-semibold text-center">Cant. Vendida</th>
                    <th class="border-0 font-weight-semibold text-center">Stock Actual</th>
                    <th class="border-0 font-weight-semibold text-end">Ingreso Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(producto, index) in reporteProductos.productos" :key="index">
                    <td><code class="text-muted bg-light px-2 py-1 rounded">{{ producto.codigo }}</code></td>
                    <td><strong class="text-dark">{{ producto.nombre }}</strong></td>
                    <td class="text-secondary small">{{ producto.marca || '-' }}</td>
                    <td class="text-center"><span class="badge bg-primary-subtle text-primary rounded-pill px-3">{{ producto.cantidad }}</span></td>
                    <td class="text-center">
                      <span class="badge rounded-pill px-3"
                        :class="{
                          'bg-success-subtle text-success': producto.stock_actual > 10,
                          'bg-warning-subtle text-warning': producto.stock_actual > 0 && producto.stock_actual <= 10,
                          'bg-danger-subtle text-danger': producto.stock_actual === 0
                        }">
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

      <div v-show="tabActiva === 'empleados'" class="fade-in">
        <div class="card shadow-sm border-0 rounded-4 mb-4">
          <div class="card-body">
            <h6 class="text-muted fw-bold mb-3 text-uppercase small">Filtros de Búsqueda</h6>
            <div class="row g-3 align-items-end">
              <div class="col-md-5">
                <label class="form-label small text-muted">Fecha Inicio</label>
                <input type="date" v-model="fechaInicio" class="form-control bg-light rounded-pill border-0 focus-ring-none">
              </div>
              <div class="col-md-5">
                <label class="form-label small text-muted">Fecha Fin</label>
                <input type="date" v-model="fechaFin" class="form-control bg-light rounded-pill border-0 focus-ring-none">
              </div>
              <div class="col-md-2">
                <button @click="generarReporteEmpleados" class="btn btn-brand rounded-pill w-100" :disabled="cargando">
                  <i :class="cargando ? 'fas fa-spinner fa-spin' : 'fas fa-search'" class="me-2"></i> Generar
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-if="reporteEmpleados" class="card shadow-sm border-0 rounded-4 overflow-hidden">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 custom-table">
              <thead class="bg-light text-muted">
                <tr>
                  <th class="border-0 font-weight-semibold">Empleado</th>
                  <th class="border-0 font-weight-semibold">Puesto</th>
                  <th class="border-0 font-weight-semibold text-center">Total Tickets</th>
                  <th class="border-0 font-weight-semibold text-center">Agendas Completadas</th>
                  <th class="border-0 font-weight-semibold text-end">Ticket Promedio</th>
                  <th class="border-0 font-weight-semibold text-end">Ventas Generadas</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(empleado, index) in reporteEmpleados.empleados" :key="index">
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="bg-secondary-subtle text-secondary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                        <i class="fas fa-user-tie"></i>
                      </div>
                      <strong class="text-dark">{{ empleado.nombre }}</strong>
                    </div>
                  </td>
                  <td><span class="text-muted small">{{ empleado.puesto }}</span></td>
                  <td class="text-center"><span class="badge bg-light border text-secondary rounded-pill px-3">{{ empleado.total_tickets }}</span></td>
                  <td class="text-center"><span class="badge bg-success-subtle text-success rounded-pill px-3">{{ empleado.agendas_completadas }}</span></td>
                  <td class="text-end text-muted">${{ empleado.ticket_promedio }}</td>
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
/* Variables y colores principales */
.brand-accent { color: #d84b72; }
.btn-brand { background-color: #d84b72; color: white; transition: all 0.3s ease; }
.btn-brand:hover { background-color: #c03d61; color: white; transform: translateY(-2px); }
.btn-brand:disabled { opacity: 0.7; transform: none; }

/* Modificadores de inputs */
.focus-ring-none:focus { box-shadow: none; outline: 1px solid #d84b72; }

/* Navegación por Tabs Estilizada */
.custom-tabs {
  border-bottom: 2px solid #e9ecef !important;
  gap: 0.5rem;
}
.custom-tabs .nav-link {
  color: #6c757d;
  font-weight: 500;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px 8px 0 0;
  transition: all 0.3s ease;
  position: relative;
}
.custom-tabs .nav-link::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  width: 100%;
  height: 3px;
  background-color: transparent;
  transition: all 0.3s ease;
}
.custom-tabs .nav-link:hover {
  color: #d84b72;
  background-color: rgba(216, 75, 114, 0.05);
}
.custom-tabs .nav-link.active {
  color: #d84b72;
  background-color: transparent;
}
.custom-tabs .nav-link.active::after {
  background-color: #d84b72;
}

/* Íconos de KPI */
.info-icon {
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Tabla personalizada */
.custom-table th {
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
}
.custom-table tbody tr {
  transition: background-color 0.2s ease;
}
.custom-table tbody tr:hover {
  background-color: #f8f9fa;
}

/* Clases sutiles para Bootstrap */
.bg-warning-subtle { background-color: #fff3cd !important; }
.bg-success-subtle { background-color: #d1e7dd !important; }
.bg-danger-subtle { background-color: #f8d7da !important; }
.bg-info-subtle { background-color: #cff4fc !important; }
.bg-secondary-subtle { background-color: #e2e3e5 !important; }
.bg-primary-subtle { background-color: #cfe2ff !important; }

/* Animaciones */
.fade-in {
  animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
