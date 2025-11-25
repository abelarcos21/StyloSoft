<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  dias_semana: Array
})

const form = useForm({
  nombre: '',
  apellido: '',
  email: '',
  telefono: '',
  direccion: '',
  puesto: '',
  hora_entrada: '',
  hora_salida: '',
  dias_laborales: [],
  salario: '',
  comision_porcentaje: 0,
  fecha_ingreso: '',
  especialidades: ''
})

function submit() {
  form.post('/empleados', {
    onSuccess: () => form.reset()
  })
}
</script>

<template>
  <AdminLayout title="Nuevo Empleado">
    <div class="container-fluid px-3">

      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 text-primary fw-bold">
          <i class="fas fa-user-plus me-2"></i> Nuevo Empleado
        </h1>
        <a href="/empleados" class="btn btn-secondary">
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
                      placeholder="Ej: Carlos"
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
                      placeholder="Ej: Rodríguez"
                      required
                    >
                    <div v-if="form.errors.apellido" class="invalid-feedback">
                      {{ form.errors.apellido }}
                    </div>
                  </div>

                  <!-- Email -->
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-envelope text-info me-2"></i> Email
                    </label>
                    <input 
                      type="email" 
                      v-model="form.email" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.email }"
                      placeholder="ejemplo@correo.com"
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
                      placeholder="999-123-4567"
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

          <!-- Información Laboral -->
          <div class="col-12">
            <div class="card shadow-sm border-0">
              <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                  <i class="fas fa-briefcase me-2"></i> Información Laboral
                </h5>
              </div>
              <div class="card-body">
                <div class="row g-3">

                  <!-- Puesto -->
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-user-tie text-primary me-2"></i> Puesto *
                    </label>
                    <input 
                      type="text" 
                      v-model="form.puesto" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.puesto }"
                      placeholder="Ej: Barbero Senior, Estilista, Recepcionista"
                      list="puestos-list"
                      required
                    >
                    <datalist id="puestos-list">
                      <option value="Barbero Senior"></option>
                      <option value="Barbero Junior"></option>
                      <option value="Estilista"></option>
                      <option value="Manicurista"></option>
                      <option value="Masajista"></option>
                      <option value="Recepcionista"></option>
                    </datalist>
                    <div v-if="form.errors.puesto" class="invalid-feedback">
                      {{ form.errors.puesto }}
                    </div>
                  </div>

                  <!-- Fecha Ingreso -->
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-calendar-check text-info me-2"></i> Fecha de Ingreso
                    </label>
                    <input 
                      type="date" 
                      v-model="form.fecha_ingreso" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.fecha_ingreso }"
                      :max="new Date().toISOString().split('T')[0]"
                    >
                    <div v-if="form.errors.fecha_ingreso" class="invalid-feedback">
                      {{ form.errors.fecha_ingreso }}
                    </div>
                  </div>

                  <!-- Hora Entrada -->
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-clock text-success me-2"></i> Hora de Entrada
                    </label>
                    <input 
                      type="time" 
                      v-model="form.hora_entrada" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.hora_entrada }"
                    >
                    <div v-if="form.errors.hora_entrada" class="invalid-feedback">
                      {{ form.errors.hora_entrada }}
                    </div>
                  </div>

                  <!-- Hora Salida -->
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-clock text-danger me-2"></i> Hora de Salida
                    </label>
                    <input 
                      type="time" 
                      v-model="form.hora_salida" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.hora_salida }"
                    >
                    <div v-if="form.errors.hora_salida" class="invalid-feedback">
                      {{ form.errors.hora_salida }}
                    </div>
                  </div>

                  <!-- Días Laborales -->
                  <div class="col-12">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-calendar-week text-warning me-2"></i> Días Laborales
                    </label>
                    <div class="d-flex flex-wrap gap-2">
                      <div 
                        v-for="dia in dias_semana" 
                        :key="dia" 
                        class="form-check form-check-inline"
                      >
                        <input 
                          class="form-check-input" 
                          type="checkbox" 
                          :id="`dia-${dia}`"
                          :value="dia"
                          v-model="form.dias_laborales"
                        >
                        <label class="form-check-label" :for="`dia-${dia}`">
                          {{ dia.charAt(0).toUpperCase() + dia.slice(1) }}
                        </label>
                      </div>
                    </div>
                    <div v-if="form.errors.dias_laborales" class="text-danger small mt-1">
                      {{ form.errors.dias_laborales }}
                    </div>
                  </div>

                  <!-- Especialidades -->
                  <div class="col-12">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-star text-warning me-2"></i> Especialidades
                    </label>
                    <textarea 
                      v-model="form.especialidades" 
                      class="form-control" 
                      rows="2"
                      :class="{ 'is-invalid': form.errors.especialidades }"
                      placeholder="Ej: Cortes clásicos, degradados, barbas, tintes..."
                    ></textarea>
                    <div v-if="form.errors.especialidades" class="invalid-feedback">
                      {{ form.errors.especialidades }}
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>

          <!-- Compensación -->
          <div class="col-12">
            <div class="card shadow-sm border-0">
              <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                  <i class="fas fa-money-bill-wave me-2"></i> Compensación
                </h5>
              </div>
              <div class="card-body">
                <div class="row g-3">

                  <!-- Salario -->
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-dollar-sign text-success me-2"></i> Salario Base
                    </label>
                    <input 
                      type="number" 
                      v-model="form.salario" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.salario }"
                      placeholder="0.00"
                      step="0.01"
                      min="0"
                    >
                    <div v-if="form.errors.salario" class="invalid-feedback">
                      {{ form.errors.salario }}
                    </div>
                  </div>

                  <!-- Comisión -->
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-percentage text-info me-2"></i> Comisión (%)
                    </label>
                    <input 
                      type="number" 
                      v-model="form.comision_porcentaje" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.comision_porcentaje }"
                      placeholder="0"
                      step="0.01"
                      min="0"
                      max="100"
                    >
                    <div v-if="form.errors.comision_porcentaje" class="invalid-feedback">
                      {{ form.errors.comision_porcentaje }}
                    </div>
                    <small class="text-muted">Porcentaje de comisión sobre ventas</small>
                  </div>

                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Botones -->
        <div class="d-flex justify-content-end gap-2 mt-4">
          <a href="/empleados" class="btn btn-secondary">
            <i class="fas fa-times me-2"></i> Cancelar
          </a>
          <button 
            type="submit" 
            class="btn btn-primary"
            :disabled="form.processing"
          >
            <i class="fas fa-save me-2"></i>
            {{ form.processing ? 'Guardando...' : 'Guardar Empleado' }}
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