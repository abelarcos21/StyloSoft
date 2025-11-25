<template>
  <div class="salon-wrapper">
    <!-- Navbar Principal -->
    <nav class="main-navbar">
      <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
          <!-- Logo y Título -->
          <div class="brand-section">
            <Link :href="route('dashboard')" class="brand-link">
              <i class="fas fa-cut me-2"></i>
              <span class="brand-text">StyloSoft</span>
            </Link>
          </div>

          <!-- Usuario y Acciones -->
          <div class="user-section">
            <div class="dropdown">
              <button 
                class="btn-user dropdown-toggle" 
                type="button" 
                id="userDropdown" 
                data-bs-toggle="dropdown" 
                aria-expanded="false"
              >
                <i class="fas fa-user-circle me-2"></i>
                <span class="d-none d-md-inline">{{ user?.name || 'Usuario' }}</span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end salon-dropdown" aria-labelledby="userDropdown">
                <li>
                  <Link :href="route('profile.edit')" class="dropdown-item">
                    <i class="fas fa-user text-pink me-2"></i>
                    Mi Perfil
                  </Link>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <Link :href="route('logout')" method="post" as="button" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Cerrar sesión
                  </Link>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Menú Horizontal -->
    <nav class="horizontal-menu">
      <div class="container-fluid">
        <div class="menu-scroll">
          <ul class="nav nav-pills">
            <li class="nav-item">
              <Link 
                :href="route('dashboard')" 
                class="nav-link" 
                :class="{ active: isActive('dashboard') }"
              >
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
              </Link>
            </li>

            <li class="nav-item">
              <Link 
                :href="route('agendas.index')" 
                class="nav-link" 
                :class="{ active: isActive('agendas.index') }"
              >
                <i class="fas fa-calendar-check"></i>
                <span>Agendas</span>
              </Link>
            </li>

            <li class="nav-item">
              <Link 
                :href="route('clientes.index')" 
                class="nav-link" 
                :class="{ active: isActive('clientes.index') }"
              >
                <i class="fas fa-user-friends"></i>
                <span>Clientes</span>
              </Link>
            </li>

            <li class="nav-item">
              <Link 
                :href="route('empleados.index')" 
                class="nav-link" 
                :class="{ active: isActive('empleados.index') }"
              >
                <i class="fas fa-users"></i>
                <span>Empleados</span>
              </Link>
            </li>

            <li class="nav-item">
              <Link 
                :href="route('servicios.index')" 
                class="nav-link" 
                :class="{ active: isActive('servicios.index') }"
              >
                <i class="fas fa-cut"></i>
                <span>Servicios</span>
              </Link>
            </li>

            <li class="nav-item">
              <Link 
                :href="route('productos.index')" 
                class="nav-link" 
                :class="{ active: isActive('productos.index') }"
              >
                <i class="fas fa-box"></i>
                <span>Productos</span>
              </Link>
            </li>

            <li class="nav-item">
              <Link 
                :href="route('tickets.index')" 
                class="nav-link" 
                :class="{ active: isActive('tickets.index') }"
              >
                <i class="fas fa-receipt"></i>
                <span>Tickets</span>
              </Link>
            </li>

            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="fas fa-chart-line"></i>
                <span>Reportes</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="main-content">
      <div class="container-fluid py-3">
        <slot />
      </div>
    </main>

    <!-- Footer -->
    <footer class="salon-footer">
      <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
          <div class="footer-text">
            <i class="fas fa-cut me-2"></i>
            <strong>StyloSoft © {{ new Date().getFullYear() }}</strong>
          </div>
          <div class="footer-links">
            <span class="text-muted">Sistema de Gestión para Salones de Belleza</span>
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
/* Variables de colores - Solo Rosa/Fucsia */
:root {
  --salon-primary: #f093fb;
  --salon-secondary: #f5576c;
  --salon-light-pink: #ffd6f5;
  --salon-deep-pink: #e94057;
}

/* Reset y estructura principal */
.salon-wrapper {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: linear-gradient(135deg, #fff5fd 0%, #ffe8f7 100%);
}

/* Navbar Principal - Rosa/Fucsia */
.main-navbar {
  background: white;
  box-shadow: 0 4px 15px rgba(240, 147, 251, 0.4);
  padding: 1rem 0;
  position: sticky;
  top: 0;
  z-index: 1030;
}

.brand-section {
  display: flex;
  align-items: center;
}

.brand-link {
  color: #f5576c;
  text-decoration: none;
  font-size: 1.5rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  transition: all 0.3s ease;
  letter-spacing: 1px;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

.brand-link:hover {
  color:#f5576c;
  transform: scale(1.05);
  filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.5));
}

.brand-text {
  font-weight: 700;
}

