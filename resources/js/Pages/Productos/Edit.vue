<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { computed } from 'vue'

const props = defineProps({
  producto: Object
})

const form = useForm({
  nombre: props.producto.nombre,
  codigo: props.producto.codigo,
  marca: props.producto.marca,
  descripcion: props.producto.descripcion,
  precio_venta: props.producto.precio_venta,
  precio_compra: props.producto.precio_compra,
  precio_mayoreo: props.producto.precio_mayoreo,
  stock: props.producto.stock,
  stock_minimo: props.producto.stock_minimo,
  categoria: props.producto.categoria,
  unidad_medida: props.producto.unidad_medida,
  activo: props.producto.activo
})

const margenGanancia = computed(() => {
  if (form.precio_compra && form.precio_venta && parseFloat(form.precio_compra) > 0) {
    const margen = ((parseFloat(form.precio_venta) - parseFloat(form.precio_compra)) / parseFloat(form.precio_compra)) * 100
    return margen.toFixed(2)
  }
  return null
})

function submit() {
  form.put(`/productos/${props.producto.id}`)
}
</script>

<template>
  <AdminLayout title="Editar Producto">
    <div class="container-fluid px-3">

      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 text-primary fw-bold">
          <i class="fas fa-edit me-2"></i> Editar Producto #{{ producto.id }}
        </h1>
        <a href="/productos" class="btn btn-secondary">
          <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
      </div>

      <!-- Formulario -->
      <form @submit.prevent="submit">
        <div class="row g-3">

          <!-- Información Básica -->
          <div class="col-12">
            <div class="card shadow-sm border-0">
              <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                  <i class="fas fa-info-circle me-2"></i> Información Básica
                </h5>
              </div>
              <div class="card-body">
                <div class="row g-3">

                  <!-- Código -->
                  <div class="col-md-4">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-barcode text-primary me-2"></i> Código *
                    </label>
                    <input 
                      type="text" 
                      v-model="form.codigo" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.codigo }"
                      required
                    >
                    <div v-if="form.errors.codigo" class="invalid-feedback">
                      {{ form.errors.codigo }}
                    </div>
                  </div>

                  <!-- Nombre -->
                  <div class="col-md-8">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-box text-success me-2"></i> Nombre del Producto *
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

                  <!-- Marca -->
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-tag text-info me-2"></i> Marca
                    </label>
                    <input 
                      type="text" 
                      v-model="form.marca" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.marca }"
                      list="marcas-list"
                    >
                    <datalist id="marcas-list">
                      <option value="Suavecito"></option>
                      <option value="Head & Shoulders"></option>
                      <option value="Pantene"></option>
                      <option value="Dove"></option>
                      <option value="American Crew"></option>
                      <option value="Gatsby"></option>
                    </datalist>
                    <div v-if="form.errors.marca" class="invalid-feedback">
                      {{ form.errors.marca }}
                    </div>
                  </div>

                  <!-- Categoría -->
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-folder text-warning me-2"></i> Categoría
                    </label>
                    <input 
                      type="text" 
                      v-model="form.categoria" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.categoria }"
                      list="categorias-list"
                    >
                    <datalist id="categorias-list">
                      <option value="Styling"></option>
                      <option value="Cuidado"></option>
                      <option value="Barba"></option>
                      <option value="Tintes"></option>
                      <option value="Tratamientos"></option>
                      <option value="Herramientas"></option>
                    </datalist>
                    <div v-if="form.errors.categoria" class="invalid-feedback">
                      {{ form.errors.categoria }}
                    </div>
                  </div>

                  <!-- Descripción -->
                  <div class="col-12">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-align-left text-secondary me-2"></i> Descripción
                    </label>
                    <textarea 
                      v-model="form.descripcion" 
                      class="form-control" 
                      rows="3"
                      :class="{ 'is-invalid': form.errors.descripcion }"
                    ></textarea>
                    <div v-if="form.errors.descripcion" class="invalid-feedback">
                      {{ form.errors.descripcion }}
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>

          <!-- Precios -->
          <div class="col-md-6">
            <div class="card shadow-sm border-0">
              <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                  <i class="fas fa-dollar-sign me-2"></i> Precios
                </h5>
              </div>
              <div class="card-body">
                <div class="row g-3">

                  <!-- Precio Venta -->
                  <div class="col-12">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-money-bill-wave text-success me-2"></i> Precio de Venta *
                    </label>
                    <input 
                      type="number" 
                      v-model="form.precio_venta" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.precio_venta }"
                      step="0.01"
                      min="0"
                      required
                    >
                    <div v-if="form.errors.precio_venta" class="invalid-feedback">
                      {{ form.errors.precio_venta }}
                    </div>
                  </div>

                  <!-- Precio Compra -->
                  <div class="col-12">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-shopping-cart text-primary me-2"></i> Precio de Compra
                    </label>
                    <input 
                      type="number" 
                      v-model="form.precio_compra" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.precio_compra }"
                      step="0.01"
                      min="0"
                    >
                    <div v-if="form.errors.precio_compra" class="invalid-feedback">
                      {{ form.errors.precio_compra }}
                    </div>
                  </div>

                  <!-- Precio Mayoreo -->
                  <div class="col-12">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-boxes text-info me-2"></i> Precio Mayoreo
                    </label>
                    <input 
                      type="number" 
                      v-model="form.precio_mayoreo" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.precio_mayoreo }"
                      step="0.01"
                      min="0"
                    >
                    <div v-if="form.errors.precio_mayoreo" class="invalid-feedback">
                      {{ form.errors.precio_mayoreo }}
                    </div>
                  </div>

                  <!-- Margen de Ganancia -->
                  <div class="col-12" v-if="margenGanancia">
                    <div class="alert alert-info mb-0">
                      <strong>Margen de Ganancia:</strong> {{ margenGanancia }}%
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>

          <!-- Inventario -->
          <div class="col-md-6">
            <div class="card shadow-sm border-0">
              <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                  <i class="fas fa-warehouse me-2"></i> Inventario
                </h5>
              </div>
              <div class="card-body">
                <div class="row g-3">

                  <!-- Stock Actual -->
                  <div class="col-12">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-boxes text-primary me-2"></i> Stock Actual *
                    </label>
                    <input 
                      type="number" 
                      v-model="form.stock" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.stock }"
                      min="0"
                      required
                    >
                    <div v-if="form.errors.stock" class="invalid-feedback">
                      {{ form.errors.stock }}
                    </div>
                  </div>

                  <!-- Stock Mínimo -->
                  <div class="col-12">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-exclamation-triangle text-warning me-2"></i> Stock Mínimo *
                    </label>
                    <input 
                      type="number" 
                      v-model="form.stock_minimo" 
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.stock_minimo }"
                      min="0"
                      required
                    >
                    <div v-if="form.errors.stock_minimo" class="invalid-feedback">
                      {{ form.errors.stock_minimo }}
                    </div>
                  </div>

                  <!-- Unidad de Medida -->
                  <div class="col-12">
                    <label class="form-label fw-semibold">
                      <i class="fas fa-ruler text-info me-2"></i> Unidad de Medida *
                    </label>
                    <select 
                      v-model="form.unidad_medida" 
                      class="form-select"
                      :class="{ 'is-invalid': form.errors.unidad_medida }"
                      required
                    >
                      <option value="pieza">Pieza</option>
                      <option value="caja">Caja</option>
                      <option value="paquete">Paquete</option>
                      <option value="ml">Mililitros (ml)</option>
                      <option value="gr">Gramos (gr)</option>
                      <option value="kg">Kilogramos (kg)</option>
                      <option value="litro">Litro</option>
                    </select>
                    <div v-if="form.errors.unidad_medida" class="invalid-feedback">
                      {{ form.errors.unidad_medida }}
                    </div>
                  </div>

                  <!-- Estado -->
                  <div class="col-12">
                    <div class="form-check form-switch">
                      <input 
                        class="form-check-input" 
                        type="checkbox" 
                        v-model="form.activo"
                        id="activo"
                      >
                      <label class="form-check-label fw-semibold" for="activo">
                        <i class="fas fa-toggle-on text-success me-2"></i>
                        Producto Activo
                      </label>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Botones -->
        <div class="d-flex justify-content-end gap-2 mt-4">
          <a href="/productos" class="btn btn-secondary">
            <i class="fas fa-times me-2"></i> Cancelar
          </a>
          <button 
            type="submit" 
            class="btn btn-primary"
            :disabled="form.processing"
          >
            <i class="fas fa-save me-2"></i>
            {{ form.processing ? 'Guardando...' : 'Actualizar Producto' }}
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