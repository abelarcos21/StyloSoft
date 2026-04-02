<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref, computed, onMounted } from 'vue'
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
      alert('Stock insuficiente')
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
      alert('Stock insuficiente')
      return
    }
    item.cantidad++
  } else if (operacion === 'decrementar' && item.cantidad > 1) {
    item.cantidad--
  }
}

function submit() {
  if (itemsVenta.value.length === 0) {
    alert('Debes agregar al menos un item')
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
  <AdminLayout title="Nuevo Ticket">
    <div class="container-fluid px-3">

      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 text-primary fw-bold">
          <i class="fas fa-cash-register me-2"></i> Punto de Venta
        </h1>
        <a href="/tickets" class="btn btn-secondary">
          <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
      </div>

      <form @submit.prevent="submit">
        <div class="row g-3">

          <!-- Panel Izquierdo - Selección de Items -->
          <div class="col-lg-7">

            <!-- Cliente y Empleado -->
            <div class="card shadow-sm border-0 mb-3">
              <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                  <i class="fas fa-users me-2"></i> Información de Venta
                </h6>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Cliente (Opcional)</label>
                    <select id="cliente-select" class="form-select"></select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Empleado que Atiende *</label>
                    <select
                      id="empleado-select"
                      class="form-select"
                      :class="{ 'is-invalid': form.errors.empleado_id }"
                    ></select>
                    <div v-if="form.errors.empleado_id" class="invalid-feedback">
                      {{ form.errors.empleado_id }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Búsqueda de Items -->
            <div class="card shadow-sm border-0 mb-3">
              <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                  <i class="fas fa-search me-2"></i> Buscar Servicios y Productos
                </h6>
              </div>
              <div class="card-body">
                <input
                  type="text"
                  v-model="busquedaItem"
                  class="form-control form-control-lg mb-3"
                  placeholder="Buscar por nombre..."
                >

                <div class="items-grid">
                  <!-- Servicios -->
                  <div
                    v-for="servicio in servicios.filter(s => !busquedaItem || s.text.toLowerCase().includes(busquedaItem.toLowerCase()))"
                    :key="'s-' + servicio.id"
                    @click="agregarItem(servicio)"
                    class="item-card bg-info"
                  >
                    <i class="fas fa-cut fa-2x mb-2"></i>
                    <div class="item-nombre">{{ servicio.text.split(' - ')[0] }}</div>
                    <div class="item-precio">${{ servicio.precio }}</div>
                  </div>

                  <!-- Productos -->
                  <div
                    v-for="producto in productos.filter(p => !busquedaItem || p.text.toLowerCase().includes(busquedaItem.toLowerCase()))"
                    :key="'p-' + producto.id"
                    @click="agregarItem(producto)"
                    class="item-card bg-warning text-dark"
                  >
                    <i class="fas fa-box fa-2x mb-2"></i>
                    <div class="item-nombre">{{ producto.text.split(' - ')[0] }}</div>
                    <div class="item-precio">${{ producto.precio }}</div>
                    <small class="d-block">Stock: {{ producto.stock }}</small>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- Panel Derecho - Carrito -->
          <div class="col-lg-5">

            <!-- Items del Carrito -->
            <div class="card shadow-sm border-0 mb-3">
              <div class="card-header bg-dark text-white">
                <h6 class="mb-0">
                  <i class="fas fa-shopping-cart me-2"></i> Items ({{ itemsVenta.length }})
                </h6>
              </div>
              <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                <div v-if="itemsVenta.length === 0" class="text-center py-5">
                  <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                  <p class="text-muted">No hay items agregados</p>
                </div>

                <div v-else>
                  <div
                    v-for="(item, index) in itemsVenta"
                    :key="index"
                    class="item-carrito"
                  >
                    <div class="d-flex justify-content-between align-items-center">
                      <div class="flex-grow-1">
                        <strong>{{ item.nombre }}</strong>
                        <div class="text-muted small">
                          <i :class="item.tipo === 'servicio' ? 'fas fa-cut' : 'fas fa-box'"></i>
                          {{ item.tipo }}
                        </div>
                      </div>
                      <button
                        type="button"
                        @click="eliminarItem(index)"
                        class="btn btn-sm btn-danger"
                      >
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                      <div class="btn-group">
                        <button
                          type="button"
                          @click="cambiarCantidad(item, 'decrementar')"
                          class="btn btn-sm btn-outline-secondary"
                        >
                          <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                          {{ item.cantidad }}
                        </button>
                        <button
                          type="button"
                          @click="cambiarCantidad(item, 'incrementar')"
                          class="btn btn-sm btn-outline-secondary"
                        >
                          <i class="fas fa-plus"></i>
                        </button>
                      </div>

                      <div>
                        <span class="text-muted">${{ item.precio_unitario }} × {{ item.cantidad }}</span>
                        <strong class="d-block text-success">
                          ${{ (item.cantidad * item.precio_unitario).toFixed(2) }}
                        </strong>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Totales -->
            <div class="card shadow-sm border-0 mb-3">
              <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                  <i class="fas fa-calculator me-2"></i> Totales
                </h6>
              </div>
              <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                  <span>Subtotal:</span>
                  <strong>${{ subtotal.toFixed(2) }}</strong>
                </div>

                <div class="mb-2">
                  <label class="form-label mb-1">Descuento:</label>
                  <input
                    type="number"
                    v-model="form.descuento"
                    class="form-control form-control-sm"
                    placeholder="0.00"
                    step="0.01"
                    min="0"
                  >
                </div>

                <div class="d-flex justify-content-between mb-2">
                  <span>IVA (16%):</span>
                  <strong>${{ impuesto.toFixed(2) }}</strong>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                  <h5 class="mb-0">Total:</h5>
                  <h4 class="mb-0 text-success">${{ total.toFixed(2) }}</h4>
                </div>
              </div>
            </div>

            <!-- Método de Pago -->
            <div class="card shadow-sm border-0 mb-3">
              <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                  <i class="fas fa-money-bill-wave me-2"></i> Método de Pago
                </h6>
              </div>
              <div class="card-body">
                <select
                  v-model="form.metodo_pago"
                  class="form-select"
                  :class="{ 'is-invalid': form.errors.metodo_pago }"
                >
                  <option value="efectivo">Efectivo</option>
                  <option value="tarjeta">Tarjeta</option>
                  <option value="transferencia">Transferencia</option>
                  <option value="mixto">Mixto</option>
                </select>
                <div v-if="form.errors.metodo_pago" class="invalid-feedback">
                  {{ form.errors.metodo_pago }}
                </div>

                <!-- Notas -->
                <div class="mt-3">
                  <label class="form-label">Notas (opcional)</label>
                  <textarea
                    v-model="form.notas"
                    class="form-control"
                    rows="2"
                    placeholder="Observaciones adicionales..."
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Botón de Cobrar -->
            <button
              type="submit"
              class="btn btn-success btn-lg w-100"
              :disabled="form.processing || itemsVenta.length === 0"
            >
              <i class="fas fa-cash-register me-2"></i>
              {{ form.processing ? 'Procesando...' : 'Cobrar $' + total.toFixed(2) }}
            </button>

          </div>

        </div>
      </form>

    </div>
  </AdminLayout>
</template>

<style scoped>
.items-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 1rem;
  max-height: 500px;
  overflow-y: auto;
}

.item-card {
  padding: 1rem;
  border-radius: 0.5rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
  color: white;
  border: 2px solid transparent;
}

.item-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
  border-color: rgba(255,255,255,0.5);
}

.item-nombre {
  font-weight: bold;
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
  min-height: 2.5rem;
}

.item-precio {
  font-size: 1.2rem;
  font-weight: bold;
}

.item-carrito {
  padding: 1rem;
  border-bottom: 1px solid #e9ecef;
}

.item-carrito:last-child {
  border-bottom: none;
}
</style>
