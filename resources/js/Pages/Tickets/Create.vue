<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref, computed, onMounted } from 'vue'
import Swal from 'sweetalert2' // Agregado para mejorar las alertas
import $ from 'jquery'
import 'select2'
import 'select2/dist/css/select2.min.css'

const props = defineProps({
  clientes: Array,
  empleados: Array,
  servicios: Array,
  productos: Array
})

const form = useForm({
  cliente_id: null,
  empleado_id: null,
  metodo_pago: 'efectivo',
  items: [],
  descuento: 0,
  notas: ''
})

const itemsVenta = ref([])
const busquedaItem = ref('')

// Helper de Moneda
const formatoMoneda = (valor) => {
  if (valor === undefined || valor === null) return '$0.00'
  return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(valor)
}

// Computados
const subtotal = computed(() => {
  return itemsVenta.value.reduce((sum, item) => sum + (item.cantidad * item.precio_unitario), 0)
})

const descuento = computed(() => parseFloat(form.descuento) || 0)

const impuesto = computed(() => {
  const base = subtotal.value - descuento.value
  return base * 0.16 // IVA 16%
})

const total = computed(() => {
  return subtotal.value - descuento.value + impuesto.value
})

// Funciones
function agregarItem(item) {
  const existe = itemsVenta.value.find(i => i.id === item.id && i.tipo === item.tipo)

  if (existe) {
    if (item.tipo === 'producto' && existe.cantidad >= item.stock) {
      Swal.fire({
        icon: 'warning',
        title: 'Stock Insuficiente',
        text: `Solo tienes ${item.stock} unidades de este producto disponibles.`,
        confirmButtonColor: '#d84b72',
        customClass: { confirmButton: 'rounded-pill px-4' }
      })
      return
    }
    existe.cantidad++
  } else {
    itemsVenta.value.push({
      id: item.id,
      tipo: item.tipo,
      nombre: item.text.split(' - ')[0],
      cantidad: 1,
      precio_unitario: item.precio,
      stock: item.stock || null
    })
  }

  busquedaItem.value = ''
}

function eliminarItem(index) {
  itemsVenta.value.splice(index, 1)
}

function cambiarCantidad(item, operacion) {
  if (operacion === 'incrementar') {
    if (item.tipo === 'producto' && item.cantidad >= item.stock) {
      Swal.fire({
        icon: 'warning',
        title: 'Límite de Stock',
        text: 'No puedes agregar más unidades de este producto.',
        confirmButtonColor: '#d84b72',
        customClass: { confirmButton: 'rounded-pill px-4' }
      })
      return
    }
    item.cantidad++
  } else if (operacion === 'decrementar' && item.cantidad > 1) {
    item.cantidad--
  }
}

function submit() {
  if (itemsVenta.value.length === 0) {
    Swal.fire({
      icon: 'error',
      title: 'Carrito Vacío',
      text: 'Debes agregar al menos un servicio o producto para cobrar.',
      confirmButtonColor: '#d84b72',
      customClass: { confirmButton: 'rounded-pill px-4' }
    })
    return
  }

  form.items = itemsVenta.value.map(item => ({
    tipo: item.tipo,
    id: item.id,
    cantidad: item.cantidad,
    precio_unitario: item.precio_unitario
  }))

  form.post('/tickets')
}

onMounted(() => {
  $('#cliente-select').select2({
    theme: 'bootstrap-5',
    placeholder: 'Seleccionar cliente (opcional)',
    allowClear: true,
    width: '100%',
    data: props.clientes
  }).on('change', function() {
    form.cliente_id = $(this).val()
  })

  $('#empleado-select').select2({
    theme: 'bootstrap-5',
    placeholder: 'Seleccionar empleado',
    allowClear: true,
    width: '100%',
    data: props.empleados
  }).on('change', function() {
    form.empleado_id = $(this).val()
  })
})
</script>

