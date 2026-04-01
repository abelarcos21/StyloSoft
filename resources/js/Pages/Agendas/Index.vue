<script setup>
import { ref, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3' // Importado Link
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from 'sweetalert2'
import debounce from 'lodash.debounce'

const props = defineProps({
  agendas: Object,
  filters: Object
})

const search = ref(props.filters.search || '')
const estado = ref(props.filters.estado || '')
const tipo = ref(props.filters.tipo || '')

// Filtrado con debounce para la búsqueda (evita saturar el servidor al teclear rápido)
const filtrar = debounce(() => {
  router.get('/agendas', {
    search: search.value,
    estado: estado.value,
    tipo: tipo.value
  }, {
    preserveState: true,
    replace: true,
  })
}, 300)

// Observar cambios en búsqueda para disparar el filtro de forma automática y óptima
watch(search, () => filtrar())

// SweetAlert estilizado
const swalEstilizado = Swal.mixin({
  confirmButtonColor: '#d84b72',
  cancelButtonColor: '#6c757d',
  customClass: {
    confirmButton: 'btn btn-primary text-white rounded-pill px-4',
    cancelButton: 'btn btn-secondary rounded-pill px-4 ms-2'
  },
  buttonsStyling: false
})

function cancelarAgenda(id) {
  swalEstilizado.fire({
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
          swalEstilizado.fire("Cancelada", "La agenda ha sido cancelada.", "success")
        }
      })
    }
  })
}

function confirmarAgenda(id) {
  swalEstilizado.fire({
    title: "¿Confirmar cita?",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, confirmar",
    cancelButtonText: "No",
  }).then(result => {
    if (result.isConfirmed) {
      router.put(`/agendas/${id}/confirmar`, {}, {
        onSuccess: () => {
          swalEstilizado.fire("Confirmada", "La cita ha sido confirmada.", "success")
        }
      })
    }
  })
}
</script>

