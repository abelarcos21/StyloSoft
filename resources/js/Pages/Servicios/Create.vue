<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

const form = ref({
  nombre: '',
  descripcion: '',
  precio: 0,
  duracion_minutos: 30,
  activo: true
})

function submit() {
  router.post('/servicios', form.value)
}
</script>

<template>
  <AdminLayout title="Crear Servicio">
    <div class="card">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="mb-3">
            <label>Nombre</label>
            <input v-model="form.nombre" type="text" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Descripción</label>
            <textarea v-model="form.descripcion" class="form-control"></textarea>
          </div>
          <div class="mb-3">
            <label>Precio</label>
            <input v-model.number="form.precio" type="number" step="0.01" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Duración (minutos)</label>
            <input v-model.number="form.duracion_minutos" type="number" class="form-control" required>
          </div>
          <div class="mb-3 form-check">
            <input v-model="form.activo" type="checkbox" class="form-check-input" id="activo">
            <label class="form-check-label" for="activo">Activo</label>
          </div>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
