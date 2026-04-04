<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { computed } from 'vue'

const props = defineProps({
  codigo_sugerido: String
})

const form = useForm({
  nombre: '',
  codigo: props.codigo_sugerido,
  marca: '',
  descripcion: '',
  precio_venta: '',
  precio_compra: '',
  precio_mayoreo: '',
  stock: 0,
  stock_minimo: 5,
  categoria: '',
  unidad_medida: 'pieza',
  activo: true
})

const margenGanancia = computed(() => {
  if (form.precio_compra && form.precio_venta && parseFloat(form.precio_compra) > 0) {
    const margen = ((parseFloat(form.precio_venta) - parseFloat(form.precio_compra)) / parseFloat(form.precio_compra)) * 100
    return margen.toFixed(2)
  }
  return null
})

function submit() {
  form.post('/productos', {
    onSuccess: () => form.reset()
  })
}
</script>

<template>
  <AdminLayout title="Nuevo Producto">
    <div class="container-fluid px-0 px-md-3">

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h1 class="h4 brand-accent fw-bold mb-0">
          <i class="fas fa-box-open me-2"></i> Nuevo Producto
        </h1>
        <Link href="/productos" class="btn btn-light border rounded-pill px-4 shadow-sm text-muted fw-medium">
          <i class="fas fa-arrow-left me-1"></i> Volver
        </Link>
      </div>

      <form @submit.prevent="submit" class="mb-5">
        <div class="row g-4">

          <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="mb-0 fw-bold text-dark">
                  <i class="fas fa-info-circle text-primary me-2"></i> Información Básica
                </h5>
              </div>
              <div class="card-body p-4">
                <div class="row g-4">

                  <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                      <i class="fas fa-barcode text-primary me-1"></i> Código *
                    </label>
                    <input
                      type="text"
                      v-model="form.codigo"
                      class="form-control form-control-lg fs-6"
                      :class="{ 'is-invalid': form.errors.codigo }"
                      placeholder="PROD-001"
                      required
                    >
                    <div v-if="form.errors.codigo" class="invalid-feedback">
                      {{ form.errors.codigo }}
                    </div>
                  </div>

                  <div class="col-md-8">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                      <i class="fas fa-box text-success me-1"></i> Nombre del Producto *
                    </label>
                    <input
                      type="text"
                      v-model="form.nombre"
                      class="form-control form-control-lg fs-6"
                      :class="{ 'is-invalid': form.errors.nombre }"
                      placeholder="Ej: Shampoo Anticaspa 400ml"
                      required
                    >
                    <div v-if="form.errors.nombre" class="invalid-feedback">
                      {{ form.errors.nombre }}
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                      <i class="fas fa-tag text-info me-1"></i> Marca
                    </label>
                    <input
                      type="text"
                      v-model="form.marca"
                      class="form-control form-control-lg fs-6"
                      :class="{ 'is-invalid': form.errors.marca }"
                      placeholder="Ej: Head & Shoulders"
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

                  <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                      <i class="fas fa-folder text-warning me-1"></i> Categoría
                    </label>
                    <input
                      type="text"
                      v-model="form.categoria"
                      class="form-control form-control-lg fs-6"
                      :class="{ 'is-invalid': form.errors.categoria }"
                      placeholder="Ej: Styling, Cuidado, Barba"
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

                  <div class="col-12">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                      <i class="fas fa-align-left text-secondary me-1"></i> Descripción
                    </label>
                    <textarea
                      v-model="form.descripcion"
                      class="form-control"
                      rows="3"
                      :class="{ 'is-invalid': form.errors.descripcion }"
                      placeholder="Descripción detallada del producto..."
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
                  <i class="fas fa-dollar-sign text-success me-2"></i> Precios
                </h5>
              </div>
              <div class="card-body p-4">
                <div class="row g-4">

                  <div class="col-12">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                      <i class="fas fa-money-bill-wave text-success me-1"></i> Precio de Venta *
                    </label>
                    <div class="input-group input-group-lg">
                      <span class="input-group-text bg-light border-end-0">$</span>
                      <input
                        type="number"
                        v-model="form.precio_venta"
                        class="form-control border-start-0 ps-0 fs-6"
                        :class="{ 'is-invalid': form.errors.precio_venta }"
                        placeholder="0.00"
                        step="0.01"
                        min="0"
                        required
                      >
                      <div v-if="form.errors.precio_venta" class="invalid-feedback">
                        {{ form.errors.precio_venta }}
                      </div>
                    </div>
                  </div>

                  <div class="col-12">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                      <i class="fas fa-shopping-cart text-primary me-1"></i> Precio de Compra
                    </label>
                    <div class="input-group">
                      <span class="input-group-text bg-light border-end-0">$</span>
                      <input
                        type="number"
                        v-model="form.precio_compra"
                        class="form-control border-start-0 ps-0"
                        :class="{ 'is-invalid': form.errors.precio_compra }"
                        placeholder="0.00"
                        step="0.01"
                        min="0"
                      >
                      <div v-if="form.errors.precio_compra" class="invalid-feedback">
                        {{ form.errors.precio_compra }}
                      </div>
                    </div>
                  </div>

                  <div class="col-12">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                      <i class="fas fa-boxes text-info me-1"></i> Precio Mayoreo
                    </label>
                    <div class="input-group">
                      <span class="input-group-text bg-light border-end-0">$</span>
                      <input
                        type="number"
                        v-model="form.precio_mayoreo"
                        class="form-control border-start-0 ps-0"
                        :class="{ 'is-invalid': form.errors.precio_mayoreo }"
                        placeholder="0.00"
                        step="0.01"
                        min="0"
                      >
                      <div v-if="form.errors.precio_mayoreo" class="invalid-feedback">
                        {{ form.errors.precio_mayoreo }}
                      </div>
                    </div>
                    <small class="text-muted mt-1 d-block">Para ventas al por mayor</small>
                  </div>

                  <div class="col-12" v-if="margenGanancia">
                    <div class="alert bg-brand-light border-0 brand-accent mb-0 rounded-3 d-flex align-items-center">
                      <i class="fas fa-chart-line fa-lg me-3"></i>
                      <div>
                        <strong>Margen de Ganancia Estimado:</strong>
                        <span class="fs-5 ms-1 fw-bold">{{ margenGanancia }}%</span>
                      </div>
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
                  <i class="fas fa-warehouse text-warning me-2"></i> Inventario
                </h5>
              </div>
              <div class="card-body p-4">
                <div class="row g-4">

                  <div class="col-12">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                      <i class="fas fa-layer-group text-primary me-1"></i> Stock Actual *
                    </label>
                    <input
                      type="number"
                      v-model="form.stock"
                      class="form-control form-control-lg fs-6"
                      :class="{ 'is-invalid': form.errors.stock }"
                      placeholder="0"
                      min="0"
                      required
                    >
                    <div v-if="form.errors.stock" class="invalid-feedback">
                      {{ form.errors.stock }}
                    </div>
                  </div>

                  <div class="col-12">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                      <i class="fas fa-exclamation-triangle text-warning me-1"></i> Stock Mínimo *
                    </label>
                    <input
                      type="number"
                      v-model="form.stock_minimo"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.stock_minimo }"
                      placeholder="5"
                      min="0"
                      required
                    >
                    <div v-if="form.errors.stock_minimo" class="invalid-feedback">
                      {{ form.errors.stock_minimo }}
                    </div>
                    <small class="text-muted mt-1 d-block">Alerta cuando el stock llegue a este nivel</small>
                  </div>

                  <div class="col-12">
                    <label class="form-label text-muted small fw-bold text-uppercase tracking-wide">
                      <i class="fas fa-ruler text-info me-1"></i> Unidad de Medida *
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

                  <div class="col-12 pt-3">
                    <div class="bg-light p-3 rounded-3">
                      <div class="form-check form-switch mb-1">
                        <input
                          class="form-check-input brand-switch"
                          type="checkbox"
                          v-model="form.activo"
                          id="activo"
                        >
                        <label class="form-check-label fw-bold ms-2" for="activo" :class="form.activo ? 'text-success' : 'text-muted'">
                          {{ form.activo ? 'Producto Activo' : 'Producto Inactivo' }}
                        </label>
                      </div>
                      <small class="text-muted ms-5">Los productos inactivos no aparecerán en ventas</small>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="d-flex justify-content-end gap-3 mt-4">
          <Link href="/productos" class="btn btn-light border rounded-pill px-4 shadow-sm fw-medium text-muted">
            <i class="fas fa-times me-1"></i> Cancelar
          </Link>
          <button
            type="submit"
            class="btn btn-brand rounded-pill px-5 shadow-sm fw-medium text-white"
            :disabled="form.processing"
          >
            <i v-if="form.processing" class="fas fa-spinner fa-spin me-2"></i>
            <i v-else class="fas fa-save me-2"></i>
            {{ form.processing ? 'Guardando...' : 'Guardar Producto' }}
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
  border-color: #d84b72;
  transition: all 0.2s ease;
}

.btn-brand:hover {
  background-color: #c03e61;
  border-color: #c03e61;
  transform: translateY(-1px);
}

.btn-brand:disabled {
  background-color: #e58ca4;
  border-color: #e58ca4;
  transform: none;
}

/* Switch personalizado */
.brand-switch:checked {
  background-color: #28a745;
  border-color: #28a745;
}

/* Tipografía de apoyo */
.tracking-wide {
  letter-spacing: 0.05em;
}

/* Estilos de Tarjetas e Inputs */
.card {
  transition: box-shadow 0.2s ease;
}

.card:hover {
  box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
}

.form-control:focus, .form-select:focus {
  border-color: #d84b72;
  box-shadow: 0 0 0 0.25rem rgba(216, 75, 114, 0.25);
}

.input-group-text {
  color: #6c757d;
  font-weight: 500;
}
</style>
