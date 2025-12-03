<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
  clientes: Object,
  filters: Object
})

const search = ref(props.filters.search || '')
const genero = ref(props.filters.genero || '')
const marketing = ref(props.filters.marketing || '')

function filtrar() {
  router.get('/clientes', {
    search: search.value,
    genero: genero.value,
    marketing: marketing.value
  }, {
    preserveState: true,
    replace: true,
  })
}

function eliminarCliente(id) {
  Swal.fire({
    title: "¿Eliminar cliente?",
    text: "Esta acción no se puede deshacer",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then(result => {
    if (result.isConfirmed) {
      router.delete(`/clientes/${id}`, {
        onSuccess: () => {
          Swal.fire("Eliminado", "Cliente eliminado exitosamente", "success")
        },
        onError: () => {
          Swal.fire("Error", "No se pudo eliminar el cliente", "error")
        }
      })
    }
  })
}

function exportarClientes() {
  window.location.href = '/clientes-exportar?' + new URLSearchParams({
    search: search.value,
    genero: genero.value,
    marketing: marketing.value
  })
}
</script>

<template>
  <AdminLayout title="Clientes">
    <div class="container-fluid px-3">

      <!-- Header -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
        <h1 class="h4 text-pink fw-bold">
          <i class="fas fa-users me-2"></i> Gestionar Clientes
        </h1>
        <div class="btn-group">
          <button @click="exportarClientes" class="btn btn-success">
            <i class="fas fa-file-excel me-2"></i> Exportar
          </button>
          <a href="/clientes/create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Nuevo Cliente
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
                placeholder="Buscar por nombre, email o teléfono..."
              >
            </div>
          </div>

          <div class="col-md-3">
            <select v-model="genero" @change="filtrar" class="form-select">
              <option value="">Todos los géneros</option>
              <option value="masculino">Masculino</option>
              <option value="femenino">Femenino</option>
              <option value="otro">Otro</option>
              <option value="prefiero_no_decir">Prefiero no decir</option>
            </select>
          </div>

          <div class="col-md-3">
            <select v-model="marketing" @change="filtrar" class="form-select">
              <option value="">Marketing (Todos)</option>
              <option value="1">Acepta Marketing</option>
              <option value="0">No acepta Marketing</option>
            </select>
          </div>

          <div class="col-md-2">
            <button @click="search = ''; genero = ''; marketing = ''; filtrar()" class="btn btn-secondary w-100">
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
                <th>Cliente</th>
                <th>Contacto</th>
                <th class="text-center">Género</th>
                <th class="text-center">Puntos</th>
                <th class="text-center">Marketing</th>
                <th class="text-center">Última Visita</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="cliente in clientes.data" :key="cliente.id">
                <td class="text-center">{{ cliente.id }}</td>

                <!-- CLIENTE -->
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-primary text-white me-2">
                      {{ cliente.nombre.charAt(0) }}{{ cliente.apellido.charAt(0) }}
                    </div>
                    <div>
                      <strong>{{ cliente.nombre_completo }}</strong><br>
                      <small class="text-muted" v-if="cliente.fecha_nacimiento">
                        <i class="fas fa-birthday-cake fa-xs me-1"></i>{{ cliente.fecha_nacimiento }}
                      </small>
                    </div>
                  </div>
                </td>

                <!-- CONTACTO -->
                <td>
                  <div v-if="cliente.telefono">
                    <i class="fas fa-phone fa-xs text-success me-1"></i>
                    <span>{{ cliente.telefono }}</span>
                  </div>
                  <div v-if="cliente.email">
                    <i class="fas fa-envelope fa-xs text-primary me-1"></i>
                    <small>{{ cliente.email }}</small>
                  </div>
                  <span v-if="!cliente.telefono && !cliente.email" class="text-muted">
                    Sin contacto
                  </span>
                </td>

                <!-- GÉNERO -->
                <td class="text-center">
                  <span class="badge bg-secondary" v-if="cliente.genero">
                    {{ cliente.genero === 'masculino' ? 'M' : cliente.genero === 'femenino' ? 'F' : 'Otro' }}
                  </span>
                  <span v-else class="text-muted">-</span>
                </td>

                <!-- PUNTOS -->
                <td class="text-center">
                  <span class="badge bg-warning text-dark">
                    <i class="fas fa-star me-1"></i>{{ cliente.puntos_fidelidad }}
                  </span>
                </td>

                <!-- MARKETING -->
                <td class="text-center">
                  <span 
                    class="badge" 
                    :class="cliente.acepta_marketing ? 'bg-success' : 'bg-secondary'"
                  >
                    {{ cliente.acepta_marketing ? 'Sí' : 'No' }}
                  </span>
                </td>

                <!-- ÚLTIMA VISITA -->
                <td class="text-center">
                  <small v-if="cliente.ultima_visita">{{ cliente.ultima_visita }}</small>
                  <span v-else class="text-muted">-</span>
                </td>

                <!-- ACCIONES -->
                <td class="text-center">
                  <div class="btn-group">
                    <a 
                      :href="`/clientes/${cliente.id}`" 
                      class="btn btn-sm btn-outline-info"
                      title="Ver detalles"
                    >
                      <i class="fas fa-eye"></i>
                    </a>
                    <a 
                      :href="`/clientes/${cliente.id}/edit`" 
                      class="btn btn-sm btn-outline-warning"
                      title="Editar"
                    >
                      <i class="fas fa-edit"></i>
                    </a>
                    <button 
                      @click="eliminarCliente(cliente.id)" 
                      class="btn btn-sm btn-outline-danger"
                      title="Eliminar"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>

              </tr>

              <tr v-if="!clientes.data.length">
                <td colspan="8" class="text-center text-muted py-5">
                  <i class="fas fa-users-slash fa-3x mb-3 d-block"></i>
                  <p class="mb-0">No hay clientes registrados con los filtros seleccionados.</p>
                </td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>

      <!-- Paginación -->
      <nav class="mt-4" v-if="clientes.data.length">
        <ul class="pagination justify-content-center flex-wrap">
          <li
            v-for="(link, i) in props.clientes.links"
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