<template>
  <AdminLayout title="Detalle de Agenda">
    <div class="card shadow-sm border-0">

      <!-- Encabezado -->
      <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
          <i class="fas fa-calendar"></i> Detalles de la Agenda
        </h3>

        <!-- Tipo de agenda -->
        <span
          class="badge fs-6 px-3 py-2"
          :class="agenda.tipo === 'flash' ? 'bg-warning text-dark' : 'bg-primary'"
        >
          {{ agenda.tipo.toUpperCase() }}
        </span>
      </div>

      <!-- Cuerpo -->
      <div class="card-body">

        <!-- Cliente -->
        <p class="mb-2">
          <strong><i class="fas fa-user"></i> Cliente:</strong>
          {{ agenda.cliente.nombre }}
        </p>

        <!-- Empleado -->
        <p class="mb-2">
          <strong><i class="fas fa-user-tie"></i> Empleado:</strong>
          {{ agenda.empleado.nombre }}
        </p>

        <!-- Servicios -->
        <p class="mb-1">
          <strong><i class="fas fa-cut"></i> Servicios:</strong>
        </p>

        <div class="mb-3">
          <span
            v-for="s in agenda.servicios"
            :key="s.id"
            class="badge bg-secondary me-1 mb-1 px-3 py-2"
          >
            {{ s.nombre }}
          </span>

          <span v-if="agenda.servicios.length === 0" class="text-muted">
            No tiene servicios asignados
          </span>
        </div>

        <!-- Fecha y hora -->
        <p class="mb-2">
          <strong><i class="fas fa-clock"></i> Fecha y Hora:</strong>
          {{ new Date(agenda.fecha_hora).toLocaleString("es-MX") }}
        </p>

        <!-- Estado -->
        <p class="mb-0">
          <strong><i class="fas fa-flag"></i> Estado:</strong>
          <span class="badge px-3 py-2 fs-6"
            :class="{
              'bg-warning text-dark': agenda.estado === 'pendiente',
              'bg-info': agenda.estado === 'confirmada',
              'bg-success': agenda.estado === 'completada',
              'bg-danger': agenda.estado === 'cancelada'
            }"
          >
            {{ agenda.estado.toUpperCase() }}
          </span>
        </p>

      </div>

      <!-- Footer -->
      <div class="card-footer text-end">
        <Link href="/agendas" class="btn btn-secondary me-2">
          <i class="fas fa-arrow-left"></i> Volver
        </Link>

        <Link :href="`/agendas/${agenda.id}/edit`" class="btn btn-warning">
          <i class="fas fa-edit"></i> Editar
        </Link>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
  agenda: Object
})
</script>
