<script setup>
import { useForm } from '@inertiajs/vue3'
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

function submit() {
  form.put(`/tickets/${props.ticket.id}`)
}
</script>

<template>
  <AdminLayout title="Editar Ticket">
    <div class="container-fluid px-3">

      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 text-primary fw-bold">
          <i class="fas fa-edit me-2"></i> Editar Ticket {{ ticket.numero_ticket }}
        </h1>
        <a href="/tickets" class="btn btn-secondary">
          <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
      </div>

      <form @submit.prevent="submit">
        <div class="row g-3">

          <!-- Información del Ticket -->
          <div class="col-md-6">
            <div class="card shadow-sm border-0">
              <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                  <i class="fas fa-info-circle me-2"></i> Información del Ticket
                </h6>
              </div>
              <div class="card-body">
                <!-- Items -->
                <div class="alert alert-info">
                  <strong>Items vendidos:</strong>
                  <ul class="mb-0 mt-2">
                    <li v-for="item in ticket.items" :key="item.id">
                      {{ item.nombre }} - {{ item.cantidad }} × ${{ item.precio_unitario }}
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          <!-- Edición -->
          <div class="col-md-6">
            <div class="card shadow-sm border-0">
              <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                  <i class="fas fa-edit me-2"></i> Editar Detalles
                </h6>
              </div>
              <div class="card-body">
                
                <!-- Método de Pago -->
                <div class="mb-3">
                  <label class="form-label fw-semibold">
                    <i class="fas fa-money-bill-wave text-success me-2"></i> Método de Pago
                  </label>
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
                </div>

                <!-- Estado de Pago -->
                <div class="mb-3">
                  <label class="form-label fw-semibold">
                    <i class="fas fa-flag text-danger me-2"></i> Estado de Pago
                  </label>
                  <select 
                    v-model="form.estado_pago" 
                    class="form-select"
                    :class="{ 'is-invalid': form.errors.estado_pago }"
                  >
                    <option value="pagado">Pagado</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="parcial">Parcial</option>
                  </select>
                  <div v-if="form.errors.estado_pago" class="invalid-feedback">
                    {{ form.errors.estado_pago }}
                  </div>
                </div>

                <!-- Descuento -->
                <div class="mb-3">
                  <label class="form-label fw-semibold">
                    <i class="fas fa-percentage text-info me-2"></i> Descuento
                  </label>
                  <input 
                    type="number" 
                    v-model="form.descuento" 
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.descuento }"
                    step="0.01"
                    min="0"
                  >
                  <div v-if="form.errors.descuento" class="invalid-feedback">
                    {{ form.errors.descuento }}
                  </div>
                </div>

                <!-- Notas -->
                <div class="mb-3">
                  <label class="form-label fw-semibold">
                    <i class="fas fa-sticky-note text-secondary me-2"></i> Notas
                  </label>
                  <textarea 
                    v-model="form.notas" 
                    class="form-control" 
                    rows="3"
                    :class="{ 'is-invalid': form.errors.notas }"
                  ></textarea>
                  <div v-if="form.errors.notas" class="invalid-feedback">
                    {{ form.errors.notas }}
                  </div>
                </div>

              </div>
            </div>
          </div>

        </div>

        <!-- Botones -->
        <div class="d-flex justify-content-end gap-2 mt-4">
          <a href="/tickets" class="btn btn-secondary">
            <i class="fas fa-times me-2"></i> Cancelar
          </a>
          <button 
            type="submit" 
            class="btn btn-primary"
            :disabled="form.processing"
          >
            <i class="fas fa-save me-2"></i>
            {{ form.processing ? 'Guardando...' : 'Actualizar Ticket' }}
          </button>
        </div>

      </form>

    </div>
  </AdminLayout>
</template>

<style scoped>
.card {
  transition: transform 0.2s;
}
</style>