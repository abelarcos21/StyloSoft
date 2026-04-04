<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  empleado: Object,
  dias_semana: Array
})

const form = useForm({
  nombre: props.empleado.nombre,
  apellido: props.empleado.apellido,
  email: props.empleado.email,
  telefono: props.empleado.telefono,
  direccion: props.empleado.direccion,
  puesto: props.empleado.puesto,
  hora_entrada: props.empleado.hora_entrada,
  hora_salida: props.empleado.hora_salida,
  dias_laborales: props.empleado.dias_laborales || [],
  salario: props.empleado.salario,
  comision_porcentaje: props.empleado.comision_porcentaje,
  fecha_ingreso: props.empleado.fecha_ingreso,
  fecha_salida: props.empleado.fecha_salida,
  estado: props.empleado.estado,
  especialidades: props.empleado.especialidades
})

function submit() {
  form.put(`/empleados/${props.empleado.id}`)
}
</script>

<template>
  <AdminLayout title="Editar Empleado">
    <div class="container-fluid px-0 px-md-3">

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h1 class="h4 brand-accent fw-bold mb-0">
          <i class="fas fa-user-edit me-2"></i> Editar Empleado #{{ empleado.id }}
        </h1>
        <Link href="/empleados" class="btn btn-light border rounded-pill px-3 shadow-sm text-muted fw-medium">
          <i class="fas fa-arrow-left me-1"></i> Volver al listado
        </Link>
      </div>

      <form @submit.prevent="submit">
        <div class="row g-4">

          <div class="col-lg-8">

            <div class="card shadow-sm border-0 rounded-4 mb-4">
              <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="mb-0 fw-bold text-dark">
                  <i class="fas fa-id-card brand-accent me-2"></i> Información Personal
                </h5>
              </div>
              <div class="card-body p-4">
                <div class="row g-3">

                  <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Nombre *</label>
                    <input
                      type="text"
                      v-model="form.nombre"
                      class="form-control form-control-lg bg-light border-0 fs-6"
                      :class="{ 'is-invalid': form.errors.nombre }"
                      required
                    >
                    <div v-if="form.errors.nombre" class="invalid-feedback">
                      {{ form.errors.nombre }}
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Apellido *</label>
                    <input
                      type="text"
                      v-model="form.apellido"
                      class="form-control form-control-lg bg-light border-0 fs-6"
                      :class="{ 'is-invalid': form.errors.apellido }"
                      required
                    >
                    <div v-if="form.errors.apellido" class="invalid-feedback">
                      {{ form.errors.apellido }}
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Email</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light border-0 text-muted"><i class="fas fa-envelope"></i></span>
                      <input
                        type="email"
                        v-model="form.email"
                        class="form-control form-control-lg bg-light border-0 fs-6 ps-0"
                        :class="{ 'is-invalid': form.errors.email }"
                      >
                      <div v-if="form.errors.email" class="invalid-feedback">
                        {{ form.errors.email }}
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Teléfono</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light border-0 text-muted"><i class="fas fa-phone"></i></span>
                      <input
                        type="text"
                        v-model="form.telefono"
                        class="form-control form-control-lg bg-light border-0 fs-6 ps-0"
                        :class="{ 'is-invalid': form.errors.telefono }"
                      >
                      <div v-if="form.errors.telefono" class="invalid-feedback">
                        {{ form.errors.telefono }}
                      </div>
                    </div>
                  </div>

                  <div class="col-12">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Dirección</label>
                    <textarea
                      v-model="form.direccion"
                      class="form-control form-control-lg bg-light border-0 fs-6"
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

            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="mb-0 fw-bold text-dark">
                  <i class="fas fa-briefcase text-info me-2"></i> Perfil Laboral
                </h5>
              </div>
              <div class="card-body p-4">
                <div class="row g-3">

                  <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Puesto *</label>
                    <input
                      type="text"
                      v-model="form.puesto"
                      class="form-control form-control-lg bg-light border-0 fs-6"
                      :class="{ 'is-invalid': form.errors.puesto }"
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

                  <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Estado *</label>
                    <select
                      v-model="form.estado"
                      class="form-select form-select-lg bg-light border-0 fs-6"
                      :class="{ 'is-invalid': form.errors.estado }"
                      required
                    >
                      <option value="activo">Activo</option>
                      <option value="inactivo">Inactivo</option>
                      <option value="vacaciones">Vacaciones</option>
                    </select>
                    <div v-if="form.errors.estado" class="invalid-feedback">
                      {{ form.errors.estado }}
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Fecha de Ingreso</label>
                    <input
                      type="date"
                      v-model="form.fecha_ingreso"
                      class="form-control form-control-lg bg-light border-0 fs-6"
                      :class="{ 'is-invalid': form.errors.fecha_ingreso }"
                      :max="new Date().toISOString().split('T')[0]"
                    >
                    <div v-if="form.errors.fecha_ingreso" class="invalid-feedback">
                      {{ form.errors.fecha_ingreso }}
                    </div>
                  </div>

                  <div class="col-md-6" v-if="form.estado === 'inactivo'">
                    <label class="form-label text-danger small fw-bold text-uppercase tracking-wide">Fecha de Salida</label>
                    <input
                      type="date"
                      v-model="form.fecha_salida"
                      class="form-control form-control-lg bg-light border-0 fs-6"
                      :class="{ 'is-invalid': form.errors.fecha_salida }"
                    >
                    <div v-if="form.errors.fecha_salida" class="invalid-feedback">
                      {{ form.errors.fecha_salida }}
                    </div>
                  </div>

                  <div class="col-12 mt-3">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Especialidades</label>
                    <textarea
                      v-model="form.especialidades"
                      class="form-control form-control-lg bg-light border-0 fs-6"
                      rows="2"
                      :class="{ 'is-invalid': form.errors.especialidades }"
                    ></textarea>
                    <div v-if="form.errors.especialidades" class="invalid-feedback">
                      {{ form.errors.especialidades }}
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
                  <i class="fas fa-clock text-warning me-2"></i> Disponibilidad
                </h5>
              </div>
              <div class="card-body p-4">

                <div class="mb-4">
                  <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Hora de Entrada</label>
                  <input
                    type="time"
                    v-model="form.hora_entrada"
                    class="form-control form-control-lg bg-light border-0 fs-6"
                    :class="{ 'is-invalid': form.errors.hora_entrada }"
                  >
                  <div v-if="form.errors.hora_entrada" class="invalid-feedback">
                    {{ form.errors.hora_entrada }}
                  </div>
                </div>

                <div class="mb-4">
                  <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Hora de Salida</label>
                  <input
                    type="time"
                    v-model="form.hora_salida"
                    class="form-control form-control-lg bg-light border-0 fs-6"
                    :class="{ 'is-invalid': form.errors.hora_salida }"
                  >
                  <div v-if="form.errors.hora_salida" class="invalid-feedback">
                    {{ form.errors.hora_salida }}
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label text-muted small fw-bold text-uppercase tracking-wide mb-3">Días Laborales</label>
                  <div class="d-flex flex-column gap-2">
                    <div
                      v-for="dia in dias_semana"
                      :key="dia"
                      class="form-check form-switch"
                    >
                      <input
                        class="form-check-input custom-switch"
                        type="checkbox"
                        role="switch"
                        :id="`dia-${dia}`"
                        :value="dia"
                        v-model="form.dias_laborales"
                      >
                      <label class="form-check-label ms-2 fw-medium" :for="`dia-${dia}`">
                        {{ dia.charAt(0).toUpperCase() + dia.slice(1) }}
                      </label>
                    </div>
                  </div>
                  <div v-if="form.errors.dias_laborales" class="text-danger small mt-2">
                    {{ form.errors.dias_laborales }}
                  </div>
                </div>

              </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="mb-0 fw-bold text-dark">
                  <i class="fas fa-money-bill-wave text-success me-2"></i> Compensación
                </h5>
              </div>
              <div class="card-body p-4">

                <div class="mb-4">
                  <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Salario Base</label>
                  <div class="input-group">
                    <span class="input-group-text bg-light border-0 text-muted">$</span>
                    <input
                      type="number"
                      v-model="form.salario"
                      class="form-control form-control-lg bg-light border-0 fs-6 ps-0"
                      :class="{ 'is-invalid': form.errors.salario }"
                      step="0.01"
                      min="0"
                    >
                    <div v-if="form.errors.salario" class="invalid-feedback">
                      {{ form.errors.salario }}
                    </div>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">Comisión (%)</label>
                  <div class="input-group">
                    <input
                      type="number"
                      v-model="form.comision_porcentaje"
                      class="form-control form-control-lg bg-light border-0 fs-6"
                      :class="{ 'is-invalid': form.errors.comision_porcentaje }"
                      step="0.01"
                      min="0"
                      max="100"
                    >
                    <span class="input-group-text bg-light border-0 text-muted"><i class="fas fa-percentage"></i></span>
                    <div v-if="form.errors.comision_porcentaje" class="invalid-feedback">
                      {{ form.errors.comision_porcentaje }}
                    </div>
                  </div>
                </div>

              </div>
            </div>

          </div>

        </div>

        <div class="d-flex justify-content-end gap-3 mt-4 mb-5">
          <Link href="/empleados" class="btn btn-light rounded-pill px-4 fw-medium shadow-sm">
            Cancelar
          </Link>
          <button
            type="submit"
            class="btn btn-brand rounded-pill px-4 fw-medium shadow-sm"
            :disabled="form.processing"
          >
            <span v-if="form.processing">
              <i class="fas fa-spinner fa-spin me-2"></i> Guardando...
            </span>
            <span v-else>
              <i class="fas fa-save me-2"></i> Actualizar Empleado
            </span>
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

/* Inputs y form controls */
.form-control:focus, .form-select:focus {
  box-shadow: 0 0 0 0.25rem rgba(216, 75, 114, 0.25);
  border-color: #d84b72;
  background-color: #fff !important;
}

/* Switches (Toggle) personalizados */
.custom-switch:checked {
  background-color: #d84b72;
  border-color: #d84b72;
}

.custom-switch:focus {
  box-shadow: 0 0 0 0.25rem rgba(216, 75, 114, 0.25);
  border-color: #d84b72;
}

.form-switch .form-check-input {
  width: 2.5em;
  height: 1.25em;
  cursor: pointer;
}

.form-check-label {
  cursor: pointer;
  padding-top: 0.15rem;
}

/* Hover de tarjetas */
.card {
  transition: box-shadow 0.2s ease;
}

.card:hover {
  box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
}
</style>
