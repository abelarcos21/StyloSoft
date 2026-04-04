<script setup>
import { useForm, Link } from '@inertiajs/vue3'
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
    <div class="container-fluid px-0 px-md-3">

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h1 class="h4 brand-accent fw-bold mb-0">
          <i class="fas fa-plus-circle me-2"></i> Nuevo Servicio
        </h1>
        <Link href="/servicios" class="btn btn-light border rounded-pill px-4 shadow-sm text-muted fw-medium">
          <i class="fas fa-arrow-left me-1"></i> Volver
        </Link>
      </div>

      <form @submit.prevent="submit">
        <div class="row g-4">

          <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="mb-0 fw-bold text-dark">
                  <i class="fas fa-info-circle text-primary me-2"></i> Información del Servicio
                </h5>
              </div>
              <div class="card-body p-4">
                <div class="row g-4">

                  <div class="col-md-8">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                      <i class="fas fa-cut text-primary me-1"></i> Nombre del Servicio <span class="text-danger">*</span>
                    </label>
                    <input
                      type="text"
                      v-model="form.nombre"
                      class="form-control rounded-3 py-2 bg-light border-0"
                      :class="{ 'is-invalid border border-danger': form.errors.nombre }"
                      placeholder="Ej: Corte Caballero, Tinte Completo, Afeitado Clásico"
                      required
                    >
                    <div v-if="form.errors.nombre" class="invalid-feedback">
                      {{ form.errors.nombre }}
                    </div>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                      <i class="fas fa-sort-numeric-down text-secondary me-1"></i> Orden
                    </label>
                    <input
                      type="number"
                      v-model="form.orden"
                      class="form-control rounded-3 py-2 bg-light border-0"
                      :class="{ 'is-invalid border border-danger': form.errors.orden }"
                      min="0"
                    >
                    <div v-if="form.errors.orden" class="invalid-feedback">
                      {{ form.errors.orden }}
                    </div>
                    <small class="text-muted d-block mt-1">Para organizar en listas</small>
                  </div>

                  <div class="col-md-12">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                      <i class="fas fa-folder text-warning me-1"></i> Categoría
                    </label>
                    <input
                      type="text"
                      v-model="form.categoria"
                      class="form-control rounded-3 py-2 bg-light border-0"
                      :class="{ 'is-invalid border border-danger': form.errors.categoria }"
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

                  <div class="col-12">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                      <i class="fas fa-align-left text-info me-1"></i> Descripción
                    </label>
                    <textarea
                      v-model="form.descripcion"
                      class="form-control rounded-3 py-2 bg-light border-0"
                      rows="3"
                      :class="{ 'is-invalid border border-danger': form.errors.descripcion }"
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

          <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
              <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="mb-0 fw-bold text-dark">
                  <i class="fas fa-dollar-sign text-success me-2"></i> Precio y Pagos
                </h5>
              </div>
              <div class="card-body p-4">

                <div class="mb-4">
                  <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                    <i class="fas fa-money-bill-wave text-success me-1"></i> Precio del Servicio <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-3 text-muted">$</span>
                    <input
                      type="number"
                      v-model="form.precio"
                      class="form-control form-control-lg bg-light border-0 rounded-end-3"
                      :class="{ 'is-invalid': form.errors.precio }"
                      placeholder="0.00"
                      step="0.01"
                      min="0"
                      required
                    >
                  </div>
                  <div v-if="form.errors.precio" class="text-danger small mt-1">
                    {{ form.errors.precio }}
                  </div>
                </div>

                <hr class="text-muted opacity-25">

                <div class="form-check form-switch mb-3">
                  <input
                    class="form-check-input custom-switch"
                    type="checkbox"
                    v-model="form.requiere_deposito"
                    id="requiere_deposito"
                    role="switch"
                  >
                  <label class="form-check-label fw-medium ms-2" for="requiere_deposito">
                    <i class="fas fa-hand-holding-usd text-warning me-1"></i> Requiere Depósito
                  </label>
                </div>

                <div v-if="form.requiere_deposito" class="p-3 bg-light rounded-3 border">
                  <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                    Monto Mínimo
                  </label>
                  <div class="input-group">
                    <span class="input-group-text bg-white border text-muted">$</span>
                    <input
                      type="number"
                      v-model="form.deposito_minimo"
                      class="form-control border-start-0"
                      :class="{ 'is-invalid': form.errors.deposito_minimo }"
                      placeholder="0.00"
                      step="0.01"
                      min="0"
                    >
                  </div>
                  <div v-if="form.errors.deposito_minimo" class="text-danger small mt-1">
                    {{ form.errors.deposito_minimo }}
                  </div>
                  <small class="text-muted d-block mt-2">Monto que el cliente debe pagar por adelantado.</small>
                </div>

              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
              <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="mb-0 fw-bold text-dark">
                  <i class="fas fa-clock text-info me-2"></i> Configuración Operativa
                </h5>
              </div>
              <div class="card-body p-4">

                <div class="mb-3">
                  <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                    <i class="fas fa-hourglass-half text-info me-1"></i> Duración (Minutos) <span class="text-danger">*</span>
                  </label>
                  <div class="d-flex align-items-center gap-3">
                    <input
                      type="number"
                      v-model="form.duracion_minutos"
                      class="form-control form-control-lg bg-light border-0 rounded-3 w-50"
                      :class="{ 'is-invalid border border-danger': form.errors.duracion_minutos }"
                      placeholder="30"
                      min="1"
                      step="5"
                      required
                    >
                    <span class="fw-bold brand-accent bg-brand-light px-3 py-2 rounded-3">
                      {{ duracionFormateada }}
                    </span>
                  </div>
                  <div v-if="form.errors.duracion_minutos" class="text-danger small mt-1">
                    {{ form.errors.duracion_minutos }}
                  </div>
                </div>

                <div class="d-flex gap-2 mb-4 flex-wrap">
                  <button type="button" @click="form.duracion_minutos = 15" class="btn btn-sm btn-outline-secondary rounded-pill">15 min</button>
                  <button type="button" @click="form.duracion_minutos = 30" class="btn btn-sm btn-outline-secondary rounded-pill">30 min</button>
                  <button type="button" @click="form.duracion_minutos = 45" class="btn btn-sm btn-outline-secondary rounded-pill">45 min</button>
                  <button type="button" @click="form.duracion_minutos = 60" class="btn btn-sm btn-outline-secondary rounded-pill">1 hora</button>
                  <button type="button" @click="form.duracion_minutos = 90" class="btn btn-sm btn-outline-secondary rounded-pill">1.5 horas</button>
                  <button type="button" @click="form.duracion_minutos = 120" class="btn btn-sm btn-outline-secondary rounded-pill">2 horas</button>
                </div>

                <hr class="text-muted opacity-25">

                <div class="form-check form-switch mt-3">
                  <input
                    class="form-check-input custom-switch"
                    type="checkbox"
                    v-model="form.activo"
                    id="activo"
                    role="switch"
                  >
                  <label class="form-check-label fw-medium ms-2" for="activo">
                    <i class="fas fa-toggle-on text-success me-1"></i> Servicio Activo
                  </label>
                </div>
                <small class="text-muted ms-5 d-block mt-1">Si está inactivo, no podrá ser seleccionado en la agenda.</small>

              </div>
            </div>
          </div>

        </div>

        <div class="d-flex justify-content-end gap-3 mt-4 mb-5">
          <Link href="/servicios" class="btn btn-light border rounded-pill px-4 text-muted fw-medium shadow-sm">
            Cancelar
          </Link>
          <button
            type="submit"
            class="btn btn-brand rounded-pill px-4 shadow-sm fw-medium"
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
/* Variables y colores corporativos */
.brand-accent {
  color: #d84b72;
}

.bg-brand-light {
  background-color: rgba(216, 75, 114, 0.1);
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

/* Tipografía de apoyo */
.tracking-wide {
  letter-spacing: 0.05em;
}

/* Tarjetas y transiciones */
.card {
  transition: box-shadow 0.2s ease;
}

.card:hover {
  box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
}

/* Inputs y focus */
.form-control:focus {
  border-color: rgba(216, 75, 114, 0.5) !important;
  box-shadow: 0 0 0 0.25rem rgba(216, 75, 114, 0.1) !important;
  background-color: #fff !important;
}

/* Switches personalizados */
.custom-switch {
  width: 2.5em !important;
  height: 1.25em !important;
  cursor: pointer;
}

.custom-switch:checked {
  background-color: #d84b72;
  border-color: #d84b72;
}
</style>
