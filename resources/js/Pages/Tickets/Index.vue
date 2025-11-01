<template>
  <AdminLayout>
    <div class="d-flex justify-content-between mb-3">
      <h2>Tickets</h2>
      <Link href="/tickets/create" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Nuevo Ticket</Link>
    </div>

    <div v-if="$page.props.flash?.success" class="alert alert-success">
        {{ $page.props.flash.success }}
    </div>

    <div class="card">
      <div class="card-body p-0">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Cliente</th>
              <th>Empleado</th>
              <th>Fecha</th>
              <th>Total</th>
              <th>Método de pago</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="ticket in tickets" :key="ticket.id">
              <td>{{ ticket.id }}</td>
              <td>{{ ticket.cliente ? ticket.cliente.nombre + ' ' + ticket.cliente.apellido : '-' }}</td>
              <td>{{ ticket.empleado ? ticket.empleado.nombre + ' ' + ticket.empleado.apellido : '-' }}</td>
              <td>{{ new Date(ticket.fecha).toLocaleString() }}</td>
              <td>{{ Number(ticket.total)?.toFixed(2) || '0.00' }}</td>
              <td>{{ ticket.metodo_pago || '-' }}</td>
              <td>
                <Link :href="`/tickets/${ticket.id}`" class="btn btn-sm btn-info me-1">Ver Detalle</Link>
                <Link :href="`/tickets/${ticket.id}/edit`" class="btn btn-sm btn-warning me-1">Editar</Link>
                <form :action="`/tickets/${ticket.id}`" method="POST" class="d-inline">
                  <input type="hidden" name="_method" value="DELETE">
                  <input type="hidden" name="_token" :value="$page.props.csrf_token">
                  <button type="submit" class="btn btn-sm btn-danger" @click.prevent="confirmDelete(ticket.id)">Eliminar</button>
                </form>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
const { tickets } = defineProps(['tickets']);

function confirmDelete(id){
    if(confirm('¿Eliminar este ticket?')){
        router.delete(`/tickets/${id}`);
    }
}
</script>
