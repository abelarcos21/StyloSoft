<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  ticket: Object,
  clientes: Array,
  empleados: Array
})

const form = useForm({
  metodo_pago: props.ticket.metodo_pago,
  estado_pago: props.ticket.estado_pago,
  descuento: props.ticket.descuento,
  notas: props.ticket.notas
})

// Helper de Moneda para mantener consistencia con el POS
const formatoMoneda = (valor) => {
  if (valor === undefined || valor === null) return '$0.00'
  return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(valor)
}

function submit() {
  form.put(`/tickets/${props.ticket.id}`)
}
</script>

<template>
  <AdminLayout title="Editar Ticket">
    <div class="container-fluid px-0 px-md-3 mb-5">

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h1 class="h4 brand-accent fw-bold mb-0">
          <i class="fas fa-edit me-2"></i> Editar Ticket <span class="text-dark">#{{ ticket.numero_ticket }}</span>
        </h1>
        <Link href="/tickets" class="btn btn-light border rounded-pill px-4 shadow-sm text-muted fw-medium">
          <i class="fas fa-arrow-left me-1"></i> Volver a Tickets
        </Link>
      </div>

      <form @submit.prevent="submit">
        <div class="row g-4">

          <div class="col-lg-5 col-xl-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
              <div class="card-header bg-white border-bottom pt-4 pb-3 px-4">
                <h6 class="mb-0 fw-bold text-dark text-uppercase tracking-wide">
                  <i class="fas fa-receipt text-primary me-2"></i> Resumen de Items
                </h6>
              </div>
              <div class="card-body p-0">
                <div class="list-group list-group-flush">
                  <div 
                    v-for="item in ticket.items" 
                    :key="item.id"
                    class="list-group-item p-4 border-bottom-0"
                  >
                    <div class="d-flex justify-content-between align-items-start mb-1">
                      <strong class="d-block text-dark lh-sm">{{ item.nombre }}</strong>
                      <span class="badge bg-light text-dark border ms-2">x{{ item.cantidad }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                      <small class="text-muted">{{ formatoMoneda(item.precio_unitario) }} c/u</small>
                      <strong class="text-success">{{ formatoMoneda(item.cantidad * item.precio_unitario) }}</strong>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer bg-light border-top p-4 rounded-bottom-4">
                 <p class="text-muted small mb-0 text-center">
                   <i class="fas fa-info-circle me-1"></i> Los items no pueden ser modificados desde esta vista.
                 </p>
              </div>
            </div>
          </div>

          <div class="col-lg-7 col-xl-8">
            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h6 class="mb-0 fw-bold text-dark text-uppercase tracking-wide">
                  <i class="fas fa-sliders-h text-warning me-2"></i> Detalles de Cobro
                </h6>
              </div>
              <div class="card-body p-4">
                
                <div class="row g-4">
                  <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                      Método de Pago
                    </label>
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

                  <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                      Estado de Pago
                    </label>
                    <select 
                      v-model="form.estado_pago" 
                      class="form-select form-select-lg rounded-3"
                      :class="{ 'is-invalid': form.errors.estado_pago }"
                    >
                      <option value="pagado">✅ Pagado</option>
                      <option value="pendiente">⏳ Pendiente</option>
                      <option value="parcial">🌗 Parcial</option>
                    </select>
                    <div v-if="form.errors.estado_pago" class="invalid-feedback">
                      {{ form.errors.estado_pago }}
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                      Descuento Aplicado
                    </label>
                    <div class="input-group input-group-lg shadow-sm">
                      <span class="input-group-text bg-white border-end-0 text-muted">$</span>
                      <input 
                        type="number" 
                        v-model="form.descuento" 
                        class="form-control border-start-0 ps-0"
                        :class="{ 'is-invalid': form.errors.descuento }"
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                        style="box-shadow: none;"
                      >
                    </div>
                    <div v-if="form.errors.descuento" class="text-danger small mt-1">
                      {{ form.errors.descuento }}
                    </div>
                  </div>

                  <div class="col-12">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                      Notas / Observaciones
                    </label>
                    <textarea 
                      v-model="form.notas" 
                      class="form-control rounded-3" 
                      rows="3"
                      :class="{ 'is-invalid': form.errors.notas }"
                      placeholder="Agrega observaciones o detalles adicionales sobre el ticket..."
                    ></textarea>
                    <div v-if="form.errors.notas" class="invalid-feedback">
                      {{ form.errors.notas }}
                    </div>
                  </div>
                </div>

              </div>
              
              <div class="card-footer bg-white border-top-0 pt-0 pb-4 px-4 d-flex justify-content-end gap-3 mt-2">
                <Link href="/tickets" class="btn btn-light border rounded-pill px-4 fw-medium">
                  Cancelar
                </Link>
                <button 
                  type="submit" 
                  class="btn btn-brand rounded-pill px-5 fw-bold shadow-sm"
                  :disabled="form.processing"
                >
                  <i class="fas fa-save me-2"></i>
                  <span v-if="form.processing">Actualizando...</span>
                  <span v-else>Actualizar Ticket</span>
                </button>
              </div>

            </div>
          </div>

        </div>
      </form>

    </div>
  </AdminLayout>
</template>

<style scoped>
/* Variables Brand (Alineadas con el create.vue) */
.brand-accent {
  color: #d84b72;
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

/* Tipografía */
.tracking-wide { 
  letter-spacing: 0.05em; 
}

/* Tarjetas */
.card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
  box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
}

/* Inputs y Selects */
.form-select, .form-control {
  border-color: #e9ecef;
}

.form-select:focus, .form-control:focus {
  border-color: #d84b72;
  box-shadow: 0 0 0 0.25rem rgba(216, 75, 114, 0.25);
}
</style>