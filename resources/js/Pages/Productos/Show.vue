<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from 'sweetalert2'

const props = defineProps({
  producto: Object
})

function ajustarStock() {
  Swal.fire({
    title: "Ajustar Stock",
    html: `
      <select id="tipo-ajuste" class="swal2-select">
        <option value="aumentar">Aumentar Stock</option>
        <option value="reducir">Reducir Stock</option>
        <option value="ajustar">Ajustar a cantidad exacta</option>
      </select>
      <input type="number" id="cantidad-ajuste" class="swal2-input" placeholder="Cantidad" min="1">
      <textarea id="motivo-ajuste" class="swal2-textarea" placeholder="Motivo del ajuste (opcional)"></textarea>
    `,
    showCancelButton: true,
    confirmButtonText: "Ajustar",
    cancelButtonText: "Cancelar",
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
          Swal.fire("Actualizado", "Stock ajustado exitosamente", "success")
        }
      })
    }
  })
}

function getStockStatus() {
  if (props.producto.stock === 0) {
    return { class: 'bg-danger', text: 'SIN STOCK' }
  } else if (props.producto.requiere_reorden) {
    return { class: 'bg-warning text-dark', text: 'STOCK BAJO' }
  } else {
    return { class: 'bg-success', text: 'STOCK OK' }
  }
}
</script>

