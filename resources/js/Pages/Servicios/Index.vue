<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'

const props = defineProps({
  servicios: Object, // con paginación
  filters: Object
})

// Filtros reactivos
const search = ref(props.filters.search || '')
const estado = ref(props.filters.estado || '')

function filtrar() {
  router.get('/servicios', { search: search.value, estado: estado.value }, {
    preserveState: true,
    replace: true,
  })
}

function eliminarServicio(id) {
  if (confirm('¿Deseas eliminar este servicio?')) {
    router.delete(`/servicios/${id}`)
  }
}
</script>

<template>
  <AdminLayout title="Servicios">
    <div class="container-fluid px-3">

      <!-- Encabezado -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
        <h1 class="h4 mb-0 text-primary fw-bold">
          <i class="fas fa-hand-sparkles me-2"></i> Servicios
        </h1>
        <a href="/servicios/create" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
          <i class="fas fa-plus"></i> Nuevo servicio
        </a>
      </div>

      <!-- Filtros -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <div class="row g-2">
            <div class="col-12 col-md-6 col-lg-4">
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                <input
                  type="search"
                  v-model="search"
                  @input="filtrar"
                  class="form-control"
                  placeholder="Buscar por nombre o descripción..."
                />
              </div>
            </div>
            <div class="col-12 col-md-4 col-lg-3">
              <select v-model="estado" @change="filtrar" class="form-select">
                <option value="">Todos los estados</option>
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabla -->
      <div class="card border-0 shadow-sm">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-center">
              <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Duración (min)</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="servicio in props.servicios.data"
                :key="servicio.id"
                class="text-center"
              >
                <td>{{ servicio.id }}</td>
                <td class="text-start">
                  <strong>{{ servicio.nombre }}</strong><br />
                  <small class="text-muted">{{ servicio.descripcion || 'Sin descripción' }}</small>
                </td>
                <td>{{ servicio.descripcion || '-' }}</td>
                <td>${{ Number(servicio.precio ?? 0).toFixed(2) }}</td>
                <td>{{ servicio.duracion_minutos }}</td>
                <td>
                  <span
                    class="badge px-3 py-2 rounded-pill"
                    :class="servicio.activo ? 'bg-success' : 'bg-danger'"
                  >
                    {{ servicio.activo ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <a
                      :href="`/servicios/${servicio.id}/edit`"
                      class="btn btn-sm btn-outline-warning"
                      title="Editar"
                    >
                      <i class="fas fa-edit"></i>
                    </a>
                    <button
                      @click.prevent="eliminarServicio(servicio.id)"
                      class="btn btn-sm btn-outline-danger"
                      title="Eliminar"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="!props.servicios.data.length">
                <td colspan="7" class="text-center text-muted py-4">
                  <i class="fas fa-concierge-bell fa-2x mb-2"></i><br />
                  No hay servicios registrados.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Paginación -->
      <nav class="mt-4">
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
