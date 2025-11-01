<template>
  <AdminLayout>
    <h2>Editar Empleado</h2>

    <form @submit.prevent="submit">
      <div class="mb-3">
        <label>Nombre</label>
        <input v-model="form.nombre" type="text" class="form-control">
      </div>
      <div class="mb-3">
        <label>Apellido</label>
        <input v-model="form.apellido" type="text" class="form-control">
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
      <div class="mb-3">
        <label>Puesto</label>
        <input v-model="form.puesto" type="text" class="form-control">
      </div>
      <div class="mb-3">
        <label>Salario</label>
        <input v-model="form.salario" type="number" step="0.01" class="form-control">
      </div>
      <div class="mb-3">
        <label>Fecha de ingreso</label>
        <input v-model="form.fecha_ingreso" type="date" class="form-control">
      </div>
      <div class="mb-3">
        <label>Estado</label>
        <select v-model="form.estado" class="form-control">
          <option value="activo">Activo</option>
          <option value="inactivo">Inactivo</option>
        </select>
      </div>

      <button class="btn btn-primary">Actualizar</button>
      <Link href="/empleados" class="btn btn-secondary">Cancelar</Link>
    </form>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  empleado: Object
})

const form = useForm({ ...props.empleado })

function submit() {
  form.put(`/empleados/${props.empleado.id}`)
}
</script>
