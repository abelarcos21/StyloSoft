<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { computed } from 'vue'

const props = defineProps({
  orden_sugerido: Number
})

const form = useForm({
  nombre: '',
  descripcion: '',
  precio: '',
  duracion_minutos: 30,
  categoria: '',
  orden: props.orden_sugerido,
  requiere_deposito: false,
  deposito_minimo: '',
  activo: true
})

const duracionFormateada = computed(() => {
  const horas = Math.floor(form.duracion_minutos / 60)
  const minutos = form.duracion_minutos % 60
  if (horas > 0) {
    return `${horas}h ${minutos}min`
  }
  return `${minutos} minutos`
})

function submit() {
  form.post('/servicios', {
    onSuccess: () => form.reset()
  })
}
</script>

<template>
  <AdminLayout title="Nuevo Servicio">
    <div class="container-fluid px-3">

      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 text-primary fw-bold">
          <i class="fas fa-plus-circle me-2"></i> Nuevo Servicio
        </h1>
        <a href="/servicios" class="btn btn-secondary">
          <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
      </div>

      <!-- Formulario -->
      <form @submit.prevent="submit">
        <div class="row g-3">

          <!-- Información Básica -->
          <div class="col-12">
            <div class="card card-primary card-outline shadow-sm">
              <div class="card-header">
                <h5 class="mb-0">
                  <i class="fas fa-info-circle me-2"></i> Información del Servicio
                </h5>
              </div>
              <div class="card-body">
                <div class="row g-3">

                  <!-- Nombre -->
                  <div class="col-md-8">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-cut text-primary me-2"></i> Nombre del Servicio *
                    </label>
                    <input 
                      type="text" 
                      v-model="form.nombre" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.nombre }"
                      placeholder="Ej: Corte Caballero, Tinte Completo, Afeitado Clásico"
                      required
                    >
                    <div v-if="form.errors.nombre" class="invalid-feedback">
                      {{ form.errors.nombre }}
                    </div>
                  </div>

                  <!-- Orden -->
                  <div class="col-md-4">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-sort-numeric-down text-secondary me-2"></i> Orden de Visualización
                    </label>
                    <input 
                      type="number" 
                      v-model="form.orden" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.orden }"
                      min="0"
                    >
                    <div v-if="form.errors.orden" class="invalid-feedback">
                      {{ form.errors.orden }}
                    </div>
                    <small class="text-muted">Para ordenar en menús y listas</small>
                  </div>

                  <!-- Categoría -->
                  <div class="col-md-12">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-folder text-warning me-2"></i> Categoría
                    </label>
                    <input 
                      type="text" 
                      v-model="form.categoria" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.categoria }"
                      placeholder="Ej: Cortes, Coloración, Afeitado, Tratamientos"
                      list="categorias-list"
                    >
                    <datalist id="categorias-list">
                      <option value="Cortes"></option>
                      <option value="Coloración"></option>
                      <option value="Afeitado"></option>
                      <option value="Peinados"></option>
                      <option value="Tratamientos"></option>
                      <option value="Barba"></option>
                      <option value="Manicure"></option>
                      <option value="Pedicure"></option>
                    </datalist>
                    <div v-if="form.errors.categoria" class="invalid-feedback">
                      {{ form.errors.categoria }}
                    </div>
                  </div>

                  <!-- Descripción -->
                  <div class="col-12">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-align-left text-info me-2"></i> Descripción
                    </label>
                    <textarea 
                      v-model="form.descripcion" 
                      class="form-control" 
                      rows="3"
                      :class="{ 'is-invalid': form.errors.descripcion }"
                      placeholder="Descripción detallada del servicio que se ofrece..."
                    ></textarea>
                    <div v-if="form.errors.descripcion" class="invalid-feedback">
                      {{ form.errors.descripcion }}
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>

          <!-- Precio y Duración -->
          <div class="col-md-6">
            <div class="card card-success card-outline shadow-sm h-100">
              <div class="card-header">
                <h5 class="mb-0">
                  <i class="fas fa-dollar-sign me-2"></i> Precio
                </h5>
              </div>
              <div class="card-body">
                
                <!-- Precio -->
                <div class="mb-3">
                  <label class="form-label fw-semibold">
                    <i class="fas fa-money-bill-wave text-success me-2"></i> Precio del Servicio *
                  </label>
                  <input 
                    type="number" 
                    v-model="form.precio" 
                    class="form-control form-control-lg"
                    :class="{ 'is-invalid': form.errors.precio }"
                    placeholder="0.00"
                    step="0.01"
                    min="0"
                    required
                  >
                  <div v-if="form.errors.precio" class="invalid-feedback">
                    {{ form.errors.precio }}
                  </div>
                </div>

                <!-- Depósito -->
                <div class="form-check form-switch mb-3">
                  <input 
                    class="form-check-input" 
                    type="checkbox" 
                    v-model="form.requiere_deposito"
                    id="requiere_deposito"
                  >
                  <label class="form-check-label fw-semibold" for="requiere_deposito">
                    <i class="fas fa-hand-holding-usd text-warning me-2"></i>
                    Requiere Depósito
                  </label>
                </div>

                <!-- Monto Depósito -->
                <div v-if="form.requiere_deposito">
                  <label class="form-label fw-semibold">
                    Depósito Mínimo
                  </label>
                  <input 
                    type="number" 
                    v-model="form.deposito_minimo" 
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.deposito_minimo }"
                    placeholder="0.00"
                    step="0.01"
                    min="0"
                  >
                  <div v-if="form.errors.deposito_minimo" class="invalid-feedback">
                    {{ form.errors.deposito_minimo }}
                  </div>
                  <small class="text-muted">Monto mínimo de depósito requerido</small>
                </div>

              </div>
            </div>
          </div>

          <!-- Duración y Estado -->
          <div class="col-md-6">
            <div class="card card-info card-outline shadow-sm h-100">
              <div class="card-header">
                <h5 class="mb-0">
                  <i class="fas fa-clock me-2"></i> Duración y Estado
                </h5>
              </div>
              <div class="card-body">
                
                <!-- Duración -->
                <div class="mb-3">
                  <label class="form-label fw-semibold">
                    <i class="fas fa-hourglass-half text-info me-2"></i> Duración Estimada (minutos) *
                  </label>
                  <input 
                    type="number" 
                    v-model="form.duracion_minutos" 
                    class="form-control form-control-lg"
                    :class="{ 'is-invalid': form.errors.duracion_minutos }"
                    placeholder="30"
                    min="1"
                    step="5"
                    required
                  >
                  <div v-if="form.errors.duracion_minutos" class="invalid-feedback">
                    {{ form.errors.duracion_minutos }}
                  </div>
                  <small class="text-muted d-block mt-2">
                    <strong>Duración: {{ duracionFormateada }}</strong>
                  </small>
                </div>

                <!-- Duraciones rápidas -->
                <div class="d-flex gap-2 mb-3 flex-wrap">
                  <button 
                    type="button" 
                    @click="form.duracion_minutos = 15" 
                    class="btn btn-sm btn-outline-secondary"
                  >
                    15 min
                  </button>
                  <button 
                    type="button" 
                    @click="form.duracion_minutos = 30" 
                    class="btn btn-sm btn-outline-secondary"
                  >
                    30 min
                  </button>
                  <button 
                    type="button" 
                    @click="form.duracion_minutos = 45" 
                    class="btn btn-sm btn-outline-secondary"
                  >
                    45 min
                  </button>
                  <button 
                    type="button" 
                    @click="form.duracion_minutos = 60" 
                    class="btn btn-sm btn-outline-secondary"
                  >
                    1 hora
                  </button>
                  <button 
                    type="button" 
                    @click="form.duracion_minutos = 90" 
                    class="btn btn-sm btn-outline-secondary"
                  >
                    1.5 horas
                  </button>
                  <button 
                    type="button" 
                    @click="form.duracion_minutos = 120" 
                    class="btn btn-sm btn-outline-secondary"
                  >
                    2 horas
                  </button>
                </div>

                <!-- Estado -->
                <div class="form-check form-switch">
                  <input 
                    class="form-check-input" 
                    type="checkbox" 
                    v-model="form.activo"
                    id="activo"
                  >
                  <label class="form-check-label fw-semibold" for="activo">
                    <i class="fas fa-toggle-on text-success me-2"></i>
                    Servicio Activo
                  </label>
                </div>
                <small class="text-muted">Los servicios inactivos no aparecerán en agendas</small>

              </div>
            </div>
          </div>

        </div>

        <!-- Botones -->
        <div class="d-flex justify-content-end gap-2 mt-4">
          <a href="/servicios" class="btn btn-secondary">
            <i class="fas fa-times me-2"></i> Cancelar
          </a>
          <button 
            type="submit" 
            class="btn btn-primary"
            :disabled="form.processing"
          >
            <i class="fas fa-save me-2"></i>
            {{ form.processing ? 'Guardando...' : 'Guardar Servicio' }}
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

.card:hover {
  transform: translateY(-2px);
}

.form-check-input:checked {
  background-color: #28a745;
  border-color: #28a745;
}
</style>