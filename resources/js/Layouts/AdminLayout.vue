<template>
  <div class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Navbar -->
      <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Botón para colapsar sidebar -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
              <i class="fas fa-bars"></i>
            </a>
          </li>
          <li class="nav-item d-none d-sm-inline-block">
            <Link href="/" class="nav-link">Inicio</Link>
          </li>
        </ul>

        <!-- Usuario / Configuración -->
        <ul class="navbar-nav ml-auto">
          <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
              <i class="fas fa-user-circle"></i> {{ user?.name || 'Usuario' }}
            </a>
            <div class="dropdown-menu dropdown-menu-right">
              <Link href="/perfil" class="dropdown-item">
                <i class="fas fa-user mr-2"></i> Perfil
              </Link>
              <div class="dropdown-divider"></div>
              <Link href="/logout" method="post" as="button" class="dropdown-item text-danger">
                <i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
              </Link>
            </div>
          </li>
        </ul>
      </nav>

      <!-- Sidebar -->
      <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Logo -->
        <a href="/" class="brand-link text-center">
          <span class="brand-text font-weight-light">💈 StyloSoft</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
          <!-- Menú lateral -->
          <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
              <li class="nav-item">
                <Link href="/dashboard" class="nav-link" :class="{ active: $page.url.startsWith('/dashboard') }">
                  <i class="nav-icon fas fa-tachometer-alt"></i>
                  <p>Dashboard</p>
                </Link>
              </li>
              <li class="nav-item">
                <Link href="/agendas" class="nav-link" :class="{ active: $page.url.startsWith('/agendas') }">
                  <i class="nav-icon fas fa-cash-register"></i>
                  <p>Agendas</p>
                </Link>
              </li>
              <li class="nav-item">
                <Link href="/productos" class="nav-link" :class="{ active: $page.url.startsWith('/productos') }">
                  <i class="nav-icon fas fa-box"></i>
                  <p>Productos</p>
                </Link>
              </li>
              <li class="nav-item">
                <Link href="/servicios" class="nav-link" :class="{ active: $page.url.startsWith('/servicios') }">
                  <i class="nav-icon fas fa-box"></i>
                  <p>Servicios</p>
                </Link>
              </li>
              <li class="nav-item">
                <Link href="/empleados" class="nav-link" :class="{ active: $page.url.startsWith('/empleados') }">
                  <i class="nav-icon fas fa-box"></i>
                  <p>Empleados</p>
                </Link>
              </li>
               <li class="nav-item">
                <Link href="/clientes" class="nav-link" :class="{ active: $page.url.startsWith('/clientes') }">
                  <i class="nav-icon fas fa-box"></i>
                  <p>Clientes</p>
                </Link>
              </li>
              <li class="nav-item">
                <Link href="/tickets" class="nav-link" :class="{ active: $page.url.startsWith('/tickets') }">
                  <i class="nav-icon fas fa-box"></i>
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
const { props } = usePage()
const user = props.auth?.user || {}
</script>

<style scoped>
.nav-link.active {
  background-color: #007bff !important;
  color: #fff !important;
}
</style>
