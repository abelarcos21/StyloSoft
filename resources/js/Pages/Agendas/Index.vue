<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'

const props = defineProps({
  agendas: Object, // con paginación
  filters: Object
})

// Filtros reactivos
const search = ref(props.filters.search || '')
const estado = ref(props.filters.estado || '')

function filtrar() {
  router.get('/agendas', { search: search.value, estado: estado.value }, {
    preserveState: true,
    replace: true,
  })
}

function eliminarAgenda(id) {
  if (confirm('¿Deseas eliminar esta agenda?')) {
    router.delete(`/agendas/${id}`)
  }
}
</script>

<template>
  <AdminLayout title="Agendas">
    <div class="container-fluid px-3">

      <!-- Encabezado -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
        <h1 class="h4 mb-0 text-primary fw-bold">
          <i class="fas fa-calendar-alt me-2"></i> Agendas
        </h1>
        <a href="/agendas/create" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
          <i class="fas fa-plus"></i> Nueva Agenda
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
                  placeholder="Buscar por cliente o empleado..."
                />
              </div>
            </div>
            <div class="col-12 col-md-4 col-lg-3">
              <select v-model="estado" @change="filtrar" class="form-select">
                <option value="">Todos los estados</option>
                <option value="pendiente">Pendiente</option>
                <option value="confirmada">Confirmada</option>
                <option value="completada">Completada</option>
                <option value="cancelada">Cancelada</option>
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
                <th>Cliente</th>
                <th>Empleado</th>
                <th>Servicio</th>
                <th>Fecha y Hora</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="agenda in props.agendas.data" :key="agenda.id" class="text-center">
                <td>{{ agenda.id }}</td>
                <td class="text-start">
                  <strong>{{ agenda.cliente.nombre }} {{ agenda.cliente.apellido }}</strong><br>
                  <small class="text-muted">{{ agenda.cliente.telefono || 'Sin teléfono' }}</small>
                </td>
                <td class="text-start">
                  <strong>{{ agenda.empleado.nombre }} {{ agenda.empleado.apellido }}</strong><br>
                  <small class="text-muted">{{ agenda.empleado.puesto }}</small>
                </td>
                <td>{{ agenda.servicio.nombre }}</td>
                <td>{{ new Date(agenda.fecha_hora).toLocaleString() }}</td>
                <td>
                  <span
                    class="badge px-3 py-2 rounded-pill"
                    :class="{
                      'bg-warning': agenda.estado === 'pendiente',
                      'bg-info': agenda.estado === 'confirmada',
                      'bg-success': agenda.estado === 'completada',
                      'bg-danger': agenda.estado === 'cancelada'
                    }"
                  >
                    {{ agenda.estado }}
                  </span>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <a :href="`/agendas/${agenda.id}/edit`" class="btn btn-sm btn-outline-warning" title="Editar">
                      <i class="fas fa-edit"></i>
                    </a>
                    <a :href="`/agendas/${agenda.id}`" class="btn btn-sm btn-outline-info" title="Ver detalle">
                      <i class="fas fa-eye"></i>
                    </a>
                    <button @click.prevent="eliminarAgenda(agenda.id)" class="btn btn-sm btn-outline-danger" title="Eliminar">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="!props.agendas.data.length">
                <td colspan="7" class="text-center text-muted py-4">
                  <i class="fas fa-calendar-check fa-2x mb-2"></i><br />
                  No hay agendas registradas.
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
            v-for="(link, i) in props.agendas.links"
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
