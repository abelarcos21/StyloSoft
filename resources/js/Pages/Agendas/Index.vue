<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'

const props = defineProps({
  agendas: Object,
  filters: Object
})

const search = ref(props.filters.search || '')
const estado = ref(props.filters.estado || '')
const tipo = ref(props.filters.tipo || '')

function filtrar() {
  router.get('/agendas', {
    search: search.value,
    estado: estado.value,
    tipo: tipo.value
  }, {
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

      <!-- Header -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
        <h1 class="h4 text-primary fw-bold">
          <i class="fas fa-calendar-alt me-2"></i> Gestionar Agendas
        </h1>
        <a href="/agendas/create" class="btn btn-primary">
          <i class="fas fa-plus"></i> Nueva Agenda
        </a>
      </div>

      <!-- Filtros -->
      <div class="card shadow-sm border-0 mb-3">
        <div class="card-body row g-2">

          <div class="col-md-4">
            <div class="input-group">
              <span class="input-group-text bg-light">
                <i class="fas fa-search"></i>
              </span>
              <input class="form-control" v-model="search" @input="filtrar" placeholder="Buscar cliente o empleado…">
            </div>
          </div>

          <div class="col-md-3">
            <select v-model="estado" @change="filtrar" class="form-select">
              <option value="">Todos los estados</option>
              <option value="pendiente">Pendiente</option>
              <option value="confirmada">Confirmada</option>
              <option value="completada">Completada</option>
              <option value="cancelada">Cancelada</option>
            </select>
          </div>

          <div class="col-md-3">
            <select v-model="tipo" @change="filtrar" class="form-select">
              <option value="">Todos los tipos</option>
              <option value="flash">Agenda Flash</option>
              <option value="local">Agenda Local</option>
            </select>
          </div>

        </div>
      </div>

      <!-- Tabla -->
      <div class="card shadow-sm border-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 text-center">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Cliente</th>
                <th>Empleado</th>
                <th>Servicios</th>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="agenda in agendas.data" :key="agenda.id">
                <td>{{ agenda.id }}</td>

                <td class="text-start">
                  <strong>{{ agenda.cliente.nombre }} {{ agenda.cliente.apellido }}</strong><br>
                  <small class="text-muted">{{ agenda.cliente.telefono }}</small>
                </td>

                <td class="text-start">
                  <strong>{{ agenda.empleado.nombre }} {{ agenda.empleado.apellido }}</strong><br>
                  <small class="text-muted">{{ agenda.empleado.puesto }}</small>
                </td>

                <!-- Servicios múltiples -->
                <td>
                  <span
                    v-for="srv in agenda.servicios"
                    :key="srv.id"
                    class="badge bg-secondary me-1"
                  >
                    {{ srv.nombre }}
                  </span>
                </td>

                <td>{{ new Date(agenda.fecha_hora).toLocaleString() }}</td>

                <td>
                  <span :class="agenda.tipo === 'flash' ? 'badge bg-warning' : 'badge bg-info'">
                    {{ agenda.tipo.toUpperCase() }}
                  </span>
                </td>

                <td>
                  <span
                    class="badge px-3 py-2"
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
                  <div class="btn-group">
                    <a :href="`/agendas/${agenda.id}/edit`" class="btn btn-sm btn-outline-warning">
                      <i class="fas fa-edit"></i>
                    </a>
                    <a :href="`/agendas/${agenda.id}`" class="btn btn-sm btn-outline-info">
                      <i class="fas fa-eye"></i>
                    </a>
                    <button @click.prevent="eliminarAgenda(agenda.id)" class="btn btn-sm btn-outline-danger">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>

              </tr>

              <tr v-if="!agendas.data.length">
                <td colspan="8" class="text-muted py-4">
                  <i class="fas fa-calendar-times fa-2x"></i><br>
                  Sin agendas registradas.
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
