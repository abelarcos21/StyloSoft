<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  cliente: Object
})

const form = useForm({
  nombre: props.cliente.nombre,
  apellido: props.cliente.apellido,
  email: props.cliente.email,
  telefono: props.cliente.telefono,
  direccion: props.cliente.direccion,
  fecha_nacimiento: props.cliente.fecha_nacimiento,
  genero: props.cliente.genero,
  acepta_marketing: props.cliente.acepta_marketing,
  puntos_fidelidad: props.cliente.puntos_fidelidad,
  notas: props.cliente.notas
})

function submit() {
  form.put(`/clientes/${props.cliente.id}`)
}
</script>

<template>
  <AdminLayout title="Editar Cliente">
    <div class="container-fluid px-3">

      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 text-primary fw-bold">
          <i class="fas fa-user-edit me-2"></i> Editar Cliente #{{ cliente.id }}
        </h1>
        <a href="/clientes" class="btn btn-secondary">
          <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
      </div>

      <!-- Formulario -->
      <form @submit.prevent="submit">
        <div class="row g-3">

          <!-- Información Personal -->
          <div class="col-12">
            <div class="card shadow-sm border-0">
              <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                  <i class="fas fa-user me-2"></i> Información Personal
                </h5>
              </div>
              <div class="card-body">
                <div class="row g-3">

                  <!-- Nombre -->
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-signature text-primary me-2"></i> Nombre *
                    </label>
                    <input 
                      type="text" 
                      v-model="form.nombre" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.nombre }"
                      required
                    >
                    <div v-if="form.errors.nombre" class="invalid-feedback">
                      {{ form.errors.nombre }}
                    </div>
                  </div>

                  <!-- Apellido -->
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-signature text-primary me-2"></i> Apellido *
                    </label>
                    <input 
                      type="text" 
                      v-model="form.apellido" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.apellido }"
                      required
                    >
                    <div v-if="form.errors.apellido" class="invalid-feedback">
                      {{ form.errors.apellido }}
                    </div>
                  </div>

                  <!-- Fecha Nacimiento -->
                  <div class="col-md-4">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-birthday-cake text-info me-2"></i> Fecha de Nacimiento
                    </label>
                    <input 
                      type="date" 
                      v-model="form.fecha_nacimiento" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.fecha_nacimiento }"
                      :max="new Date().toISOString().split('T')[0]"
                    >
                    <div v-if="form.errors.fecha_nacimiento" class="invalid-feedback">
                      {{ form.errors.fecha_nacimiento }}
                    </div>
                  </div>

                  <!-- Género -->
                  <div class="col-md-4">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-venus-mars text-secondary me-2"></i> Género
                    </label>
                    <select 
                      v-model="form.genero" 
                      class="form-select"
                      :class="{ 'is-invalid': form.errors.genero }"
                    >
                      <option value="">Seleccionar...</option>
                      <option value="masculino">Masculino</option>
                      <option value="femenino">Femenino</option>
                      <option value="otro">Otro</option>
                      <option value="prefiero_no_decir">Prefiero no decir</option>
                    </select>
                    <div v-if="form.errors.genero" class="invalid-feedback">
                      {{ form.errors.genero }}
                    </div>
                  </div>

                  <!-- Marketing -->
                  <div class="col-md-4">
                    <label class="form-label fw-semibold d-block">
                      <i class="fas fa-bullhorn text-warning me-2"></i> Marketing
                    </label>
                    <div class="form-check form-switch mt-2">
                      <input 
                        class="form-check-input" 
                        type="checkbox" 
                        v-model="form.acepta_marketing"
                        id="acepta_marketing"
                      >
                      <label class="form-check-label" for="acepta_marketing">
                        Acepta recibir promociones
                      </label>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>

          <!-- Información de Contacto -->
          <div class="col-12">
            <div class="card shadow-sm border-0">
              <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                  <i class="fas fa-address-book me-2"></i> Información de Contacto
                </h5>
              </div>
              <div class="card-body">
                <div class="row g-3">

                  <!-- Email -->
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-envelope text-primary me-2"></i> Email
                    </label>
                    <input 
                      type="email" 
                      v-model="form.email" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.email }"
                    >
                    <div v-if="form.errors.email" class="invalid-feedback">
                      {{ form.errors.email }}
                    </div>
                  </div>

                  <!-- Teléfono -->
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-phone text-success me-2"></i> Teléfono
                    </label>
                    <input 
                      type="text" 
                      v-model="form.telefono" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.telefono }"
                    >
                    <div v-if="form.errors.telefono" class="invalid-feedback">
                      {{ form.errors.telefono }}
                    </div>
                  </div>

                  <!-- Dirección -->
                  <div class="col-12">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-map-marker-alt text-danger me-2"></i> Dirección
                    </label>
                    <textarea 
                      v-model="form.direccion" 
                      class="form-control" 
                      rows="2"
                      :class="{ 'is-invalid': form.errors.direccion }"
                    ></textarea>
                    <div v-if="form.errors.direccion" class="invalid-feedback">
                      {{ form.errors.direccion }}
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>

          <!-- Puntos de Fidelidad -->
          <div class="col-md-6">
            <div class="card shadow-sm border-0">
              <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                  <i class="fas fa-star me-2"></i> Puntos de Fidelidad
                </h5>
              </div>
              <div class="card-body">
                <label class="form-label fw-semibold">
                  Puntos Actuales
                </label>
                <input 
                  type="number" 
                  v-model="form.puntos_fidelidad" 
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.puntos_fidelidad }"
                  min="0"
                >
                <div v-if="form.errors.puntos_fidelidad" class="invalid-feedback">
                  {{ form.errors.puntos_fidelidad }}
                </div>
                <small class="text-muted">
                  Los puntos pueden ser canjeados por descuentos o servicios
                </small>
              </div>
            </div>
          </div>

          <!-- Notas -->
          <div class="col-md-6">
            <div class="card shadow-sm border-0">
              <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                  <i class="fas fa-sticky-note me-2"></i> Notas Adicionales
                </h5>
              </div>
              <div class="card-body">
                <label class="form-label fw-semibold">
                  Observaciones
                </label>
                <textarea 
                  v-model="form.notas" 
                  class="form-control" 
                  rows="3"
                  :class="{ 'is-invalid': form.errors.notas }"
                  placeholder="Alergias, preferencias, observaciones especiales..."
                ></textarea>
                <div v-if="form.errors.notas" class="invalid-feedback">
                  {{ form.errors.notas }}
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Botones -->
        <div class="d-flex justify-content-end gap-2 mt-4">
          <a href="/clientes" class="btn btn-secondary">
            <i class="fas fa-times me-2"></i> Cancelar
          </a>
          <button 
            type="submit" 
            class="btn btn-primary"
            :disabled="form.processing"
          >
            <i class="fas fa-save me-2"></i>
            {{ form.processing ? 'Guardando...' : 'Actualizar Cliente' }}
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
</style>