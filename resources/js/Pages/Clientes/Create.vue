<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

const form = ref({
  nombre: '',
  apellido: '',
  email: '',
  telefono: '',
  direccion: ''
})

function submit() {
  router.post('/clientes', form.value)
}
</script>

<template>
  <AdminLayout title="Crear Cliente">
    <div class="card">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="mb-3">
            <label>Nombre</label>
            <input v-model="form.nombre" type="text" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Apellido</label>
            <input v-model="form.apellido" type="text" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input v-model="form.email" type="email" class="form-control">
          </div>
          <div class="mb-3">
            <label>Teléfono</label>
            <input v-model="form.telefono" type="text" class="form-control">
          </div>
          <div class="mb-3">
            <label>Dirección</label>
            <input v-model="form.direccion" type="text" class="form-control">
          </div>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
