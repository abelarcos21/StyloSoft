<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  tickets: Object, // con paginación
  filters: Object
})

// Filtros reactivos
const search = ref(props.filters.search || '')
const metodoPago = ref(props.filters.metodo_pago || '')

function filtrar() {
  router.get('/tickets', { search: search.value, metodo_pago: metodoPago.value }, {
    preserveState: true,
    replace: true,
  })
}

function eliminarTicket(id) {
  if (confirm('¿Deseas eliminar este ticket?')) {
    router.delete(`/tickets/${id}`)
  }
}
</script>

<template>
  <AdminLayout title="Tickets">
    <div class="container-fluid px-3">

      <!-- Encabezado -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
        <h1 class="h4 mb-0 text-primary fw-bold">
          <i class="fas fa-receipt me-2"></i> Tickets
        </h1>
        <a href="/tickets/create" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
          <i class="fas fa-plus"></i> Nuevo Ticket
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
              <select v-model="metodoPago" @change="filtrar" class="form-select">
                <option value="">Todos los métodos</option>
                <option value="efectivo">Efectivo</option>
                <option value="tarjeta">Tarjeta</option>
                <option value="transferencia">Transferencia</option>
                <!-- agrega otros métodos según tu BD -->
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
                <th>Fecha</th>
                <th>Total</th>
                <th>Método de pago</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="ticket in props.tickets.data" :key="ticket.id" class="text-center">
                <td>{{ ticket.id }}</td>
                <td class="text-start">
                  <strong>{{ ticket.cliente ? ticket.cliente.nombre + ' ' + ticket.cliente.apellido : '-' }}</strong>
                  <br><small class="text-muted">{{ ticket.cliente?.telefono || '' }}</small>
                </td>
                <td class="text-start">
                  <strong>{{ ticket.empleado ? ticket.empleado.nombre + ' ' + ticket.empleado.apellido : '-' }}</strong>
                  <br><small class="text-muted">{{ ticket.empleado?.puesto || '' }}</small>
                </td>
                <td>{{ new Date(ticket.fecha).toLocaleString() }}</td>
                <td>${{ Number(ticket.total)?.toFixed(2) || '0.00' }}</td>
                <td>
                  <span
                    class="badge px-3 py-2 rounded-pill"
                    :class="{
                      'bg-success': ticket.metodo_pago === 'efectivo',
                      'bg-info': ticket.metodo_pago === 'tarjeta',
                      'bg-warning': ticket.metodo_pago === 'transferencia',
                      'bg-secondary': !ticket.metodo_pago
                    }"
                  >
                    {{ ticket.metodo_pago || '-' }}
                  </span>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <a :href="`/tickets/${ticket.id}`" class="btn btn-sm btn-outline-info" title="Ver detalle">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a :href="`/tickets/${ticket.id}/edit`" class="btn btn-sm btn-outline-warning" title="Editar">
                      <i class="fas fa-edit"></i>
                    </a>
                    <button @click.prevent="eliminarTicket(ticket.id)" class="btn btn-sm btn-outline-danger" title="Eliminar">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="!props.tickets.data.length">
                <td colspan="7" class="text-center text-muted py-4">
                  <i class="fas fa-inbox fa-2x mb-2"></i><br>
                  No hay tickets registrados.
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
