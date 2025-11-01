<template>
  <AdminLayout>
    <h2 class="mb-4">Editar Ticket #{{ ticket.id }}</h2>

    <form @submit.prevent="submit">
      <div class="mb-3">
        <label>Cliente</label>
        <select v-model="form.cliente_id" class="form-control">
          <option value="">-- Seleccione cliente --</option>
          <option v-for="c in clientes" :key="c.id" :value="c.id">{{ c.nombre }} {{ c.apellido }}</option>
        </select>
      </div>

      <div class="mb-3">
        <label>Empleado</label>
        <select v-model="form.empleado_id" class="form-control">
          <option value="">-- Seleccione empleado --</option>
          <option v-for="e in empleados" :key="e.id" :value="e.id">{{ e.nombre }} {{ e.apellido }}</option>
        </select>
      </div>

      <div class="mb-3">
        <label>Método de pago</label>
        <select v-model="form.metodo_pago" class="form-control">
          <option value="">-- Seleccione --</option>
          <option value="efectivo">Efectivo</option>
          <option value="tarjeta">Tarjeta</option>
        </select>
      </div>

      <div class="mb-3">
        <label>Fecha</label>
        <input type="datetime-local" v-model="form.fecha" class="form-control" />
      </div>

      <hr />

      <!-- Productos -->
      <div class="mb-3">
        <h4>Productos</h4>
        <div v-for="(item, index) in form.productos" :key="index" class="row mb-2">
          <div class="col-md-6">
            <select v-model="item.id" class="form-control">
              <option value="">-- Seleccione producto --</option>
              <option v-for="p in productos" :key="p.id" :value="p.id">{{ p.nombre }}</option>
            </select>
          </div>
          <div class="col-md-3">
            <input type="number" v-model.number="item.cantidad" class="form-control" min="1" placeholder="Cantidad" />
          </div>
          <div class="col-md-3">
            <button type="button" class="btn btn-danger w-100" @click="removeProducto(index)">Eliminar</button>
          </div>
        </div>
        <button type="button" class="btn btn-primary" @click="addProducto">Agregar Producto</button>
      </div>

      <!-- Servicios -->
      <div class="mb-3">
        <h4>Servicios</h4>
        <div v-for="(item, index) in form.servicios" :key="index" class="row mb-2">
          <div class="col-md-6">
            <select v-model="item.id" class="form-control">
              <option value="">-- Seleccione servicio --</option>
              <option v-for="s in servicios" :key="s.id" :value="s.id">{{ s.nombre }}</option>
            </select>
          </div>
          <div class="col-md-3">
            <input type="number" v-model.number="item.cantidad" class="form-control" min="1" placeholder="Cantidad" />
          </div>
          <div class="col-md-3">
            <button type="button" class="btn btn-danger w-100" @click="removeServicio(index)">Eliminar</button>
          </div>
        </div>
        <button type="button" class="btn btn-primary" @click="addServicio">Agregar Servicio</button>
      </div>

      <hr />
      <div class="mb-3">
        <h4>Total: ${{ total }}</h4>
      </div>

      <button type="submit" class="btn btn-success">Actualizar Ticket</button>
      <Link href="/tickets" class="btn btn-secondary">Cancelar</Link>
    </form>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
  ticket: Object,
  clientes: Array,
  empleados: Array,
  productos: Array,
  servicios: Array,
});

// Inicialización segura del formulario
const form = ref({
  cliente_id: props.ticket.cliente_id || '',
  empleado_id: props.ticket.empleado_id || '',
  metodo_pago: props.ticket.metodo_pago || '',
  fecha: props.ticket.fecha?.slice(0,16) || '',
  productos: props.ticket.detalleTickets?.filter(d => d.vendible_type.includes('Producto')).map(d => ({
    id: d.vendible_id, cantidad: d.cantidad
  })) || [],
  servicios: props.ticket.detalleTickets?.filter(d => d.vendible_type.includes('Servicio')).map(d => ({
    id: d.vendible_id, cantidad: d.cantidad
  })) || [],
});

// Métodos para agregar/eliminar productos y servicios
const addProducto = () => form.value.productos.push({id:'', cantidad:1});
const removeProducto = (i) => form.value.productos.splice(i,1);
const addServicio = () => form.value.servicios.push({id:'', cantidad:1});
const removeServicio = (i) => form.value.servicios.splice(i,1);

// Total seguro
const total = computed(() => {
  let sum = 0;
  form.value.productos.forEach(p => {
    const prod = props.productos.find(x => x.id === p.id);
    if(prod) sum += Number(prod.precio_venta) * Number(p.cantidad);
  });
  form.value.servicios.forEach(s => {
    const serv = props.servicios.find(x => x.id === s.id);
    if(serv) sum += Number(serv.precio) * Number(s.cantidad);
  });
  return sum.toFixed(2);
});

// Submit
const submit = () => {
  router.put(`/tickets/${props.ticket.id}`, form.value);
};
</script>
