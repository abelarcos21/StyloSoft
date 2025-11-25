<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
  servicios: Object,
  filters: Object,
  categorias: Array
})

const search = ref(props.filters.search || '')
const categoria = ref(props.filters.categoria || '')
const activo = ref(props.filters.activo || '')
const duracion = ref(props.filters.duracion || '')

function filtrar() {
  router.get('/servicios', {
    search: search.value,
    categoria: categoria.value,
    activo: activo.value,
    duracion: duracion.value
  }, {
    preserveState: true,
    replace: true,
  })
}

function eliminarServicio(id) {
  Swal.fire({
    title: "¿Eliminar servicio?",
    text: "Esta acción no se puede deshacer",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then(result => {
    if (result.isConfirmed) {
      router.delete(`/servicios/${id}`, {
        onSuccess: () => {
          Swal.fire("Eliminado", "Servicio eliminado exitosamente", "success")
        },
        onError: () => {
          Swal.fire("Error", "No se pudo eliminar el servicio", "error")
        }
      })
    }
  })
}

function duplicarServicio(id) {
  Swal.fire({
    title: "¿Duplicar servicio?",
    text: "Se creará una copia del servicio",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, duplicar",
    cancelButtonText: "Cancelar",
  }).then(result => {
    if (result.isConfirmed) {
      router.post(`/servicios/${id}/duplicar`, {}, {
        onSuccess: () => {
          Swal.fire("Duplicado", "Servicio duplicado exitosamente", "success")
        }
      })
    }
  })
}

function exportarServicios() {
  window.location.href = '/servicios-exportar?' + new URLSearchParams({
    search: search.value,
    categoria: categoria.value,
    activo: activo.value,
    duracion: duracion.value
  })
}

function getDuracionClass(minutos) {
  if (minutos <= 30) return 'badge bg-success'
  if (minutos <= 60) return 'badge bg-warning text-dark'
  return 'badge bg-danger'
}
</script>

<template>
  <AdminLayout title="Servicios">
    <div class="container-fluid px-3">

      <!-- Header -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
        <h1 class="h4 text-pink fw-bold">
          <i class="fas fa-cut me-2"></i> Gestionar Servicios
        </h1>
        <div class="btn-group">
          <button @click="exportarServicios" class="btn btn-success">
            <i class="fas fa-file-excel me-2"></i> Exportar
          </button>
          <a href="/servicios/create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Nuevo Servicio
          </a>
        </div>
      </div>

      <!-- Filtros -->
      <div class="card shadow-sm border-0 mb-3">
        <div class="card-body row g-2">

          <div class="col-md-3">
            <div class="input-group">
              <span class="input-group-text bg-light">
                <i class="fas fa-search"></i>
              </span>
              <input 
                class="form-control" 
                v-model="search" 
                @input="filtrar" 
                placeholder="Buscar servicio..."
              >
            </div>
          </div>

          <div class="col-md-2">
            <select v-model="categoria" @change="filtrar" class="form-select">
              <option value="">Todas las categorías</option>
              <option v-for="cat in categorias" :key="cat" :value="cat">{{ cat }}</option>
            </select>
          </div>

          <div class="col-md-2">
            <select v-model="duracion" @change="filtrar" class="form-select">
              <option value="">Todas las duraciones</option>
              <option value="corta">Corta (≤30 min)</option>
              <option value="media">Media (31-60 min)</option>
              <option value="larga">Larga (>60 min)</option>
            </select>
          </div>

          <div class="col-md-2">
            <select v-model="activo" @change="filtrar" class="form-select">
              <option value="">Todos</option>
              <option value="1">Activos</option>
              <option value="0">Inactivos</option>
            </select>
          </div>

          <div class="col-md-3">
            <button @click="search = ''; categoria = ''; activo = ''; duracion = ''; filtrar()" class="btn btn-secondary w-100">
              <i class="fas fa-redo me-2"></i> Limpiar Filtros
            </button>
          </div>

        </div>
      </div>

      <!-- Tabla -->
      <div class="card shadow-sm border-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="text-center">Orden</th>
                <th>Servicio</th>
                <th>Categoría</th>
                <th class="text-center">Duración</th>
                <th class="text-end">Precio</th>
                <th class="text-center">Depósito</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="servicio in servicios.data" :key="servicio.id">
                
                <!-- ORDEN -->
                <td class="text-center">
                  <span class="badge bg-secondary">
                    {{ servicio.orden }}
                  </span>
                </td>

                <!-- SERVICIO -->
                <td>
                  <div>
                    <strong>{{ servicio.nombre }}</strong>
                    <div v-if="servicio.descripcion">
                      <small class="text-muted">
                        {{ servicio.descripcion.substring(0, 60) }}{{ servicio.descripcion.length > 60 ? '...' : '' }}
                      </small>
                    </div>
                  </div>
                </td>

                <!-- CATEGORÍA -->
                <td>
                  <span v-if="servicio.categoria" class="badge bg-info">
                    {{ servicio.categoria }}
                  </span>
                  <span v-else class="text-muted">-</span>
                </td>

                <!-- DURACIÓN -->
                <td class="text-center">
                  <span :class="getDuracionClass(servicio.duracion_minutos)">
                    <i class="fas fa-clock fa-xs me-1"></i>{{ servicio.duracion_minutos }} min
                  </span>
                </td>

                <!-- PRECIO -->
                <td class="text-end">
                  <strong class="text-success">${{ servicio.precio }}</strong>
                </td>

                <!-- DEPÓSITO -->
                <td class="text-center">
                  <div v-if="servicio.requiere_deposito">
                    <span class="badge bg-warning text-dark">
                      <i class="fas fa-dollar-sign fa-xs"></i> Requiere
                    </span>
                    <div v-if="servicio.deposito_minimo">
                      <small class="text-muted">${{ servicio.deposito_minimo }}</small>
                    </div>
                  </div>
                  <span v-else class="text-muted">-</span>
                </td>

                <!-- ESTADO -->
                <td class="text-center">
                  <span 
                    class="badge px-3 py-2"
                    :class="servicio.activo ? 'bg-success' : 'bg-secondary'"
                  >
                    {{ servicio.activo ? 'ACTIVO' : 'INACTIVO' }}
                  </span>
                </td>

                <!-- ACCIONES -->
                <td class="text-center">
                  <div class="btn-group">
                    <a 
                      :href="`/servicios/${servicio.id}`" 
                      class="btn btn-sm btn-outline-info"
                      title="Ver detalles"
                    >
                      <i class="fas fa-eye"></i>
                    </a>
                    <a 
                      :href="`/servicios/${servicio.id}/edit`" 
                      class="btn btn-sm btn-outline-warning"
                      title="Editar"
                    >
                      <i class="fas fa-edit"></i>
                    </a>
                    <button 
                      @click="duplicarServicio(servicio.id)" 
                      class="btn btn-sm btn-outline-primary"
                      title="Duplicar"
                    >
                      <i class="fas fa-copy"></i>
                    </button>
                    <button 
                      @click="eliminarServicio(servicio.id)" 
                      class="btn btn-sm btn-outline-danger"
                      title="Eliminar"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>

              </tr>

              <tr v-if="!servicios.data.length">
                <td colspan="8" class="text-center text-muted py-5">
                  <i class="fas fa-scissors fa-3x mb-3 d-block"></i>
                  <p class="mb-0">No hay servicios registrados con los filtros seleccionados.</p>
                </td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>

      <!-- Paginación -->
      <nav class="mt-4" v-if="servicios.data.length">
        <ul class="pagination justify-content-center flex-wrap">
          <li
            v-for="(link, i) in props.servicios.links"
            :key="i"
            class="page-item"
            :class="{ active: link.active, disabled: !link.url }"
          >
            <button
              class="page-link"
              v-html="link.label"
              @click="link.url && router.get(link.url, {}, { preserveState: true })"
            />
          </li>
        </ul>
      </nav>

    </div>
  </AdminLayout>
</template>

<style scoped>
.badge {
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.btn-group .btn {
  padding: 0.25rem 0.5rem;
}

.table tbody tr {
  transition: background-color 0.2s;
}

.table tbody tr:hover {
  background-color: rgba(0, 123, 255, 0.05);
}
</style>