/* Sección de Usuario */
.user-section {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.btn-user {
  background: rgba(255, 255, 255, 0.25);
  border: 2px solid rgba(255, 255, 255, 0.4);
  color: #f5576c;
  padding: 0.6rem 1.2rem;
  border-radius: 25px;
  font-weight: 600;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.btn-user:hover,
.btn-user:focus {
  background: rgba(255, 255, 255, 0.4);
  border-color: #f5576c;
  color: #f5576c;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
}

/* Dropdown mejorado */
.salon-dropdown {
  border: none;
  border-radius: 15px;
  box-shadow: 0 5px 25px rgba(240, 147, 251, 0.3);
  margin-top: 0.5rem;
  min-width: 200px;
  background: white;
}

.salon-dropdown .dropdown-item {
  padding: 0.7rem 1.2rem;
  transition: all 0.3s ease;
  border-radius: 10px;
  margin: 0.2rem 0.5rem;
  color: #495057;
}

.salon-dropdown .dropdown-item:hover {
  background: linear-gradient(135deg, #fff5fd 0%, #ffe8f7 100%);
  transform: translateX(5px);
  color: #f5576c;
}

.text-pink {
  color: #f5576c;
}

/* Menú Horizontal - Rosa/Fucsia */
.horizontal-menu {
  background: white;
  box-shadow: 0 3px 12px rgba(240, 147, 251, 0.2);
  position: sticky;
  top: 72px;
  z-index: 1020;
  border-bottom: 3px solid transparent;
  border-image: linear-gradient(90deg, #f093fb 0%, #f5576c 100%) 1;
}

.menu-scroll {
  overflow-x: auto;
  overflow-y: hidden;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: thin;
  scrollbar-color: #f093fb #ffe8f7;
}

.menu-scroll::-webkit-scrollbar {
  height: 5px;
}

.menu-scroll::-webkit-scrollbar-track {
  background: #ffe8f7;
}

.menu-scroll::-webkit-scrollbar-thumb {
  background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%);
  border-radius: 10px;
}

.horizontal-menu .nav {
  display: flex;
  flex-wrap: nowrap;
  padding: 0.5rem 0;
  gap: 0.5rem;
}

.horizontal-menu .nav-item {
  white-space: nowrap;
}

.horizontal-menu .nav-link {
  color: #6c757d;
  padding: 0.8rem 1.5rem;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
  font-weight: 500;
  border: 2px solid transparent;
  position: relative;
  overflow: hidden;
}

.horizontal-menu .nav-link::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #fff5fd 0%, #ffe8f7 100%);
  transition: left 0.3s ease;
  z-index: -1;
}

.horizontal-menu .nav-link i {
  font-size: 1.1rem;
  color: #f5576c;
  transition: all 0.3s ease;
}

.horizontal-menu .nav-link:hover::before {
  left: 0;
}

.horizontal-menu .nav-link:hover {
  color: #f5576c;
  transform: translateY(-3px);
  border-color: #f093fb;
  box-shadow: 0 4px 12px rgba(240, 147, 251, 0.3);
}

.horizontal-menu .nav-link:hover i {
  transform: scale(1.3) rotate(5deg);
  color: #f5576c;
}

.horizontal-menu .nav-link.active {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  color: white;
  box-shadow: 0 4px 15px rgba(240, 147, 251, 0.5);
  font-weight: 600;
  border-color: transparent;
}

.horizontal-menu .nav-link.active i {
  color: white;
  animation: pulse 2s infinite;
}

.horizontal-menu .nav-link.active::before {
  display: none;
}

/* Contenido Principal */
.main-content {
  flex: 1;
  padding: 1rem 0;
}

/* Footer - Rosa/Fucsia */
.salon-footer {
  background: white;
  border-top: 4px solid transparent;
  border-image: linear-gradient(90deg, #f093fb 0%, #f5576c 100%) 1;
  padding: 1.5rem 0;
  margin-top: auto;
  box-shadow: 0 -3px 12px rgba(240, 147, 251, 0.15);
}

.footer-text {
  color: #495057;
  font-size: 0.95rem;
}

.footer-text strong {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  font-weight: 700;
  font-size: 1.1rem;
}

.footer-text i {
  color: #f5576c;
}

.footer-links {
  font-size: 0.9rem;
  color: #6c757d;
}

/* Responsive */
@media (max-width: 768px) {
  .main-navbar {
    padding: 0.8rem 0;
  }

  .brand-link {
    font-size: 1.2rem;
  }

  .horizontal-menu {
    top: 64px;
  }

  .horizontal-menu .nav-link {
    padding: 0.7rem 1rem;
    font-size: 0.9rem;
  }

  .horizontal-menu .nav-link span {
    display: none;
  }

  .horizontal-menu .nav-link i {
    font-size: 1.4rem;
  }

  .footer-text,
  .footer-links {
    text-align: center;
    width: 100%;
    margin-bottom: 0.5rem;
  }
}

@media (max-width: 576px) {
  .brand-text {
    display: none;
  }

  .btn-user span {
    display: none !important;
  }

  .horizontal-menu .nav {
    justify-content: flex-start;
  }

  .horizontal-menu .nav-link {
    padding: 0.6rem 0.9rem;
  }
}

/* Animaciones */
/* @keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
} */

/* .nav-item {
  animation: slideIn 0.3s ease forwards;
  opacity: 0;
}

.nav-item:nth-child(1) { animation-delay: 0.1s; }
.nav-item:nth-child(2) { animation-delay: 0.15s; }
.nav-item:nth-child(3) { animation-delay: 0.2s; }
.nav-item:nth-child(4) { animation-delay: 0.25s; }
.nav-item:nth-child(5) { animation-delay: 0.3s; }
.nav-item:nth-child(6) { animation-delay: 0.35s; }
.nav-item:nth-child(7) { animation-delay: 0.4s; }
.nav-item:nth-child(8) { animation-delay: 0.45s; } */

/* Efecto de brillo en hover */
/* @keyframes shine {
  0% {
    left: -100%;
  }
  100% {
    left: 100%;
  }
} */

/* .horizontal-menu .nav-link.active::after {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 30%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  animation: shine 3s infinite;
} */
</style>