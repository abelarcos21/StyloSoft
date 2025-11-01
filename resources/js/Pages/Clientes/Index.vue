<script setup>
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'

const props = defineProps({
  clientes: Object, // paginado desde el backend
  filters: Object
})

const search = ref(props.filters?.search || '')

function filtrar() {
  router.get('/clientes', { search: search.value }, {
    preserveState: true,
    replace: true,
  })
}

function eliminarCliente(id) {
  if (confirm('¿Deseas eliminar este cliente?')) {
    router.delete(`/clientes/${id}`)
  }
}
</script>

<template>
  <AdminLayout title="Clientes">
    <div class="container-fluid px-3">

      <!-- Encabezado -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
        <h1 class="h4 mb-0 text-primary fw-bold">
          <i class="fas fa-user-friends me-2"></i> Clientes
        </h1>
        <Link href="/clientes/create" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
          <i class="fas fa-plus"></i> Nuevo cliente
        </Link>
      </div>

      <!-- Mensaje de éxito -->
      <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ $page.props.flash.success }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>

      <!-- Filtro -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <div class="row g-2 align-items-center">
            <div class="col-12 col-md-6 col-lg-4">
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                <input
                  type="search"
                  v-model="search"
                  @input="filtrar"
                  class="form-control"
                  placeholder="Buscar cliente por nombre, email o teléfono..."
                />
              </div>
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
                <th>Nombre completo</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="cliente in props.clientes.data" :key="cliente.id" class="text-center">
                <td>{{ cliente.id }}</td>
                <td class="text-start">
                  <strong>{{ cliente.nombre }} {{ cliente.apellido }}</strong>
                  <br />
                  <small class="text-muted">ID: {{ cliente.id }}</small>
                </td>
                <td>{{ cliente.email || '-' }}</td>
                <td>{{ cliente.telefono || '-' }}</td>
                <td>{{ cliente.direccion || '-' }}</td>
                <td>
                  <div class="btn-group" role="group">
                    <Link :href="`/clientes/${cliente.id}/edit`" class="btn btn-sm btn-outline-warning" title="Editar">
                      <i class="fas fa-edit"></i>
                    </Link>
                    <button
                      @click.prevent="eliminarCliente(cliente.id)"
                      class="btn btn-sm btn-outline-danger"
                      title="Eliminar"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!props.clientes.data.length">
                <td colspan="6" class="text-center text-muted py-4">
                  <i class="fas fa-inbox fa-2x mb-2"></i><br />
                  No hay clientes registrados.
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
