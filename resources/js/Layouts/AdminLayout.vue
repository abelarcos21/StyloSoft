<template>
  <div class="salon-wrapper">
    <nav class="main-navbar bg-white border-bottom">
      <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center">
          <div class="brand-section py-2">
            <Link :href="route('dashboard')" class="brand-link">
              <div class="brand-icon-wrapper me-2">
                <i class="fas fa-cut"></i>
              </div>
              <span class="brand-text text-dark">StyloSoft</span>
            </Link>
          </div>

          <div class="user-section">
            <div class="dropdown">
              <button
                class="btn-user dropdown-toggle"
                type="button"
                id="userDropdown"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                <i class="fas fa-user-circle me-2 fs-5 text-muted"></i>
                <span class="d-none d-md-inline fw-medium">{{ user?.name || 'Usuario' }}</span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" aria-labelledby="userDropdown">
                <li>
                  <Link :href="route('profile.edit')" class="dropdown-item py-2">
                    <i class="fas fa-user me-2 text-muted"></i> Mi Perfil
                  </Link>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <Link :href="route('logout')" method="post" as="button" class="dropdown-item py-2 text-danger">
                    <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
                  </Link>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <nav class="horizontal-menu bg-white shadow-sm">
      <div class="container-fluid px-0 px-md-4">
        <div class="menu-scroll">
          <ul class="nav flex-nowrap px-3 px-md-0 py-2">
            <li class="nav-item flex-shrink-0">
              <Link :href="route('dashboard')" class="nav-link" :class="{ active: isActive('dashboard') }">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
              </Link>
            </li>

            <li class="nav-item flex-shrink-0">
              <Link :href="route('agendas.index')" class="nav-link" :class="{ active: isActive('agendas.index') }">
                <i class="fas fa-calendar-check"></i>
                <span>Agendas</span>
              </Link>
            </li>

            <li class="nav-item flex-shrink-0">
              <Link :href="route('clientes.index')" class="nav-link" :class="{ active: isActive('clientes.index') }">
                <i class="fas fa-user-friends"></i>
                <span>Clientes</span>
              </Link>
            </li>

            <li class="nav-item flex-shrink-0">
              <Link :href="route('empleados.index')" class="nav-link" :class="{ active: isActive('empleados.index') }">
                <i class="fas fa-users"></i>
                <span>Empleados</span>
              </Link>
            </li>

            <li class="nav-item flex-shrink-0">
              <Link :href="route('servicios.index')" class="nav-link" :class="{ active: isActive('servicios.index') }">
                <i class="fas fa-cut"></i>
                <span>Servicios</span>
              </Link>
            </li>

            <li class="nav-item flex-shrink-0">
              <Link :href="route('productos.index')" class="nav-link" :class="{ active: isActive('productos.index') }">
                <i class="fas fa-box"></i>
                <span>Productos</span>
              </Link>
            </li>

            <li class="nav-item flex-shrink-0">
              <Link :href="route('tickets.index')" class="nav-link" :class="{ active: isActive('tickets.index') }">
                <i class="fas fa-receipt"></i>
                <span>Tickets</span>
              </Link>
            </li>

            <li class="nav-item flex-shrink-0">
              <a href="/reportes" class="nav-link">
                <i class="fas fa-chart-line"></i>
                <span>Reportes</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="main-content flex-grow-1">
      <slot />
    </main>

    <footer class="bg-white border-top py-4 mt-auto">
      <div class="container-fluid px-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
          <div class="text-dark fw-bold d-flex align-items-center">
            <i class="fas fa-cut brand-accent me-2"></i>
            StyloSoft © {{ new Date().getFullYear() }}
          </div>
          <div class="text-muted small">
            Sistema de Gestión para Salones de Belleza
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const { props } = usePage()
const user = props.auth?.user || {}

function isActive(routeName) {
  return route().current(routeName)
}
</script>

<style scoped>
/* Variables de colores refinadas */
:root {
  --salon-primary: #d84b72; /* Un rosa quemado/elegante en lugar de fucsia neón */
  --salon-bg: #f8f9fa; /* Fondo gris muy suave para resaltar tarjetas blancas */
  --salon-text: #2b2b2b;
}

/* Reset y estructura principal */
.salon-wrapper {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: var(--salon-bg);
}

/* Navbar Principal */
.main-navbar {
  position: sticky;
  top: 0;
  z-index: 1030;
}

.brand-link {
  text-decoration: none;
  font-size: 1.25rem;
  display: flex;
  align-items: center;
  transition: opacity 0.2s;
}

.brand-link:hover {
  opacity: 0.8;
}

.brand-icon-wrapper {
  background-color: var(--salon-primary);
  color: white;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  font-size: 0.9rem;
}

.brand-text {
  font-weight: 800;
  letter-spacing: -0.5px;
}

.brand-accent {
  color: var(--salon-primary);
}

/* Sección de Usuario */
.btn-user {
  background: transparent;
  border: 1px solid #e9ecef;
  color: var(--salon-text);
  padding: 0.4rem 1rem;
  border-radius: 20px;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
}

.btn-user:hover,
.btn-user:focus {
  background: #f8f9fa;
  border-color: #dee2e6;
}

.dropdown-menu .dropdown-item:hover {
  background-color: #f8f9fa;
  color: var(--salon-primary);
}

/* Menú Horizontal */
.horizontal-menu {
  position: sticky;
  top: 61px; /* Ajustar según la altura de tu navbar en móvil/desktop */
  z-index: 1020;
}

.menu-scroll {
  overflow-x: auto;
  overflow-y: hidden;
  -webkit-overflow-scrolling: touch;
  /* Ocultar scrollbar para un look de App nativa */
  scrollbar-width: none; /* Firefox */
}

.menu-scroll::-webkit-scrollbar {
  display: none; /* Chrome, Safari, Edge */
}

.nav-link {
  color: #6c757d;
  padding: 0.75rem 1rem;
  margin-right: 0.5rem;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 500;
  font-size: 0.95rem;
  transition: all 0.2s ease;
  border: 1px solid transparent;
}

.nav-link i {
  font-size: 1.1rem;
  opacity: 0.7;
}

.nav-link:hover {
  color: var(--salon-text);
  background-color: #f8f9fa;
}

/* Estado Activo Elegante */
.nav-link.active {
  color: var(--salon-primary);
  background-color: rgba(216, 75, 114, 0.08); /* Fondo con opacidad del color primario */
  font-weight: 600;
}

.nav-link.active i {
  opacity: 1;
}

/* Responsive */
@media (max-width: 768px) {
  .horizontal-menu {
    top: 56px;
  }

  .nav-link {
    flex-direction: column;
    padding: 0.5rem 0.8rem;
    gap: 0.2rem;
    font-size: 0.75rem;
  }

  .nav-link i {
    font-size: 1.25rem;
  }

  /* En móvil no ocultamos el texto, lo ponemos debajo del icono para mejor UX */
  .nav-link span {
    display: block;
  }
}
</style>