<template>
  <AdminLayout title="Punto de Venta">
    <div class="container-fluid px-0 px-md-3 mb-5">

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h1 class="h4 brand-accent fw-bold mb-0">
          <i class="fas fa-cash-register me-2"></i> Punto de Venta
        </h1>
        <Link href="/tickets" class="btn btn-light border rounded-pill px-4 shadow-sm text-muted fw-medium">
          <i class="fas fa-arrow-left me-1"></i> Volver a Tickets
        </Link>
      </div>

      <form @submit.prevent="submit">
        <div class="row g-4">

          <div class="col-lg-7 col-xl-8">

            <div class="card shadow-sm border-0 rounded-4 mb-4">
              <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h6 class="mb-0 fw-bold text-dark text-uppercase tracking-wide">
                  <i class="fas fa-user-tag text-primary me-2"></i> Información de Venta
                </h6>
              </div>
              <div class="card-body p-4">
                <div class="row g-4">
                  <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Cliente (Opcional)</label>
                    <div class="select-wrapper">
                      <select id="cliente-select" class="form-select"></select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Empleado que Atiende <span class="text-danger">*</span></label>
                    <div class="select-wrapper">
                      <select
                        id="empleado-select"
                        class="form-select"
                        :class="{ 'is-invalid': form.errors.empleado_id }"
                      ></select>
                      <div v-if="form.errors.empleado_id" class="invalid-feedback d-block mt-1">
                        {{ form.errors.empleado_id }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h6 class="mb-0 fw-bold text-dark text-uppercase tracking-wide">
                  <i class="fas fa-th-large text-success me-2"></i> Catálogo
                </h6>
              </div>
              <div class="card-body p-4">
                <div class="input-group mb-4 shadow-sm rounded-pill overflow-hidden border">
                  <span class="input-group-text bg-white border-0 text-muted ps-4">
                    <i class="fas fa-search"></i>
                  </span>
                  <input
                    type="text"
                    v-model="busquedaItem"
                    class="form-control form-control-lg border-0 bg-white"
                    placeholder="Buscar por nombre de servicio o producto..."
                    style="box-shadow: none;"
                  >
                </div>

                <div class="items-grid">
                  <div
                    v-for="servicio in servicios.filter(s => !busquedaItem || s.text.toLowerCase().includes(busquedaItem.toLowerCase()))"
                    :key="'s-' + servicio.id"
                    @click="agregarItem(servicio)"
                    class="pos-item-card card shadow-sm border-0 h-100"
                  >
                    <div class="card-body text-center d-flex flex-column justify-content-center p-3">
                      <div class="icon-circle bg-info bg-opacity-10 text-info mx-auto mb-2">
                        <i class="fas fa-cut"></i>
                      </div>
                      <h6 class="item-nombre text-dark fw-bold mb-1">{{ servicio.text.split(' - ')[0] }}</h6>
                      <div class="mt-auto">
                        <span class="badge bg-light text-dark border w-100 fs-6 py-2">{{ formatoMoneda(servicio.precio) }}</span>
                      </div>
                    </div>
                  </div>

                  <div
                    v-for="producto in productos.filter(p => !busquedaItem || p.text.toLowerCase().includes(busquedaItem.toLowerCase()))"
                    :key="'p-' + producto.id"
                    @click="agregarItem(producto)"
                    class="pos-item-card card shadow-sm border-0 h-100"
                  >
                    <div class="card-body text-center d-flex flex-column justify-content-center p-3">
                      <div class="icon-circle bg-warning bg-opacity-10 text-warning mx-auto mb-2">
                        <i class="fas fa-box"></i>
                      </div>
                      <h6 class="item-nombre text-dark fw-bold mb-1">{{ producto.text.split(' - ')[0] }}</h6>
                      <div class="mt-auto">
                        <span class="badge bg-light text-dark border w-100 fs-6 py-2 mb-1">{{ formatoMoneda(producto.precio) }}</span>
                        <small class="text-muted d-block fw-medium" style="font-size: 0.75rem;">
                          Stock: <span :class="producto.stock > 0 ? 'text-success' : 'text-danger'">{{ producto.stock }}</span>
                        </small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <div class="col-lg-5 col-xl-4">
            <div class="sticky-top" style="top: 1.5rem; z-index: 10;">
              
              <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
                  <h6 class="mb-0 fw-bold text-dark text-uppercase tracking-wide">
                    <i class="fas fa-shopping-cart text-brand me-2"></i> Ticket
                  </h6>
                  <span class="badge bg-brand-light brand-accent rounded-pill px-3 py-2">
                    {{ itemsVenta.length }} Items
                  </span>
                </div>
                
                <div class="card-body p-0" style="max-height: 40vh; overflow-y: auto;">
                  <div v-if="itemsVenta.length === 0" class="text-center py-5 px-3">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                      <i class="fas fa-cart-arrow-down fa-2x text-muted"></i>
                    </div>
                    <h6 class="fw-bold text-dark">El carrito está vacío</h6>
                    <p class="text-muted mb-0 small">Selecciona productos o servicios del catálogo.</p>
                  </div>

                  <div v-else class="list-group list-group-flush">
                    <div
                      v-for="(item, index) in itemsVenta"
                      :key="index"
                      class="list-group-item p-3 border-bottom item-carrito"
                    >
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="pe-2">
                          <strong class="d-block text-dark lh-sm mb-1">{{ item.nombre }}</strong>
                          <span class="badge bg-light text-muted border text-capitalize" style="font-size: 0.7rem;">
                            <i :class="item.tipo === 'servicio' ? 'fas fa-cut me-1' : 'fas fa-box me-1'"></i>{{ item.tipo }}
                          </span>
                        </div>
                        <button
                          type="button"
                          @click="eliminarItem(index)"
                          class="btn btn-sm text-danger btn-light rounded-circle p-2"
                          title="Eliminar"
                        >
                          <i class="fas fa-times"></i>
                        </button>
                      </div>

                      <div class="d-flex justify-content-between align-items-end mt-2">
                        <div class="input-group input-group-sm bg-light rounded-pill p-1 border" style="width: 100px;">
                          <button
                            type="button"
                            @click="cambiarCantidad(item, 'decrementar')"
                            class="btn btn-sm btn-light rounded-circle text-muted border-0"
                            style="width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center;"
                          >
                            <i class="fas fa-minus" style="font-size: 0.6rem;"></i>
                          </button>
                          <input type="text" class="form-control border-0 bg-transparent text-center px-0 fw-bold" :value="item.cantidad" readonly style="width: 30px; box-shadow: none;">
                          <button
                            type="button"
                            @click="cambiarCantidad(item, 'incrementar')"
                            class="btn btn-sm btn-light rounded-circle text-muted border-0"
                            style="width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center;"
                          >
                            <i class="fas fa-plus" style="font-size: 0.6rem;"></i>
                          </button>
                        </div>

                        <div class="text-end">
                          <small class="text-muted d-block" style="font-size: 0.75rem;">{{ formatoMoneda(item.precio_unitario) }} c/u</small>
                          <strong class="text-success">{{ formatoMoneda(item.cantidad * item.precio_unitario) }}</strong>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="card-footer bg-light border-top p-4">
                  
                  <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted fw-bold text-uppercase tracking-wide">Subtotal</span>
                    <strong class="text-dark">{{ formatoMoneda(subtotal) }}</strong>
                  </div>

                  <div class="d-flex justify-content-between align-items-center mb-2 small">
                    <label class="text-muted fw-bold text-uppercase tracking-wide mb-0">Descuento (-)</label>
                    <div class="input-group input-group-sm w-50">
                      <span class="input-group-text bg-white">$</span>
                      <input
                        type="number"
                        v-model="form.descuento"
                        class="form-control text-end"
                        placeholder="0.00"
                        step="0.01"
                        min="0"
                      >
                    </div>
                  </div>

                  <div class="d-flex justify-content-between mb-3 small">
                    <span class="text-muted fw-bold text-uppercase tracking-wide">IVA (16%)</span>
                    <strong class="text-dark">{{ formatoMoneda(impuesto) }}</strong>
                  </div>

                  <hr class="text-muted opacity-25 mb-3">

                  <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0 fw-bold text-dark text-uppercase tracking-wide">Total</h5>
                    <h3 class="mb-0 text-success fw-bold">{{ formatoMoneda(total) }}</h3>
                  </div>

                  <div class="mb-3">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Método de Pago</label>
                    <select
                      v-model="form.metodo_pago"
                      class="form-select form-select-lg rounded-3"
                      :class="{ 'is-invalid': form.errors.metodo_pago }"
                    >
                      <option value="efectivo">💵 Efectivo</option>
                      <option value="tarjeta">💳 Tarjeta</option>
                      <option value="transferencia">🏦 Transferencia</option>
                      <option value="mixto">🔄 Mixto</option>
                    </select>
                    <div v-if="form.errors.metodo_pago" class="invalid-feedback">
                      {{ form.errors.metodo_pago }}
                    </div>
                  </div>

                  <div class="mb-4">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Notas</label>
                    <textarea
                      v-model="form.notas"
                      class="form-control rounded-3"
                      rows="2"
                      placeholder="Observaciones adicionales..."
                    ></textarea>
                  </div>

                  <button
                    type="submit"
                    class="btn btn-brand btn-lg w-100 rounded-pill fw-bold shadow-sm py-3"
                    :disabled="form.processing || itemsVenta.length === 0"
                  >
                    <i class="fas fa-check-circle me-2"></i>
                    <span v-if="form.processing">Procesando...</span>
                    <span v-else>Cobrar {{ formatoMoneda(total) }}</span>
                  </button>

                </div>
              </div>

            </div>
          </div>

        </div>
      </form>

    </div>
  </AdminLayout>
</template>

<style scoped>
/* Variables Brand */
.brand-accent {
  color: #d84b72;
}

.bg-brand-light {
  background-color: rgba(216, 75, 114, 0.1);
}

.btn-brand {
  background-color: #d84b72;
  border-color: #d84b72;
  color: white;
  transition: all 0.2s ease;
}

.btn-brand:hover:not(:disabled) {
  background-color: #c03e61;
  border-color: #c03e61;
  transform: translateY(-2px);
  color: white;
}

.text-brand {
  color: #d84b72 !important;
}

/* Tipografía */
.tracking-wide { 
  letter-spacing: 0.05em; 
}

/* Grid del Catálogo */
.items-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 1rem;
  max-height: 55vh;
  overflow-y: auto;
  padding-bottom: 1rem;
  padding-right: 0.5rem;
}

