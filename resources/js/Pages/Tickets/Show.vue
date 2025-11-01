<template>
  <AdminLayout>
    <div class="content-header">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1 class="m-0">
          <i class="fas fa-receipt"></i> Detalle del Ticket #{{ ticket.id }}
        </h1>
        <Link href="/tickets" class="btn btn-secondary">
          <i class="fas fa-arrow-left"></i> Regresar
        </Link>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="card shadow-sm">
          <div class="card-body">

            <!-- Información general -->
            <div class="row mb-4">
              <div class="col-md-4">
                <h5><i class="fas fa-user"></i> Cliente</h5>
                <p v-if="ticket.cliente">
                  {{ ticket.cliente.nombre }} {{ ticket.cliente.apellido }} <br>
                  <small class="text-muted">ID: {{ ticket.cliente.id }}</small>
                </p>
                <p v-else class="text-muted">Sin cliente asignado</p>
              </div>

              <div class="col-md-4">
                <h5><i class="fas fa-user-tie"></i> Empleado</h5>
                <p v-if="ticket.empleado">
                  {{ ticket.empleado.nombre }} {{ ticket.empleado.apellido }} <br>
                  <small class="text-muted">ID: {{ ticket.empleado.id }}</small>
                </p>
                <p v-else class="text-muted">Sin empleado asignado</p>
              </div>

              <div class="col-md-4">
                <h5><i class="fas fa-calendar-alt"></i> Fecha</h5>
                <p>{{ formatoFecha(ticket.fecha) }}</p>

                <h5 class="mt-3"><i class="fas fa-credit-card"></i> Método de Pago</h5>
                <p>{{ ticket.metodo_pago || 'No especificado' }}</p>
              </div>
            </div>

            <hr>

            <!-- Productos -->
            <div v-if="productos.length">
            <h5><i class="fas fa-box"></i> Productos</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(p, index) in productos" :key="index">
                    <td>{{ p.nombre }}</td>
                    <td>{{ p.cantidad }}</td>
                    <td>${{ Number(p.precio_unitario || 0).toFixed(2) }}</td>
                    <td>${{ Number(p.subtotal || 0).toFixed(2) }}</td>
                    </tr>
                </tbody>
                </table>
            </div>
            </div>

            <!-- Servicios -->
            <div v-if="servicios.length" class="mt-4">
            <h5><i class="fas fa-spa"></i> Servicios</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                <thead class="table-success">
                    <tr>
                    <th>Servicio</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(s, index) in servicios" :key="index">
                    <td>{{ s.nombre }}</td>
                    <td>{{ s.cantidad }}</td>
                    <td>${{ Number(s.precio_unitario || 0).toFixed(2) }}</td>
                    <td>${{ Number(s.subtotal || 0).toFixed(2) }}</td>
                    </tr>
                </tbody>
                </table>
            </div>
            </div>

            <hr>

            <!-- Total -->
            <div class="text-end mt-4">
              <h3>Total: <strong>${{ Number(ticket.total)?.toFixed(2) || '0.00'  }}</strong></h3>
            </div>

          </div>
        </div>
      </div>
    </section>
  </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed } from 'vue';

const props = defineProps({
  ticket: Object,
});

// Filtrar productos y servicios desde los detalles
const productos = computed(() =>
  props.ticket.detalle_tickets?.filter(d => d.vendible_type.includes('Producto')).map(d => ({
    nombre: d.vendible?.nombre || 'Producto eliminado',
    cantidad: d.cantidad,
    precio_unitario: d.precio_unitario,
    subtotal: d.subtotal,
  })) || []
);

const servicios = computed(() =>
  props.ticket.detalle_tickets?.filter(d => d.vendible_type.includes('Servicio')).map(d => ({
    nombre: d.vendible?.nombre || 'Servicio eliminado',
    cantidad: d.cantidad,
    precio_unitario: d.precio_unitario,
    subtotal: d.subtotal,
  })) || []
);

// Formato de fecha
const formatoFecha = (fecha) => {
  if (!fecha) return '-';
  return new Date(fecha).toLocaleString('es-MX', {
    dateStyle: 'long',
    timeStyle: 'short'
  });
};
</script>

<style scoped>
.card {
  border-radius: 1rem;
}
.table th, .table td {
  vertical-align: middle;
}
</style>

