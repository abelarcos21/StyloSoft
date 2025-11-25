<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'
import Swal from 'sweetalert2'

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

function cancelarAgenda(id) {
  Swal.fire({
    title: "¿Cancelar agenda?",
    text: "Esta acción cambiará su estado a 'cancelada'.",
    icon: "warning",
    input: 'textarea',
    inputPlaceholder: 'Razón de cancelación (opcional)',
    showCancelButton: true,
    confirmButtonText: "Sí, cancelar",
    cancelButtonText: "No",
  }).then(result => {
    if (result.isConfirmed) {
      router.put(`/agendas/${id}/cancelar`, {
        razon: result.value || 'Sin razón especificada',
        cancelado_por: 'sistema'
      }, {
        onSuccess: () => {
          Swal.fire("Cancelada", "La agenda ha sido cancelada.", "success")
        }
      })
    }
  })
}

function confirmarAgenda(id) {
  Swal.fire({
    title: "¿Confirmar cita?",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, confirmar",
    cancelButtonText: "No",
  }).then(result => {
    if (result.isConfirmed) {
      router.put(`/agendas/${id}/confirmar`, {}, {
        onSuccess: () => {
          Swal.fire("Confirmada", "La cita ha sido confirmada.", "success")
        }
      })
    }
  })
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
          <i class="fas fa-plus me-2"></i> Nueva Agenda
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
              <input
                class="form-control"
                v-model="search"
                @input="filtrar"
                placeholder="Buscar cliente o empleado…"
              >
            </div>
          </div>

          <div class="col-md-3">
            <select v-model="estado" @change="filtrar" class="form-select">
              <option value="">Todos los estados</option>
              <option value="pendiente">Pendiente</option>
              <option value="confirmada">Confirmada</option>
              <option value="en_proceso">En Proceso</option>
              <option value="completada">Completada</option>
              <option value="cancelada">Cancelada</option>
              <option value="no_asistio">No asistió</option>
            </select>
          </div>

          <div class="col-md-3">
            <select v-model="tipo" @change="filtrar" class="form-select">
              <option value="">Todos los tipos</option>
              <option value="express">Express</option>
              <option value="estandar">Estándar</option>
            </select>
          </div>

        </div>
      </div>

      <!-- Tabla -->
      <div class="card shadow-sm border-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="text-center">#</th>
                <th>Cliente</th>
                <th>Empleado</th>
                <th>Servicios</th>
                <th class="text-center">Total</th>
                <th class="text-center">Fecha y Hora</th>
                <th class="text-center">Tipo</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="agenda in agendas.data" :key="agenda.id">
                <td class="text-center">{{ agenda.id }}</td>

                <!-- CLIENTE -->
                <td>
                  <strong>{{ agenda.cliente.nombre_completo }}</strong><br>
                  <small class="text-muted">
                    <i class="fas fa-phone fa-xs me-1"></i>{{ agenda.cliente.telefono }}
                  </small>
                </td>

                <!-- EMPLEADO -->
                <td>
                  <strong>{{ agenda.empleado.nombre_completo }}</strong><br>
                  <small class="text-muted">{{ agenda.empleado.puesto }}</small>
                </td>

                <!-- SERVICIOS -->
                <td>
                  <span
                    v-for="srv in agenda.servicios"
                    :key="srv.id"
                    class="badge bg-secondary me-1 mb-1"
                  >
                    {{ srv.nombre }}
                  </span>
                  <br>
                  <small class="text-muted">
                    <i class="fas fa-clock fa-xs me-1"></i>{{ agenda.duracion_total }} min
                  </small>
                </td>

                <!-- TOTAL -->
                <td class="text-center fw-bold text-success">
                  ${{ agenda.total }}
                </td>

                <!-- FECHA -->
                <td class="text-center">
                  <div class="d-flex flex-column">
                    <span class="fw-semibold">{{ agenda.fecha_formato }}</span>
                  </div>
                </td>

                <!-- TIPO -->
                <td class="text-center">
                  <span :class="agenda.tipo === 'express' ? 'badge bg-warning text-dark' : 'badge bg-info'">
                    {{ agenda.tipo.toUpperCase() }}
                  </span>
                </td>

                <!-- ESTADO -->
                <td class="text-center">
                  <span
                    class="badge px-3 py-2"
                    :class="{
                      'bg-warning text-dark': agenda.estado === 'pendiente',
                      'bg-info': agenda.estado === 'confirmada',
                      'bg-primary': agenda.estado === 'en_proceso',
                      'bg-success': agenda.estado === 'completada',
                      'bg-danger': agenda.estado === 'cancelada',
                      'bg-dark': agenda.estado === 'no_asistio'
                    }"
                  >
                    {{ agenda.estado.replace('_', ' ').toUpperCase() }}
                  </span>
                </td>

                <!-- ACCIONES -->
                <td class="text-center">
                  <div class="btn-group">
                    <!-- Ver -->
                    <a
                      :href="`/agendas/${agenda.id}`"
                      class="btn btn-sm btn-outline-info"
                      title="Ver detalles"
                    >
                      <i class="fas fa-eye"></i>
                    </a>

                    <!-- Editar -->
                    <a
                      :href="`/agendas/${agenda.id}/edit`"
                      class="btn btn-sm btn-outline-warning"
                      title="Editar"
                    >
                      <i class="fas fa-edit"></i>
                    </a>

                    <!-- Confirmar (solo pendientes) -->
                    <button
                      v-if="agenda.estado === 'pendiente'"
                      @click.prevent="confirmarAgenda(agenda.id)"
                      class="btn btn-sm btn-outline-success"
                      title="Confirmar cita"
                    >
                      <i class="fas fa-check"></i>
                    </button>

                    <!-- Cancelar (solo pendientes o confirmadas) -->
                    <button
                      v-if="['pendiente', 'confirmada'].includes(agenda.estado)"
                      @click.prevent="cancelarAgenda(agenda.id)"
                      class="btn btn-sm btn-outline-danger"
                      title="Cancelar"
                    >
                      <i class="fas fa-ban"></i>
                    </button>
                  </div>
                </td>

              </tr>

              <tr v-if="!agendas.data.length">
                <td colspan="9" class="text-center text-muted py-5">
                  <i class="fas fa-calendar-times fa-3x mb-3 d-block"></i>
                  <p class="mb-0">No hay agendas registradas con los filtros seleccionados.</p>
                </td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>

      <!-- Paginación -->
      <nav class="mt-4" v-if="agendas.data.length">
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
