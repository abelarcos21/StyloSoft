<template>
  <AdminLayout title="Nuevo Ticket">
    <h2>Nuevo Ticket</h2>

    <form @submit.prevent="submit">
      <!-- Cliente y Empleado -->
      <div class="card p-4 mb-4">
        <div class="row mb-3">
          <div class="col-md-6">
            <label>Cliente</label>
            <select v-model="form.cliente_id" class="form-control">
              <option value="">-- Seleccionar Cliente --</option>
              <option v-for="c in clientes" :key="c.id" :value="c.id">
                {{ c.nombre }} {{ c.apellido }}
              </option>
            </select>
          </div>
          <div class="col-md-6">
            <label>Empleado</label>
            <select v-model="form.empleado_id" class="form-control">
              <option value="">-- Seleccionar Empleado --</option>
              <option v-for="e in empleados" :key="e.id" :value="e.id">
                {{ e.nombre }} {{ e.apellido }}
              </option>
            </select>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label>Fecha</label>
            <input type="datetime-local" v-model="form.fecha" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label>Método de Pago</label>
            <input type="text" v-model="form.metodo_pago" class="form-control" placeholder="Efectivo, Tarjeta...">
          </div>
        </div>
      </div>

      <!-- Productos -->
      <div class="card p-4 mb-4">
        <h4>Productos</h4>
        <div v-for="(item, index) in form.productos" :key="index" class="row mb-2">
          <div class="col-md-6">
            <select v-model="item.id" class="form-control">
              <option value="">-- Seleccionar Producto --</option>
              <option v-for="p in productos" :key="p.id" :value="p.id">
                {{ p.nombre }} - ${{ safeNumber(p.precio_venta).toFixed(2) }}
              </option>
            </select>
          </div>
          <div class="col-md-3">
            <input type="number" v-model.number="item.cantidad" class="form-control" min="1" placeholder="Cantidad">
          </div>
          <div class="col-md-3">
            <button type="button" class="btn btn-danger w-100" @click="removeProducto(index)">Eliminar</button>
          </div>
        </div>
        <button type="button" class="btn btn-primary" @click="addProducto">Agregar Producto</button>
      </div>

      <!-- Servicios -->
      <div class="card p-4 mb-4">
        <h4>Servicios</h4>
        <div v-for="(item, index) in form.servicios" :key="index" class="row mb-2">
          <div class="col-md-6">
            <select v-model="item.id" class="form-control">
              <option value="">-- Seleccionar Servicio --</option>
              <option v-for="s in servicios" :key="s.id" :value="s.id">
                {{ s.nombre }} - ${{ safeNumber(s.precio).toFixed(2) }}
              </option>
            </select>
          </div>
          <div class="col-md-3">
            <input type="number" v-model.number="item.cantidad" class="form-control" min="1" placeholder="Cantidad">
          </div>
          <div class="col-md-3">
            <button type="button" class="btn btn-danger w-100" @click="removeServicio(index)">Eliminar</button>
          </div>
        </div>
        <button type="button" class="btn btn-primary" @click="addServicio">Agregar Servicio</button>
      </div>

      <!-- Total -->
      <div class="mb-3">
        <h4>Total: ${{ total.toFixed(2) }}</h4>
      </div>

      <button type="submit" class="btn btn-success">Guardar Ticket</button>
      <Link href="/tickets" class="btn btn-secondary">Cancelar</Link>
    </form>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
  clientes: Array,
  empleados: Array,
  productos: Array,
  servicios: Array,
});

// Función para evitar errores con null o string
const safeNumber = (value) => Number(value || 0);

const form = ref({
  cliente_id: '',
  empleado_id: '',
  metodo_pago: '',
  fecha: new Date().toISOString().slice(0,16),
  productos: [],
  servicios: [],
});

const addProducto = () => form.value.productos.push({id:'', cantidad:1});
const removeProducto = (index) => form.value.productos.splice(index,1);
const addServicio = () => form.value.servicios.push({id:'', cantidad:1});
const removeServicio = (index) => form.value.servicios.splice(index,1);

const total = computed(() => {
  let sum = 0;
  form.value.productos.forEach(p => {
    const prod = props.productos.find(x => x.id === p.id);
    if(prod) sum += safeNumber(prod.precio_venta) * p.cantidad;
  });
  form.value.servicios.forEach(s => {
    const serv = props.servicios.find(x => x.id === s.id);
    if(serv) sum += safeNumber(serv.precio) * s.cantidad;
  });
  return sum;
});

const submit = () => {
  router.post('/tickets', form.value);
};
</script>
