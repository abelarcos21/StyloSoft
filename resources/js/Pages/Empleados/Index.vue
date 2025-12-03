<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
  empleados: Object,
  filters: Object,
  puestos: Array
})

const search = ref(props.filters.search || '')
const estado = ref(props.filters.estado || '')
const puesto = ref(props.filters.puesto || '')

function filtrar() {
  router.get('/empleados', {
    search: search.value,
    estado: estado.value,
    puesto: puesto.value
  }, {
    preserveState: true,
    replace: true,
  })
}

function eliminarEmpleado(id) {
  Swal.fire({
    title: "¿Eliminar empleado?",
    text: "Esta acción no se puede deshacer",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then(result => {
    if (result.isConfirmed) {
      router.delete(`/empleados/${id}`, {
        onSuccess: () => {
          Swal.fire("Eliminado", "Empleado eliminado exitosamente", "success")
        },
        onError: () => {
          Swal.fire("Error", "No se pudo eliminar el empleado", "error")
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
</script>

<template>
  <AdminLayout title="Empleados">
    <div class="container-fluid px-3">

      <!-- Header -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
        <h1 class="h4 text-pink fw-bold">
          <i class="fas fa-users-cog me-2"></i> Gestionar Empleados
        </h1>
        <div class="btn-group">
          <button @click="exportarEmpleados" class="btn btn-success">
            <i class="fas fa-file-excel me-2"></i> Exportar
          </button>
          <a href="/empleados/create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Nuevo Empleado
          </a>
        </div>
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
                placeholder="Buscar por nombre, puesto o email..."
              >
            </div>
          </div>

          <div class="col-md-3">
            <select v-model="estado" @change="filtrar" class="form-select">
              <option value="">Todos los estados</option>
              <option value="activo">Activo</option>
              <option value="inactivo">Inactivo</option>
              <option value="vacaciones">Vacaciones</option>
            </select>
          </div>

          <div class="col-md-3">
            <select v-model="puesto" @change="filtrar" class="form-select">
              <option value="">Todos los puestos</option>
              <option v-for="p in puestos" :key="p" :value="p">{{ p }}</option>
            </select>
          </div>

          <div class="col-md-2">
            <button @click="search = ''; estado = ''; puesto = ''; filtrar()" class="btn btn-secondary w-100">
              <i class="fas fa-redo me-2"></i> Limpiar
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
                <th class="text-center">#</th>
                <th>Empleado</th>
                <th>Puesto</th>
                <th>Contacto</th>
                <th class="text-center">Horario</th>
                <th class="text-center">Días Laborales</th>
                <th class="text-center">Salario</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="empleado in empleados.data" :key="empleado.id">
                <td class="text-center">{{ empleado.id }}</td>

                <!-- EMPLEADO -->
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-success text-white me-2">
                      {{ empleado.nombre.charAt(0) }}{{ empleado.apellido.charAt(0) }}
                    </div>
                    <div>
                      <strong>{{ empleado.nombre_completo }}</strong><br>
                      <small class="text-muted" v-if="empleado.fecha_ingreso">
                        <i class="fas fa-calendar fa-xs me-1"></i>Desde: {{ empleado.fecha_ingreso }}
                      </small>
                    </div>
                  </div>
                </td>

                <!-- PUESTO -->
                <td>
                  <span class="badge bg-primary">
                    <i class="fas fa-briefcase me-1"></i>{{ empleado.puesto }}
                  </span>
                </td>

                <!-- CONTACTO -->
                <td>
                  <div v-if="empleado.telefono">
                    <i class="fas fa-phone fa-xs text-success me-1"></i>
                    <span>{{ empleado.telefono }}</span>
                  </div>
                  <div v-if="empleado.email">
                    <i class="fas fa-envelope fa-xs text-primary me-1"></i>
                    <small>{{ empleado.email }}</small>
                  </div>
                </td>

                <!-- HORARIO -->
                <td class="text-center">
                  <small v-if="empleado.hora_entrada && empleado.hora_salida">
                    <i class="fas fa-clock text-info me-1"></i>
                    {{ empleado.hora_entrada }} - {{ empleado.hora_salida }}
                  </small>
                  <span v-else class="text-muted">-</span>
                </td>

                <!-- DÍAS LABORALES -->
                <td class="text-center">
                  <div v-if="empleado.dias_laborales && empleado.dias_laborales.length">
                    <span 
                      v-for="dia in empleado.dias_laborales" 
                      :key="dia"
                      class="badge bg-secondary me-1 mb-1"
                    >
                      {{ dia.substring(0, 3).toUpperCase() }}
                    </span>
                  </div>
                  <span v-else class="text-muted">-</span>
                </td>

                <!-- SALARIO -->
                <td class="text-center">
                  <span class="fw-semibold text-success">
                    ${{ empleado.salario }}
                  </span>
                  <div v-if="empleado.comision_porcentaje > 0">
                    <small class="text-muted">
                      <i class="fas fa-percentage fa-xs"></i> {{ empleado.comision_porcentaje }}%
                    </small>
                  </div>
                </td>

                <!-- ESTADO -->
                <td class="text-center">
                  <span 
                    class="badge px-3 py-2"
                    :class="{
                      'bg-success': empleado.estado === 'activo',
                      'bg-danger': empleado.estado === 'inactivo',
                      'bg-warning text-dark': empleado.estado === 'vacaciones'
                    }"
                  >
                    {{ empleado.estado.toUpperCase() }}
                  </span>
                </td>

                <!-- ACCIONES -->
                <td class="text-center">
                  <div class="btn-group">
                    <a 
                      :href="`/empleados/${empleado.id}`" 
                      class="btn btn-sm btn-outline-info"
                      title="Ver detalles"
                    >
                      <i class="fas fa-eye"></i>
                    </a>
                    <a 
                      :href="`/empleados/${empleado.id}/edit`" 
                      class="btn btn-sm btn-outline-warning"
                      title="Editar"
                    >
                      <i class="fas fa-edit"></i>
                    </a>
                    <button 
                      @click="eliminarEmpleado(empleado.id)" 
                      class="btn btn-sm btn-outline-danger"
                      title="Eliminar"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>

              </tr>

              <tr v-if="!empleados.data.length">
                <td colspan="9" class="text-center text-muted py-5">
                  <i class="fas fa-users-slash fa-3x mb-3 d-block"></i>
                  <p class="mb-0">No hay empleados registrados con los filtros seleccionados.</p>
                </td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>

      <!-- Paginación -->
      <nav class="mt-4" v-if="empleados.data.length">
        <ul class="pagination justify-content-center flex-wrap">
          <li
            v-for="(link, i) in props.empleados.links"
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
.avatar-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 0.9rem;
}

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