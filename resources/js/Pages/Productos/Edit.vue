<template>
  <AdminLayout>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="mb-0"><i class="fas fa-edit"></i> Editar Producto</h2>
      <Link href="/productos" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
      </Link>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <form @submit.prevent="actualizarProducto">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Nombre</label>
              <input v-model="form.nombre" type="text" class="form-control" required>
              <div v-if="errors.nombre" class="text-danger">{{ errors.nombre }}</div>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Código</label>
              <input v-model="form.codigo" type="text" class="form-control" required>
              <div v-if="errors.codigo" class="text-danger">{{ errors.codigo }}</div>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Precio Venta</label>
              <input v-model="form.precio_venta" type="number" step="0.01" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Precio Compra</label>
              <input v-model="form.precio_compra" type="number" step="0.01" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Stock</label>
              <input v-model="form.stock" type="number" class="form-control" min="0">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Estado</label>
              <select v-model="form.activo" class="form-control">
                <option :value="true">Activo</option>
                <option :value="false">Inactivo</option>
              </select>
            </div>

            <div class="col-12 mb-3">
              <label class="form-label">Descripción</label>
              <textarea v-model="form.descripcion" class="form-control" rows="3"></textarea>
            </div>
          </div>

          <div class="text-end">
            <button type="submit" class="btn btn-success">
              <i class="fas fa-save"></i> Actualizar
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
  producto: Object,
})

const form = useForm({
  nombre: props.producto.nombre,
  codigo: props.producto.codigo,
  descripcion: props.producto.descripcion,
  precio_venta: props.producto.precio_venta,
  precio_compra: props.producto.precio_compra,
  stock: props.producto.stock,
  activo: props.producto.activo,
})

const errors = form.errors

function actualizarProducto() {
  form.put(`/productos/${props.producto.id}`)
}
</script>
