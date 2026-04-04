<script setup>
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  producto: Object
})

// Helper para dar formato de moneda
const formatoMoneda = (valor) => {
  if (!valor) return '$0.00'
  return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(valor)
}

function ajustarStock() {
  Swal.fire({
    title: "Ajustar Stock",
    html: `
      <div class="mb-3">
        <select id="tipo-ajuste" class="form-select form-select-lg">
          <option value="aumentar">Aumentar Stock</option>
          <option value="reducir">Reducir Stock</option>
          <option value="ajustar">Ajustar a cantidad exacta</option>
        </select>
      </div>
      <div class="mb-3">
        <input type="number" id="cantidad-ajuste" class="form-control form-control-lg" placeholder="Cantidad" min="1">
      </div>
      <div>
        <textarea id="motivo-ajuste" class="form-control" placeholder="Motivo del ajuste (opcional)" rows="2"></textarea>
      </div>
    `,
    showCancelButton: true,
    confirmButtonText: "Ajustar",
    cancelButtonText: "Cancelar",
    confirmButtonColor: "#d84b72", // Color brand
    cancelButtonColor: "#6c757d",
    customClass: {
      confirmButton: 'rounded-pill px-4',
      cancelButton: 'rounded-pill px-4'
    },
    preConfirm: () => {
      const tipo = document.getElementById('tipo-ajuste').value
      const cantidad = parseInt(document.getElementById('cantidad-ajuste').value)
      const motivo = document.getElementById('motivo-ajuste').value
      
      if (!cantidad || cantidad < 1) {
        Swal.showValidationMessage('Debes ingresar una cantidad válida')
        return false
      }
      
      return { tipo, cantidad, motivo }
    }
  }).then(result => {
    if (result.isConfirmed) {
      router.post(`/productos/${props.producto.id}/ajustar-stock`, result.value, {
        onSuccess: () => {
          Swal.fire({
            title: "Actualizado", 
            text: "Stock ajustado exitosamente", 
            icon: "success",
            confirmButtonColor: "#d84b72",
            customClass: { confirmButton: 'rounded-pill px-4' }
          })
        }
      })
    }
  })
}

function getStockStatus() {
  if (props.producto.stock === 0) {
    return { class: 'bg-danger text-white', text: 'SIN STOCK' }
  } else if (props.producto.requiere_reorden) {
    return { class: 'bg-warning text-dark', text: 'STOCK BAJO' }
  } else {
    return { class: 'bg-success text-white', text: 'STOCK OK' }
  }
}
</script>