<template>
  <AdminLayout title="Agendas">
    <div class="container-fluid px-0 px-md-3">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h1 class="h4 brand-accent fw-bold mb-0">
          <i class="fas fa-calendar-alt me-2"></i> Gestionar Agendas
        </h1>
        <Link href="/agendas/create" class="btn btn-brand rounded-pill px-4 shadow-sm">
          <i class="fas fa-plus me-2"></i> Nueva Agenda
        </Link>
      </div>

      <div class="card shadow-sm border-0 mb-4 rounded-4">
        <div class="card-body row g-3">
          <div class="col-12 col-md-4">
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0 rounded-start-pill">
                <i class="fas fa-search text-muted"></i>
              </span>
              <input
                class="form-control border-start-0 bg-light rounded-end-pill focus-ring-none"
                v-model="search"
                placeholder="Buscar cliente o empleado…"
              >
            </div>
          </div>

          <div class="col-6 col-md-3">
            <select v-model="estado" @change="filtrar" class="form-select bg-light rounded-pill border-0">
              <option value="">Todos los estados</option>
              <option value="pendiente">Pendiente</option>
              <option value="confirmada">Confirmada</option>
              <option value="en_proceso">En Proceso</option>
              <option value="completada">Completada</option>
              <option value="cancelada">Cancelada</option>
              <option value="no_asistio">No asistió</option>
            </select>
          </div>

          <div class="col-6 col-md-3">
            <select v-model="tipo" @change="filtrar" class="form-select bg-light rounded-pill border-0">
              <option value="">Todos los tipos</option>
              <option value="express">Express</option>
              <option value="estandar">Estándar</option>
            </select>
          </div>

          <div class="col-12 col-md-2">
            <button @click="search = ''; estado = ''; tipo = ''; filtrar()" class="btn btn-light rounded-pill w-100 text-muted">
              <i class="fas fa-redo me-1"></i> Limpiar
            </button>
          </div>
        </div>
      </div>

      <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 custom-table">
            <thead class="bg-light text-muted">
              <tr>
                <th class="text-center border-0 font-weight-semibold">#</th>
                <th class="border-0 font-weight-semibold">Cliente</th>
                <th class="border-0 font-weight-semibold">Empleado</th>
                <th class="border-0 font-weight-semibold">Servicios</th>
                <th class="text-center border-0 font-weight-semibold">Total</th>
                <th class="text-center border-0 font-weight-semibold">Fecha y Hora</th>
                <th class="text-center border-0 font-weight-semibold">Tipo</th>
                <th class="text-center border-0 font-weight-semibold">Estado</th>
                <th class="text-center border-0 font-weight-semibold">Acciones</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="agenda in agendas.data" :key="agenda.id">
                <td class="text-center text-muted">{{ agenda.id }}</td>

                <td>
                  <div class="fw-bold text-dark">{{ agenda.cliente.nombre_completo }}</div>
                  <div class="small text-muted">
                    <i class="fas fa-phone fa-xs me-1"></i>{{ agenda.cliente.telefono }}
                  </div>
                </td>

                <td>
                  <div class="fw-bold text-dark">{{ agenda.empleado.nombre_completo }}</div>
                  <div class="small text-muted">{{ agenda.empleado.puesto }}</div>
                </td>

                <td>
                  <span
                    v-for="srv in agenda.servicios"
                    :key="srv.id"
                    class="badge bg-light text-secondary border me-1 mb-1 fw-normal"
                  >
                    {{ srv.nombre }}
                  </span>
                  <div class="small text-muted mt-1">
                    <i class="fas fa-clock fa-xs me-1"></i>{{ agenda.duracion_total }} min
                  </div>
                </td>

                <td class="text-center fw-bold brand-accent">
                  ${{ agenda.total }}
                </td>

                <td class="text-center">
                  <span class="fw-medium text-dark">{{ agenda.fecha_formato }}</span>
                </td>

                <td class="text-center">
                  <span class="badge rounded-pill fw-medium px-3" :class="agenda.tipo === 'express' ? 'bg-warning-subtle text-warning' : 'bg-info-subtle text-info'">
                    {{ agenda.tipo }}
                  </span>
                </td>

                <td class="text-center">
                  <span
                    class="badge rounded-pill px-3 py-2 fw-medium"
                    :class="{
                      'bg-warning-subtle text-warning': agenda.estado === 'pendiente',
                      'bg-info-subtle text-info': agenda.estado === 'confirmada',
                      'bg-primary-subtle text-primary': agenda.estado === 'en_proceso',
                      'bg-success-subtle text-success': agenda.estado === 'completada',
                      'bg-danger-subtle text-danger': agenda.estado === 'cancelada',
                      'bg-secondary-subtle text-secondary': agenda.estado === 'no_asistio'
                    }"
                  >
                    {{ agenda.estado.replace('_', ' ') }}
                  </span>
                </td>

                <td class="text-center">
                  <div class="d-flex justify-content-center gap-2">
                    <Link
                      :href="`/agendas/${agenda.id}`"
                      class="btn btn-sm btn-light text-info rounded-circle action-btn"
                      title="Ver detalles"
                    >
                      <i class="fas fa-eye"></i>
                    </Link>

                    <Link
                      :href="`/agendas/${agenda.id}/edit`"
                      class="btn btn-sm btn-light text-warning rounded-circle action-btn"
                      title="Editar"
                    >
                      <i class="fas fa-edit"></i>
                    </Link>

                    <button
                      v-if="agenda.estado === 'pendiente'"
                      @click.prevent="confirmarAgenda(agenda.id)"
                      class="btn btn-sm btn-light text-success rounded-circle action-btn"
                      title="Confirmar cita"
                    >
                      <i class="fas fa-check"></i>
                    </button>

                    <button
                      v-if="['pendiente', 'confirmada'].includes(agenda.estado)"
                      @click.prevent="cancelarAgenda(agenda.id)"
                      class="btn btn-sm btn-light text-danger rounded-circle action-btn"
                      title="Cancelar"
                    >
                      <i class="fas fa-ban"></i>
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="!agendas.data.length">
                <td colspan="9" class="text-center py-5">
                  <div class="text-muted d-flex flex-column align-items-center">
                    <i class="fas fa-calendar-times fa-3x mb-3 text-light"></i>
                    <h5 class="fw-medium text-secondary">Sin resultados</h5>
                    <p class="mb-0 small">No hay agendas registradas con los filtros seleccionados.</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <nav class="mt-4" v-if="agendas.data.length">
        <ul class="pagination justify-content-center flex-wrap">
          <li
            v-for="(link, i) in props.agendas.links"
            :key="i"
            class="page-item"
            :class="{ active: link.active, disabled: !link.url }"
          >
            <button
              class="page-link shadow-sm border-0 mx-1 rounded-2"
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
/* Variables y colores principales */
.brand-accent {
  color: #d84b72;
}

.btn-brand {
  background-color: #d84b72;
  color: white;
  transition: all 0.3s ease;
}

.btn-brand:hover {
  background-color: #c03d61;
  color: white;
  transform: translateY(-2px);
}

/* Modificadores de inputs */
.focus-ring-none:focus {
  box-shadow: none;
  border-color: #dee2e6;
}

/* Tabla personalizada */
.custom-table th {
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
}

.custom-table tbody tr {
  transition: background-color 0.2s ease;
}

.custom-table tbody tr:hover {
  background-color: #f8f9fa;
}

/* Botones de acción */
.action-btn {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
  background-color: #f8f9fa;
}

.action-btn:hover {
  transform: scale(1.1);
  background-color: #e9ecef;
}

/* Clases sutiles para Bootstrap (Por si no usas Bootstrap 5.3 que ya las trae) */
.bg-warning-subtle { background-color: #fff3cd !important; }
.bg-info-subtle { background-color: #cff4fc !important; }
.bg-primary-subtle { background-color: #cfe2ff !important; }
.bg-success-subtle { background-color: #d1e7dd !important; }
.bg-danger-subtle { background-color: #f8d7da !important; }
.bg-secondary-subtle { background-color: #e2e3e5 !important; }

/* Paginación corporativa */
.page-link {
  color: #6c757d;
}

.page-link:hover {
  color: #d84b72;
  background-color: #f8f9fa;
}

.page-item.active .page-link {
  background-color: #d84b72;
  color: white;
}
</style>
