<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const form = useForm({
  nombre: '',
  apellido: '',
  email: '',
  telefono: '',
  direccion: '',
  fecha_nacimiento: '',
  genero: '',
  acepta_marketing: false,
  notas: ''
})

function submit() {
  form.post('/clientes', {
    onSuccess: () => form.reset()
  })
}
</script>

<template>
  <AdminLayout title="Nuevo Cliente">
    <div class="container-fluid px-0 px-md-3">

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h1 class="h4 brand-accent fw-bold mb-0">
          <i class="fas fa-user-plus me-2"></i> Nuevo Cliente
        </h1>
        <Link href="/clientes" class="btn btn-light rounded-pill px-4 shadow-sm text-muted fw-medium">
          <i class="fas fa-arrow-left me-2"></i> Volver
        </Link>
      </div>

      <form @submit.prevent="submit">
        <div class="row g-4">

          <div class="col-lg-8">

            <div class="card shadow-sm border-0 rounded-4 mb-4">
              <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="mb-0 fw-bold text-dark">
                  <i class="fas fa-user brand-accent me-2"></i> Información Personal
                </h5>
              </div>
              <div class="card-body p-4">
                <div class="row g-4">

                  <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide mb-2">
                      <i class="fas fa-signature text-secondary me-1"></i> Nombre *
                    </label>
                    <input
                      type="text"
                      v-model="form.nombre"
                      class="form-control rounded-3 py-2 custom-input"
                      :class="{ 'is-invalid': form.errors.nombre }"
                      placeholder="Ej: Juan"
                      required
                    >
                    <div v-if="form.errors.nombre" class="invalid-feedback">
                      {{ form.errors.nombre }}
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide mb-2">
                      <i class="fas fa-signature text-secondary me-1"></i> Apellido *
                    </label>
                    <input
                      type="text"
                      v-model="form.apellido"
                      class="form-control rounded-3 py-2 custom-input"
                      :class="{ 'is-invalid': form.errors.apellido }"
                      placeholder="Ej: Pérez"
                      required
                    >
                    <div v-if="form.errors.apellido" class="invalid-feedback">
                      {{ form.errors.apellido }}
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide mb-2">
                      <i class="fas fa-birthday-cake text-secondary me-1"></i> Fecha de Nacimiento
                    </label>
                    <input
                      type="date"
                      v-model="form.fecha_nacimiento"
                      class="form-control rounded-3 py-2 custom-input"
                      :class="{ 'is-invalid': form.errors.fecha_nacimiento }"
                      :max="new Date().toISOString().split('T')[0]"
                    >
                    <div v-if="form.errors.fecha_nacimiento" class="invalid-feedback">
                      {{ form.errors.fecha_nacimiento }}
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide mb-2">
                      <i class="fas fa-venus-mars text-secondary me-1"></i> Género
                    </label>
                    <select
                      v-model="form.genero"
                      class="form-select rounded-3 py-2 custom-input"
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

                </div>
              </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="mb-0 fw-bold text-dark">
                  <i class="fas fa-address-book brand-accent me-2"></i> Información de Contacto
                </h5>
              </div>
              <div class="card-body p-4">
                <div class="row g-4">

                  <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide mb-2">
                      <i class="fas fa-envelope text-secondary me-1"></i> Email
                    </label>
                    <input
                      type="email"
                      v-model="form.email"
                      class="form-control rounded-3 py-2 custom-input"
                      :class="{ 'is-invalid': form.errors.email }"
                      placeholder="ejemplo@correo.com"
                    >
                    <div v-if="form.errors.email" class="invalid-feedback">
                      {{ form.errors.email }}
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide mb-2">
                      <i class="fas fa-phone text-secondary me-1"></i> Teléfono
                    </label>
                    <input
                      type="text"
                      v-model="form.telefono"
                      class="form-control rounded-3 py-2 custom-input"
                      :class="{ 'is-invalid': form.errors.telefono }"
                      placeholder="999-123-4567"
                    >
                    <div v-if="form.errors.telefono" class="invalid-feedback">
                      {{ form.errors.telefono }}
                    </div>
                  </div>

                  <div class="col-12">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide mb-2">
                      <i class="fas fa-map-marker-alt text-secondary me-1"></i> Dirección
                    </label>
                    <textarea
                      v-model="form.direccion"
                      class="form-control rounded-3 py-2 custom-input"
                      rows="2"
                      :class="{ 'is-invalid': form.errors.direccion }"
                      placeholder="Calle, número, colonia, ciudad..."
                    ></textarea>
                    <div v-if="form.errors.direccion" class="invalid-feedback">
                      {{ form.errors.direccion }}
                    </div>
                  </div>

                </div>
              </div>
            </div>

          </div>

          <div class="col-lg-4">

            <div class="card shadow-sm border-0 rounded-4 mb-4">
              <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="mb-0 fw-bold text-dark">
                  <i class="fas fa-cog brand-accent me-2"></i> Preferencias
                </h5>
              </div>
              <div class="card-body p-4">
                <div class="p-3 bg-brand-subtle rounded-4 border border-white">
                  <label class="form-label brand-accent small fw-bold text-uppercase tracking-wide d-block mb-2">
                    <i class="fas fa-bullhorn me-1"></i> Marketing
                  </label>
                  <div class="form-check form-switch mt-2 ml-2 mb-0">
                    <input
                      class="form-check-input custom-switch"
                      type="checkbox"
                      v-model="form.acepta_marketing"
                      id="acepta_marketing"
                      role="switch"
                    >
                    <label class="form-check-label text-dark fw-medium ms-3" for="acepta_marketing">
                      Acepta recibir promociones
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mb-4">
              <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="mb-0 fw-bold text-dark">
                  <i class="fas fa-sticky-note text-secondary me-2"></i> Notas Adicionales
                </h5>
              </div>
              <div class="card-body p-4">
                <label class="form-label text-muted small fw-bold text-uppercase tracking-wide mb-2">
                  Observaciones
                </label>
                <textarea
                  v-model="form.notas"
                  class="form-control rounded-3 py-2 custom-input"
                  rows="5"
                  :class="{ 'is-invalid': form.errors.notas }"
                  placeholder="Alergias, preferencias de servicio, observaciones especiales..."
                ></textarea>
                <div v-if="form.errors.notas" class="invalid-feedback">
                  {{ form.errors.notas }}
                </div>
              </div>
            </div>

            <div class="d-flex flex-column gap-3">
              <button
                type="submit"
                class="btn btn-brand rounded-pill py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center"
                :disabled="form.processing"
              >
                <i class="fas fa-save me-2"></i>
                {{ form.processing ? 'Guardando...' : 'Guardar Cliente' }}
              </button>

              <Link
                href="/clientes"
                class="btn btn-light rounded-pill py-3 fw-medium d-flex align-items-center justify-content-center border"
              >
                <i class="fas fa-times me-2"></i> Cancelar
              </Link>
            </div>

          </div>

        </div>
      </form>

    </div>
  </AdminLayout>
</template>

<style scoped>
/* Variables y colores corporativos */
.brand-accent {
  color: #d84b72;
}

.bg-brand-subtle {
  background-color: #fce8ee;
}

.btn-brand {
  background-color: #d84b72;
  color: white;
  transition: all 0.3s ease;
}

.btn-brand:hover {
  background-color: #c03d61;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(216, 75, 114, 0.3) !important;
}

/* Hover de tarjetas */
.card {
  transition: box-shadow 0.2s ease;
}
.card:hover {
  box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
}

/* Tipografía de apoyo */
.tracking-wide {
  letter-spacing: 0.05em;
}

/* Inputs personalizados */
.custom-input {
  border-color: #e2e3e5;
  transition: all 0.2s ease;
}

.custom-input:focus {
  border-color: #d84b72;
  box-shadow: 0 0 0 0.25rem rgba(216, 75, 114, 0.25);
}

/* Switch de Marketing */
.custom-switch {
  cursor: pointer;
  height: 1.5em;
  width: 3em;
}

.custom-switch:checked {
  background-color: #d84b72;
  border-color: #d84b72;
}

.custom-switch:focus {
  box-shadow: 0 0 0 0.25rem rgba(216, 75, 114, 0.25);
}
</style>
