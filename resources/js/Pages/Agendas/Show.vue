<template>
  <AdminLayout title="Detalle de Agenda">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-calendar"></i> Detalles de la Agenda</h3>
      </div>

      <div class="card-body">
        <p><strong>Cliente:</strong> {{ agenda.cliente.nombre }}</p>
        <p><strong>Empleado:</strong> {{ agenda.empleado.nombre }}</p>
        <p><strong>Servicio:</strong> {{ agenda.servicio.nombre }}</p>
        <p><strong>Fecha y Hora:</strong> {{ new Date(agenda.fecha_hora).toLocaleString() }}</p>
        <p><strong>Estado:</strong>
          <span class="badge"
                :class="{
                  'badge-warning': agenda.estado === 'pendiente',
                  'badge-info': agenda.estado === 'confirmada',
                  'badge-success': agenda.estado === 'completada',
                  'badge-danger': agenda.estado === 'cancelada'
                }">
            {{ agenda.estado }}
          </span>
        </p>
      </div>

      <div class="card-footer text-right">
        <Link href="/agendas" class="btn btn-secondary">Volver</Link>
        <Link :href="`/agendas/${agenda.id}/edit`" class="btn btn-warning">Editar</Link>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({ agenda: Object })
</script>
