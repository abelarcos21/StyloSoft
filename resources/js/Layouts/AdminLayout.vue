<template>
  <div class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Navbar -->
      <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
              <i class="fas fa-bars"></i>
            </a>
          </li>
          <li class="nav-item d-none d-sm-inline-block">
            <Link :href="route('dashboard')" class="nav-link">Inicio</Link>
          </li>
        </ul>

        <ul class="navbar-nav ml-auto">
          <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
              <i class="fas fa-user-circle"></i> {{ user?.name || 'Usuario' }}
            </a>
            <div class="dropdown-menu dropdown-menu-right">
              <Link :href="route('perfil')" class="dropdown-item">
                <i class="fas fa-user mr-2"></i> Perfil
              </Link>
              <div class="dropdown-divider"></div>
              <Link :href="route('logout')" method="post" as="button" class="dropdown-item text-danger">
                <i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
              </Link>
            </div>
          </li>
        </ul>
      </nav>

      <!-- Sidebar -->
      <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a :href="route('dashboard')" class="brand-link text-center">
          <span class="brand-text font-weight-light">💈 StyloSoft</span>
        </a>

        <div class="sidebar">
          <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
              <li class="nav-item">
                <Link :href="route('dashboard')" class="nav-link" :class="{ active: isActive('dashboard') }">
                  <i class="nav-icon fas fa-tachometer-alt"></i>
                  <p>Dashboard</p>
                </Link>
              </li>
              <li class="nav-item">
                <Link :href="route('agendas.index')" class="nav-link" :class="{ active: isActive('agendas.index') }">
                  <i class="nav-icon fas fa-calendar-alt"></i>
                  <p>Agendas</p>
                </Link>
              </li>
              <li class="nav-item">
                <Link :href="route('productos.index')" class="nav-link" :class="{ active: isActive('productos.index') }">
                  <i class="nav-icon fas fa-boxes"></i>
                  <p>Productos</p>
                </Link>
              </li>
              <li class="nav-item">
                <Link :href="route('servicios.index')" class="nav-link" :class="{ active: isActive('servicios.index') }">
                  <i class="nav-icon fas fa-hand-sparkles"></i>
                  <p>Servicios</p>
                </Link>
              </li>
              <li class="nav-item">
                <Link :href="route('empleados.index')" class="nav-link" :class="{ active: isActive('empleados.index') }">
                  <i class="nav-icon fas fa-users"></i>
                  <p>Empleados</p>
                </Link>
              </li>
              <li class="nav-item">
                <Link :href="route('clientes.index')" class="nav-link" :class="{ active: isActive('clientes.index') }">
                  <i class="nav-icon fas fa-user-friends"></i>
                  <p>Clientes</p>
                </Link>
              </li>
              <li class="nav-item">
                <Link :href="route('tickets.index')" class="nav-link" :class="{ active: isActive('tickets.index') }">
                  <i class="nav-icon fas fa-receipt"></i>
                  <p>Tickets</p>
                </Link>
              </li>
            </ul>
          </nav>
        </div>
      </aside>

      <!-- Contenido principal -->
      <div class="content-wrapper">
        <section class="content pt-3">
          <div class="container-fluid">
            <slot />
          </div>
        </section>
      </div>

      <!-- Footer -->
      <footer class="main-footer text-sm text-center">
        <strong>StyloSoft © {{ new Date().getFullYear() }}</strong> | Todos los derechos reservados.
      </footer>
    </div>
  </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const { props } = usePage()
const user = props.auth?.user || {}

// Función para marcar link activo
function isActive(routeName) {
  return route(routeName) === window.location.pathname
}
</script>

<style scoped>
.nav-link.active {
  background-color: #007bff !important;
  color: #fff !important;
}
</style>
