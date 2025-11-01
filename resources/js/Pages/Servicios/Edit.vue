<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  servicio: Object
})

const form = ref({
  nombre: props.servicio.nombre,
  descripcion: props.servicio.descripcion,
  precio: props.servicio.precio,
  duracion_minutos: props.servicio.duracion_minutos,
  activo: props.servicio.activo
})

function submit() {
  router.put(`/servicios/${props.servicio.id}`, form.value)
}
</script>

<template>
  <AdminLayout title="Editar Servicio">
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
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
