<script setup>
import { ref, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from 'sweetalert2'
import debounce from 'lodash.debounce'

const props = defineProps({
  empleados: Object,
  filters: Object,
  puestos: Array
})

const search = ref(props.filters.search || '')
const estado = ref(props.filters.estado || '')
const puesto = ref(props.filters.puesto || '')

// Filtrado con debounce para la búsqueda optimizada
const filtrar = debounce(() => {
  router.get('/empleados', {
    search: search.value,
    estado: estado.value,
    puesto: puesto.value
  }, {
    preserveState: true,
    replace: true,
  })
}, 300)

// Observar cambios en búsqueda para disparar el filtro automáticamente
watch(search, () => filtrar())

// SweetAlert estilizado para coincidir con el diseño
const swalEstilizado = Swal.mixin({
  customClass: {
    confirmButton: 'btn btn-danger text-white rounded-pill px-4',
    cancelButton: 'btn btn-secondary rounded-pill px-4 ms-2'
  },
  buttonsStyling: false
})

function eliminarEmpleado(id) {
  swalEstilizado.fire({
    title: "¿Eliminar empleado?",
    text: "Esta acción no se puede deshacer.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then(result => {
    if (result.isConfirmed) {
      router.delete(`/empleados/${id}`, {
        onSuccess: () => {
          Swal.fire({
            title: "Eliminado",
            text: "Empleado eliminado exitosamente",
            icon: "success",
            confirmButtonColor: '#d84b72'
          })
        },
        onError: () => {
          Swal.fire({
            title: "Error",
            text: "No se pudo eliminar el empleado",
            icon: "error",
            confirmButtonColor: '#d84b72'
          })
        }
      })
    }
  })
}

function exportarEmpleados() {
  window.location.href = '/empleados-exportar?' + new URLSearchParams({
    search: search.value,
    estado: estado.value,
    puesto: puesto.value
  })
}

// Función auxiliar para obtener iniciales de forma segura
function getIniciales(nombre, apellido) {
  const n = nombre ? nombre.charAt(0).toUpperCase() : '';
  const a = apellido ? apellido.charAt(0).toUpperCase() : '';
  return `${n}${a}` || 'E';
}
</script>

<template>
  <AdminLayout title="Empleados">
    <div class="container-fluid px-0 px-md-3">

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h1 class="h4 brand-accent fw-bold mb-0">
          <i class="fas fa-users-cog me-2"></i> Gestionar Empleados
        </h1>
        <div class="d-flex gap-2">
          <button @click="exportarEmpleados" class="btn btn-light rounded-pill px-4 shadow-sm text-success fw-medium">
            <i class="fas fa-file-excel me-2"></i> Exportar
          </button>
          <Link href="/empleados/create" class="btn btn-brand rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus me-2"></i> Nuevo Empleado
          </Link>
        </div>
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
                placeholder="Buscar por nombre, puesto o email..."
              >
            </div>
          </div>

          <div class="col-6 col-md-3">
            <select v-model="estado" @change="filtrar" class="form-select bg-light rounded-pill border-0">
              <option value="">Todos los estados</option>
              <option value="activo">Activo</option>
              <option value="inactivo">Inactivo</option>
              <option value="vacaciones">Vacaciones</option>
            </select>
          </div>

          <div class="col-6 col-md-3">
            <select v-model="puesto" @change="filtrar" class="form-select bg-light rounded-pill border-0">
              <option value="">Todos los puestos</option>
              <option v-for="p in puestos" :key="p" :value="p">{{ p }}</option>
            </select>
          </div>

          <div class="col-12 col-md-2">
            <button @click="search = ''; estado = ''; puesto = ''; filtrar()" class="btn btn-light rounded-pill w-100 text-muted">
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
                <th class="border-0 font-weight-semibold">Empleado</th>
                <th class="border-0 font-weight-semibold">Puesto</th>
                <th class="border-0 font-weight-semibold">Contacto</th>
                <th class="text-center border-0 font-weight-semibold">Horario</th>
                <th class="text-center border-0 font-weight-semibold">Días Laborales</th>
                <th class="text-center border-0 font-weight-semibold">Salario</th>
                <th class="text-center border-0 font-weight-semibold">Estado</th>
                <th class="text-center border-0 font-weight-semibold">Acciones</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="empleado in empleados.data" :key="empleado.id">
                <td class="text-center text-muted">{{ empleado.id }}</td>

                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar-circle me-3">
                      {{ getIniciales(empleado.nombre, empleado.apellido) }}
                    </div>
                    <div>
                      <div class="fw-bold text-dark">{{ empleado.nombre_completo }}</div>
                      <div class="small text-muted" v-if="empleado.fecha_ingreso">
                        <i class="fas fa-calendar fa-xs me-1"></i>Desde: {{ empleado.fecha_ingreso }}
                      </div>
                    </div>
                  </div>
                </td>

                <td>
                  <span class="badge rounded-pill bg-primary-subtle text-primary fw-medium px-3">
                    <i class="fas fa-briefcase me-1"></i>{{ empleado.puesto }}
                  </span>
                </td>

                <td>
                  <div class="text-dark" v-if="empleado.telefono">
                    <i class="fas fa-phone fa-xs text-muted me-2"></i>{{ empleado.telefono }}
                  </div>
                  <div class="small text-muted mt-1" v-if="empleado.email">
                    <i class="fas fa-envelope fa-xs me-2"></i>{{ empleado.email }}
                  </div>
                </td>

                <td class="text-center">
                  <span class="fw-medium text-dark small" v-if="empleado.hora_entrada && empleado.hora_salida">
                    <i class="fas fa-clock text-info me-1"></i>
                    {{ empleado.hora_entrada }} - {{ empleado.hora_salida }}
                  </span>
                  <span v-else class="text-muted">-</span>
                </td>

                <td class="text-center">
                  <div v-if="empleado.dias_laborales && empleado.dias_laborales.length">
                    <span
                      v-for="dia in empleado.dias_laborales"
                      :key="dia"
                      class="badge bg-light border text-secondary me-1 mb-1 fw-normal"
                    >
                      {{ dia.substring(0, 3).toUpperCase() }}
                    </span>
                  </div>
                  <span v-else class="text-muted">-</span>
                </td>

                <td class="text-center">
                  <div class="fw-bold brand-accent">
                    ${{ empleado.salario }}
                  </div>
                  <div v-if="empleado.comision_porcentaje > 0" class="small text-muted mt-1">
                    <i class="fas fa-percentage fa-xs"></i> {{ empleado.comision_porcentaje }}%
                  </div>
                </td>

                <td class="text-center">
                  <span
                    class="badge rounded-pill px-3 py-2 fw-medium"
                    :class="{
                      'bg-success-subtle text-success': empleado.estado === 'activo',
                      'bg-danger-subtle text-danger': empleado.estado === 'inactivo',
                      'bg-warning-subtle text-warning': empleado.estado === 'vacaciones'
                    }"
                  >
                    {{ empleado.estado.toUpperCase() }}
                  </span>
                </td>

                <td class="text-center">
                  <div class="d-flex justify-content-center gap-2">
                    <Link
                      :href="`/empleados/${empleado.id}`"
                      class="btn btn-sm btn-light text-info rounded-circle action-btn"
                      title="Ver detalles"
                    >
                      <i class="fas fa-eye"></i>
                    </Link>
                    <Link
                      :href="`/empleados/${empleado.id}/edit`"
                      class="btn btn-sm btn-light text-warning rounded-circle action-btn"
                      title="Editar"
                    >
                      <i class="fas fa-edit"></i>
                    </Link>
                    <button
                      @click="eliminarEmpleado(empleado.id)"
                      class="btn btn-sm btn-light text-danger rounded-circle action-btn"
                      title="Eliminar"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="!empleados.data.length">
                <td colspan="9" class="text-center py-5">
                  <div class="text-muted d-flex flex-column align-items-center">
                    <i class="fas fa-users-slash fa-3x mb-3 text-light"></i>
                    <h5 class="fw-medium text-secondary">Sin resultados</h5>
                    <p class="mb-0 small">No hay empleados registrados con los filtros seleccionados.</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <nav class="mt-4" v-if="empleados.data.length">
        <ul class="pagination justify-content-center flex-wrap">
          <li
            v-for="(link, i) in props.empleados.links"
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

/* Avatar del empleado (Ajustado para diferenciarlo de clientes, usando un tono azul suave) */
.avatar-circle {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.95rem;
  background-color: rgba(13, 110, 253, 0.1);
  color: #0d6efd;
  border: 1px solid rgba(13, 110, 253, 0.2);
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

/* Clases sutiles para Bootstrap (Por si usas una versión anterior a 5.3) */
.bg-primary-subtle { background-color: #cfe2ff !important; }
.bg-warning-subtle { background-color: #fff3cd !important; }
.bg-success-subtle { background-color: #d1e7dd !important; }
.bg-danger-subtle { background-color: #f8d7da !important; }

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