/* Custom scrollbar para los grids y carritos */
.items-grid::-webkit-scrollbar,
.card-body::-webkit-scrollbar {
  width: 6px;
}
.items-grid::-webkit-scrollbar-track,
.card-body::-webkit-scrollbar-track {
  background: #f1f1f1; 
  border-radius: 4px;
}
.items-grid::-webkit-scrollbar-thumb,
.card-body::-webkit-scrollbar-thumb {
  background: #c1c1c1; 
  border-radius: 4px;
}

/* Tarjetas de producto */
.pos-item-card {
  cursor: pointer;
  transition: all 0.2s ease;
  border: 1px solid #e9ecef !important;
}

.pos-item-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 .5rem 1rem rgba(0,0,0,.1) !important;
  border-color: #d84b72 !important;
}

.icon-circle {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
}

.item-nombre {
  font-size: 0.85rem;
  line-height: 1.2;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2; /* Trunca a 2 líneas */
  line-clamp: 2;
  -webkit-box-orient: vertical;
}

/* Carrito */
.item-carrito {
  transition: background-color 0.2s;
}
.item-carrito:hover {
  background-color: #f8f9fa;
}

/* Ajustes de Select2 para que se vea bien con Bootstrap */
.select-wrapper :deep(.select2-container .select2-selection--single) {
  height: 38px;
  border: 1px solid #dee2e6;
  border-radius: 0.375rem;
  padding: 0.25rem 0.5rem;
}
.select-wrapper :deep(.select2-container--bootstrap-5 .select2-selection) {
  box-shadow: none;
}
</style>