<template>
  <AdminLayout :title="`Detalle: ${producto.nombre}`">
    <div class="container-fluid px-0 px-md-3 mb-5">

      <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4 gap-3">
        <h1 class="h4 brand-accent fw-bold mb-0">
          <i class="fas fa-box-open me-2"></i> Detalle del Producto
        </h1>
        <div class="d-flex flex-wrap gap-2">
          <Link href="/productos" class="btn btn-light border rounded-pill px-4 shadow-sm text-muted fw-medium">
            <i class="fas fa-arrow-left me-1"></i> Volver
          </Link>
          <button @click="ajustarStock" class="btn border border-secondary text-secondary bg-white rounded-pill px-4 shadow-sm fw-medium">
            <i class="fas fa-boxes me-1"></i> Ajustar Stock
          </button>
          <Link :href="`/productos/${producto.id}/edit`" class="btn btn-brand rounded-pill px-4 shadow-sm fw-medium text-white">
            <i class="fas fa-edit me-1"></i> Editar
          </Link>
        </div>
      </div>

      <div class="row g-4">

        <div class="col-lg-4">
          
          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body text-center p-4">
              <div class="product-icon bg-brand-light brand-accent mx-auto mb-3">
                <i class="fas fa-box-open fa-3x"></i>
              </div>
              <h4 class="mb-1 fw-bold text-dark">{{ producto.nombre }}</h4>
              <p class="text-muted mb-3" v-if="producto.marca">
                <i class="fas fa-tag me-1 text-info"></i>{{ producto.marca }}
              </p>
              
              <div class="d-flex justify-content-center gap-2 mb-2">
                <span class="badge bg-light text-dark border px-3 py-2 fw-medium tracking-wide">
                  <i class="fas fa-barcode me-1 text-muted"></i> {{ producto.codigo }}
                </span>
                <span 
                  class="badge px-3 py-2 fw-medium tracking-wide" 
                  :class="producto.activo ? 'bg-success' : 'bg-secondary'"
                >
                  {{ producto.activo ? 'ACTIVO' : 'INACTIVO' }}
                </span>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
              <h6 class="mb-0 fw-bold text-dark text-uppercase tracking-wide">
                <i class="fas fa-warehouse text-warning me-2"></i> Inventario
              </h6>
            </div>
            <div class="card-body p-4">
              <div class="text-center mb-4">
                <h1 class="display-3 fw-bold mb-0" :class="producto.stock === 0 ? 'text-danger' : 'text-dark'">
                  {{ producto.stock }}
                </h1>
                <span class="badge bg-light text-dark border px-2 py-1 text-uppercase">
                  {{ producto.unidad_medida }}
                </span>
              </div>

              <div class="d-flex justify-content-between align-items-center mb-3 bg-light p-2 rounded-3">
                <span class="text-muted small fw-bold text-uppercase tracking-wide">Stock Mínimo:</span>
                <strong class="fs-5">{{ producto.stock_minimo }}</strong>
              </div>

              <div class="text-center mt-3">
                <span class="badge px-4 py-2 rounded-pill tracking-wide" :class="getStockStatus().class">
                  <i v-if="producto.stock === 0" class="fas fa-times-circle me-1"></i>
                  <i v-else-if="producto.requiere_reorden" class="fas fa-exclamation-triangle me-1"></i>
                  <i v-else class="fas fa-check-circle me-1"></i>
                  {{ getStockStatus().text }}
                </span>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
              <h6 class="mb-0 fw-bold text-dark text-uppercase tracking-wide">
                <i class="fas fa-dollar-sign text-success me-2"></i> Precios
              </h6>
            </div>
            <div class="card-body p-4">
              <div class="price-item mb-3">
                <label class="text-muted small fw-bold text-uppercase tracking-wide d-block">Precio de Venta</label>
                <h3 class="text-success mb-0 fw-bold">{{ formatoMoneda(producto.precio_venta) }}</h3>
              </div>

              <div class="row g-2">
                <div class="col-6 price-item-sm" v-if="producto.precio_compra">
                  <label class="text-muted small fw-bold text-uppercase tracking-wide d-block">Compra</label>
                  <h6 class="text-dark mb-0">{{ formatoMoneda(producto.precio_compra) }}</h6>
                </div>
                <div class="col-6 price-item-sm" v-if="producto.precio_mayoreo">
                  <label class="text-muted small fw-bold text-uppercase tracking-wide d-block">Mayoreo</label>
                  <h6 class="text-info mb-0">{{ formatoMoneda(producto.precio_mayoreo) }}</h6>
                </div>
              </div>

              <div v-if="producto.margen_ganancia" class="alert bg-brand-light border-0 brand-accent mt-3 mb-0 rounded-3 d-flex align-items-center">
                <i class="fas fa-chart-line me-2"></i>
                <strong>Margen: {{ producto.margen_ganancia }}%</strong>
              </div>
            </div>
          </div>

        </div>

        <div class="col-lg-8">

          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
              <h6 class="mb-0 fw-bold text-dark text-uppercase tracking-wide">
                <i class="fas fa-chart-bar text-primary me-2"></i> Rendimiento del Producto
              </h6>
            </div>
            <div class="card-body p-4">
              <div class="row g-3 text-center">
                <div class="col-sm-6">
                  <div class="stat-card border-0 bg-light">
                    <h2 class="text-dark fw-bold mb-0">{{ producto.total_vendido }}</h2>
                    <p class="text-muted small fw-bold text-uppercase tracking-wide mb-0">Unidades Vendidas</p>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="stat-card border-0 bg-light">
                    <h2 class="text-success fw-bold mb-0">{{ formatoMoneda(producto.ventas_generadas) }}</h2>
                    <p class="text-muted small fw-bold text-uppercase tracking-wide mb-0">Ingresos Generados</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4 mb-4" v-if="producto.descripcion || producto.categoria">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
              <h6 class="mb-0 fw-bold text-dark text-uppercase tracking-wide">
                <i class="fas fa-align-left text-secondary me-2"></i> Detalles Adicionales
              </h6>
            </div>
            <div class="card-body p-4">
              <div class="mb-4" v-if="producto.categoria">
                <label class="text-muted small fw-bold text-uppercase tracking-wide d-block mb-1">Categoría</label>
                <span class="badge bg-light text-dark border px-3 py-2 fs-6 fw-medium">{{ producto.categoria }}</span>
              </div>

              <div v-if="producto.descripcion">
                <label class="text-muted small fw-bold text-uppercase tracking-wide d-block mb-1">Descripción</label>
                <div class="bg-light p-3 rounded-3">
                  <p class="mb-0 text-dark" style="white-space: pre-line;">{{ producto.descripcion }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
              <h6 class="mb-0 fw-bold text-dark text-uppercase tracking-wide">
                <i class="fas fa-history text-info me-2"></i> Últimas Ventas
              </h6>
            </div>
            <div class="card-body p-0 pt-3">
              <div v-if="producto.ultimas_ventas && producto.ultimas_ventas.length" class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th class="ps-4 text-muted small fw-bold text-uppercase tracking-wide border-0">Ticket</th>
                      <th class="text-muted small fw-bold text-uppercase tracking-wide border-0">Fecha</th>
                      <th class="text-muted small fw-bold text-uppercase tracking-wide border-0">Cliente</th>
                      <th class="text-center text-muted small fw-bold text-uppercase tracking-wide border-0">Cant.</th>
                      <th class="text-end pe-4 text-muted small fw-bold text-uppercase tracking-wide border-0">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="venta in producto.ultimas_ventas" :key="venta.id">
                      <td class="ps-4">
                        <Link :href="`/tickets/${venta.id}`" class="text-decoration-none fw-bold brand-accent">
                          #{{ venta.id }}
                        </Link>
                      </td>
                      <td>
                        <span class="text-muted small">{{ venta.fecha }}</span>
                      </td>
                      <td class="text-dark fw-medium">{{ venta.cliente }}</td>
                      <td class="text-center">
                        <span class="badge bg-light text-dark border">{{ venta.cantidad }}</span>
                      </td>
                      <td class="text-end pe-4 fw-bold text-success">{{ formatoMoneda(venta.subtotal) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div v-else class="text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                  <i class="fas fa-receipt fa-2x text-muted"></i>
                </div>
                <h6 class="fw-bold text-dark">Sin historial de ventas</h6>
                <p class="text-muted mb-0 small">Este producto aún no se ha vendido.</p>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center px-2">
            <small class="text-muted"><i class="fas fa-calendar-alt me-1"></i> Registrado: {{ producto.created_at }}</small>
            <small class="text-muted"><i class="fas fa-hashtag me-1"></i> ID Interno: {{ producto.id }}</small>
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

.btn-brand {
  background-color: #d84b72;
  border-color: #d84b72;
  transition: all 0.2s ease;
}

.btn-brand:hover {
  background-color: #c03e61;
  border-color: #c03e61;
  transform: translateY(-1px);
}

/* Tipografía de apoyo */
.tracking-wide { 
  letter-spacing: 0.05em; 
}

/* Elementos visuales */
.product-icon {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.price-item {
  padding: 1.25rem;
  border-radius: 0.75rem;
  background-color: #f8f9fa;
  border-left: 4px solid #198754; /* Borde verde para destacar venta */
}

.price-item-sm {
  padding: 1rem;
  border-radius: 0.75rem;
  background-color: #f8f9fa;
}

.stat-card {
  padding: 1.5rem;
  border-radius: 1rem;
  transition: transform 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-3px);
}

/* Tarjetas base */
.card {
  transition: box-shadow 0.2s ease;
}

.card:hover {
  box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
}

/* Tabla */
.table tbody tr {
  transition: background-color 0.2s;
}

.table tbody tr:hover {
  background-color: rgba(216, 75, 114, 0.04);
}
</style>