<template>
  <AdminLayout title="Detalle de Producto">
    <div class="container-fluid px-3">

      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 text-primary fw-bold">
          <i class="fas fa-box me-2"></i> Detalle del Producto
        </h1>
        <div class="btn-group">
          <a href="/productos" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Volver
          </a>
          <a :href="`/productos/${producto.id}/edit`" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i> Editar
          </a>
          <button @click="ajustarStock" class="btn btn-info">
            <i class="fas fa-boxes me-2"></i> Ajustar Stock
          </button>
        </div>
      </div>

      <div class="row g-3">

        <!-- Información del Producto -->
        <div class="col-lg-4">
          
          <!-- Datos Principales -->
          <div class="card shadow-sm border-0 mb-3">
            <div class="card-body text-center">
              <div class="product-icon bg-primary text-white mx-auto mb-3">
                <i class="fas fa-box-open fa-3x"></i>
              </div>
              <h4 class="mb-1">{{ producto.nombre }}</h4>
              <p class="text-muted mb-2" v-if="producto.marca">
                <i class="fas fa-tag me-1"></i>{{ producto.marca }}
              </p>
              
              <!-- Código -->
              <div class="mb-3">
                <span class="badge bg-secondary px-3 py-2">
                  {{ producto.codigo }}
                </span>
              </div>

              <!-- Estado -->
              <span 
                class="badge px-4 py-2 fs-6" 
                :class="producto.activo ? 'bg-success' : 'bg-danger'"
              >
                {{ producto.activo ? 'ACTIVO' : 'INACTIVO' }}
              </span>
            </div>
          </div>

          <!-- Stock -->
          <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-warning text-dark">
              <h6 class="mb-0">
                <i class="fas fa-warehouse me-2"></i> Inventario
              </h6>
            </div>
            <div class="card-body">
              <div class="text-center mb-3">
                <h1 class="display-4 mb-0" :class="producto.stock === 0 ? 'text-danger' : 'text-primary'">
                  {{ producto.stock }}
                </h1>
                <small class="text-muted">{{ producto.unidad_medida }}</small>
              </div>

              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Stock Mínimo:</span>
                <strong>{{ producto.stock_minimo }} {{ producto.unidad_medida }}</strong>
              </div>

              <div class="text-center mt-3">
                <span class="badge px-3 py-2" :class="getStockStatus().class">
                  {{ getStockStatus().text }}
                </span>
              </div>

              <button @click="ajustarStock" class="btn btn-info w-100 mt-3">
                <i class="fas fa-edit me-2"></i> Ajustar Stock
              </button>
            </div>
          </div>

          <!-- Precios -->
          <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">
              <h6 class="mb-0">
                <i class="fas fa-dollar-sign me-2"></i> Precios
              </h6>
            </div>
            <div class="card-body">
              <div class="price-item mb-3">
                <label class="text-muted small fw-semibold d-block">PRECIO DE VENTA</label>
                <h3 class="text-success mb-0">${{ producto.precio_venta }}</h3>
              </div>

              <div class="price-item mb-3" v-if="producto.precio_compra">
                <label class="text-muted small fw-semibold d-block">PRECIO DE COMPRA</label>
                <h5 class="text-primary mb-0">${{ producto.precio_compra }}</h5>
              </div>

              <div class="price-item mb-3" v-if="producto.precio_mayoreo">
                <label class="text-muted small fw-semibold d-block">PRECIO MAYOREO</label>
                <h5 class="text-info mb-0">${{ producto.precio_mayoreo }}</h5>
              </div>

              <div v-if="producto.margen_ganancia" class="alert alert-info mb-0">
                <strong>Margen:</strong> {{ producto.margen_ganancia }}%
              </div>
            </div>
          </div>

        </div>

        <!-- Información Detallada -->
        <div class="col-lg-8">

          <!-- Descripción y Categoría -->
          <div class="card shadow-sm border-0 mb-3" v-if="producto.descripcion || producto.categoria">
            <div class="card-header bg-primary text-white">
              <h6 class="mb-0">
                <i class="fas fa-info-circle me-2"></i> Información
              </h6>
            </div>
            <div class="card-body">
              <div class="mb-3" v-if="producto.categoria">
                <label class="text-muted small fw-semibold d-block">CATEGORÍA</label>
                <span class="badge bg-info">{{ producto.categoria }}</span>
              </div>

              <div v-if="producto.descripcion">
                <label class="text-muted small fw-semibold d-block">DESCRIPCIÓN</label>
                <p class="mb-0">{{ producto.descripcion }}</p>
              </div>
            </div>
          </div>

          <!-- Estadísticas -->
          <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-info text-white">
              <h6 class="mb-0">
                <i class="fas fa-chart-line me-2"></i> Estadísticas de Ventas
              </h6>
            </div>
            <div class="card-body">
              <div class="row text-center">
                <div class="col-md-6 mb-3">
                  <div class="stat-card">
                    <h2 class="text-primary mb-0">{{ producto.total_vendido }}</h2>
                    <small class="text-muted">Unidades Vendidas</small>
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="stat-card">
                    <h2 class="text-success mb-0">${{ producto.ventas_generadas }}</h2>
                    <small class="text-muted">Ventas Generadas</small>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Historial de Ventas -->
          <div class="card shadow-sm border-0">
            <div class="card-header bg-secondary text-white">
              <h6 class="mb-0">
                <i class="fas fa-history me-2"></i> Historial de Ventas
              </h6>
            </div>
            <div class="card-body p-0">
              <div v-if="producto.ultimas_ventas.length" class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Ticket</th>
                      <th>Fecha</th>
                      <th>Cliente</th>
                      <th class="text-center">Cantidad</th>
                      <th class="text-end">Precio Unit.</th>
                      <th class="text-end">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="venta in producto.ultimas_ventas" :key="venta.id">
                      <td>
                        <a :href="`/tickets/${venta.id}`" class="text-decoration-none">
                          #{{ venta.id }}
                        </a>
                      </td>
                      <td>
                        <small>{{ venta.fecha }}</small>
                      </td>
                      <td>{{ venta.cliente }}</td>
                      <td class="text-center">
                        <span class="badge bg-primary">{{ venta.cantidad }}</span>
                      </td>
                      <td class="text-end">${{ venta.precio_unitario }}</td>
                      <td class="text-end fw-semibold text-success">${{ venta.subtotal }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div v-else class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">Sin historial de ventas</p>
              </div>
            </div>
          </div>

          <!-- Información Adicional -->
          <div class="card shadow-sm border-0 mt-3">
            <div class="card-header bg-light">
              <h6 class="mb-0">
                <i class="fas fa-info me-2"></i> Información Adicional
              </h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <small class="text-muted">Registrado el:</small>
                  <p class="fw-semibold">{{ producto.created_at }}</p>
                </div>
                <div class="col-md-6">
                  <small class="text-muted">ID del Producto:</small>
                  <p class="fw-semibold">#{{ producto.id }}</p>
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
.product-icon {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.price-item {
  padding: 1rem;
  border-radius: 0.5rem;
  background-color: #f8f9fa;
}

.stat-card {
  padding: 1.5rem;
  border-radius: 0.5rem;
  background-color: #f8f9fa;
  border: 2px solid #e9ecef;
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