<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
  tickets: Object,
  filters: Object
})

const search = ref(props.filters.search || '')
const estado_pago = ref(props.filters.estado_pago || '')
const metodo_pago = ref(props.filters.metodo_pago || '')
const fecha_inicio = ref(props.filters.fecha_inicio || '')
const fecha_fin = ref(props.filters.fecha_fin || '')

function filtrar() {
  router.get('/tickets', {
    search: search.value,
    estado_pago: estado_pago.value,
    metodo_pago: metodo_pago.value,
    fecha_inicio: fecha_inicio.value,
    fecha_fin: fecha_fin.value
  }, {
    preserveState: true,
    replace: true,
  })
}

function eliminarTicket(id) {
  Swal.fire({
    title: "¿Eliminar ticket?",
    text: "Esta acción no se puede deshacer",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then(result => {
    if (result.isConfirmed) {
      router.delete(`/tickets/${id}`, {
        onSuccess: () => {
          Swal.fire("Eliminado", "Ticket eliminado exitosamente", "success")
        }
      })
    }
  })
}

function getEstadoPagoClass(estado) {
  const classes = {
    'pagado': 'bg-success',
    'pendiente': 'bg-warning text-dark',
    'parcial': 'bg-info'
  }
  return classes[estado] || 'bg-secondary'
}

function getMetodoPagoIcon(metodo) {
  const icons = {
    'efectivo': 'fa-money-bill-wave',
    'tarjeta': 'fa-credit-card',
    'transferencia': 'fa-exchange-alt',
    'mixto': 'fa-coins'
  }
  return icons[metodo] || 'fa-dollar-sign'
}
</script>

<template>
  <AdminLayout title="Tickets">
    <div class="container-fluid px-3">

      <!-- Header -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
        <h1 class="h4 text-pink fw-bold">
          <i class="fas fa-receipt me-2"></i> Tickets de Venta
        </h1>
        <a href="/tickets/create" class="btn btn-primary">
          <i class="fas fa-plus me-2"></i> Nuevo Ticket
        </a>
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
                placeholder="Buscar por N° ticket o cliente..."
              >
            </div>
          </div>

          <div class="col-md-2">
            <select v-model="estado_pago" @change="filtrar" class="form-select">
              <option value="">Todos los estados</option>
              <option value="pagado">Pagado</option>
              <option value="pendiente">Pendiente</option>
              <option value="parcial">Parcial</option>
            </select>
          </div>

          <div class="col-md-2">
            <select v-model="metodo_pago" @change="filtrar" class="form-select">
              <option value="">Todos los métodos</option>
              <option value="efectivo">Efectivo</option>
              <option value="tarjeta">Tarjeta</option>
              <option value="transferencia">Transferencia</option>
              <option value="mixto">Mixto</option>
            </select>
          </div>

          <div class="col-md-2">
            <input 
              type="date" 
              v-model="fecha_inicio" 
              @change="filtrar" 
              class="form-control"
              placeholder="Desde"
            >
          </div>

          <div class="col-md-2">
            <input 
              type="date" 
              v-model="fecha_fin" 
              @change="filtrar" 
              class="form-control"
              placeholder="Hasta"
            >
          </div>

          <div class="col-md-1">
            <button @click="search = ''; estado_pago = ''; metodo_pago = ''; fecha_inicio = ''; fecha_fin = ''; filtrar()" class="btn btn-secondary w-100">
              <i class="fas fa-redo"></i>
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
                <th>N° Ticket</th>
                <th>Cliente</th>
                <th>Empleado</th>
                <th class="text-center">Fecha</th>
                <th class="text-center">Método Pago</th>
                <th class="text-end">Subtotal</th>
                <th class="text-end">Total</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="ticket in tickets.data" :key="ticket.id">
                
                <!-- NÚMERO -->
                <td>
                  <strong class="text-primary">{{ ticket.numero_ticket }}</strong>
                </td>

                <!-- CLIENTE -->
                <td>
                  <div v-if="ticket.cliente">
                    <i class="fas fa-user text-primary me-1"></i>
                    {{ ticket.cliente.nombre_completo }}
                  </div>
                  <span v-else class="text-muted">Cliente General</span>
                </td>

                <!-- EMPLEADO -->
                <td>
                  <div v-if="ticket.empleado">
                    <i class="fas fa-user-tie text-success me-1"></i>
                    {{ ticket.empleado.nombre_completo }}
                  </div>
                </td>

                <!-- FECHA -->
                <td class="text-center">
                  <small>{{ ticket.fecha }}</small>
                </td>

                <!-- MÉTODO PAGO -->
                <td class="text-center">
                  <span class="badge bg-secondary">
                    <i :class="['fas', getMetodoPagoIcon(ticket.metodo_pago), 'me-1']"></i>
                    {{ ticket.metodo_pago }}
                  </span>
                </td>

                <!-- SUBTOTAL -->
                <td class="text-end">
                  <span class="text-muted">${{ ticket.subtotal }}</span>
                </td>

                <!-- TOTAL -->
                <td class="text-end">
                  <strong class="text-success fs-6">${{ ticket.total }}</strong>
                </td>

                <!-- ESTADO -->
                <td class="text-center">
                  <span 
                    class="badge px-3 py-2"
                    :class="getEstadoPagoClass(ticket.estado_pago)"
                  >
                    {{ ticket.estado_pago.toUpperCase() }}
                  </span>
                </td>

                <!-- ACCIONES -->
                <td class="text-center">
                  <div class="btn-group">
                    <a 
                      :href="`/tickets/${ticket.id}`" 
                      class="btn btn-sm btn-outline-info"
                      title="Ver ticket"
                    >
                      <i class="fas fa-eye"></i>
                    </a>
                    <a 
                      :href="`/tickets/${ticket.id}/imprimir`" 
                      class="btn btn-sm btn-outline-primary"
                      title="Imprimir"
                      target="_blank"
                    >
                      <i class="fas fa-print"></i>
                    </a>
                    <a 
                      :href="`/tickets/${ticket.id}/edit`" 
                      class="btn btn-sm btn-outline-warning"
                      title="Editar"
                    >
                      <i class="fas fa-edit"></i>
                    </a>
                    <button 
                      @click="eliminarTicket(ticket.id)" 
                      class="btn btn-sm btn-outline-danger"
                      title="Eliminar"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>

              </tr>

              <tr v-if="!tickets.data.length">
                <td colspan="9" class="text-center text-muted py-5">
                  <i class="fas fa-receipt fa-3x mb-3 d-block"></i>
                  <p class="mb-0">No hay tickets registrados con los filtros seleccionados.</p>
                </td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>

      <!-- Paginación -->
      <nav class="mt-4" v-if="tickets.data.length">
        <ul class="pagination justify-content-center flex-wrap">
          <li
            v-for="(link, i) in props.tickets.links"
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