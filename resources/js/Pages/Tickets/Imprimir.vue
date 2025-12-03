<script setup>
import { onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
  ticket: Object
})

// Función para imprimir
const handlePrint = () => {
  window.print()
}

// Función para cerrar
const handleClose = () => {
  window.close()
}

onMounted(() => {
  // Auto-imprimir cuando carga la página
  setTimeout(() => {
    window.print()
  }, 500)
})
</script>

<template>
  <Head :title="`Ticket ${ticket.numero_ticket}`" />

  <div class="ticket-container">
    <div class="ticket">

      <!-- Header / Logo -->
      <div class="header">
        <h1 class="business-name">TU NEGOCIO</h1>
        <p class="business-info">
          Dirección de tu negocio<br>
          Tel: (123) 456-7890<br>
          RFC: XAXX010101000
        </p>
      </div>

      <!-- Separador -->
      <div class="separator">━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</div>

      <!-- Información del Ticket -->
      <div class="ticket-info">
        <p class="ticket-number">
          <strong>{{ ticket.numero_ticket }}</strong>
        </p>
        <p class="ticket-date">{{ ticket.fecha }}</p>
      </div>

      <!-- Cliente y Empleado -->
      <div class="info-section">
        <p>
          <strong>Cliente:</strong> {{ ticket.cliente }}
        </p>
        <p>
          <strong>Atendió:</strong> {{ ticket.empleado }}
        </p>
      </div>

      <!-- Separador -->
      <div class="separator">━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</div>

      <!-- Detalle de Items -->
      <div class="items-section">
        <table class="items-table">
          <thead>
            <tr>
              <th class="text-left">Descripción</th>
              <th class="text-center">Cant</th>
              <th class="text-right">Precio</th>
              <th class="text-right">Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(detalle, index) in ticket.detalles" :key="index">
              <td class="text-left item-name">{{ detalle.nombre }}</td>
              <td class="text-center">{{ detalle.cantidad }}</td>
              <td class="text-right">${{ detalle.precio_unitario }}</td>
              <td class="text-right">${{ detalle.subtotal }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Separador -->
      <div class="separator">━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</div>

      <!-- Totales -->
      <div class="totals-section">
        <div class="total-row">
          <span>Subtotal:</span>
          <span>${{ ticket.subtotal }}</span>
        </div>

        <div class="total-row" v-if="parseFloat(ticket.descuento) > 0">
          <span>Descuento:</span>
          <span class="text-danger">-${{ ticket.descuento }}</span>
        </div>

        <div class="total-row">
          <span>IVA (16%):</span>
          <span>${{ ticket.impuesto }}</span>
        </div>

        <div class="separator-line"></div>

        <div class="total-row total-final">
          <span>TOTAL:</span>
          <span>${{ ticket.total }}</span>
        </div>
      </div>

      <!-- Método de Pago -->
      <div class="payment-section">
        <p>
          <strong>Método de pago:</strong>
          <span class="text-uppercase">{{ ticket.metodo_pago }}</span>
        </p>
      </div>

      <!-- Separador -->
      <div class="separator">━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</div>

      <!-- Footer -->
      <div class="footer">
        <p class="thanks">¡Gracias por su compra!</p>
        <p class="small-text">
          Conserve este ticket como comprobante<br>
          de su compra
        </p>
      </div>

    </div>

    <!-- Botones de control (solo en pantalla) -->
    <div class="no-print controls">
      <button @click="handlePrint" class="btn btn-primary">
        <i class="fas fa-print"></i> Imprimir
      </button>
      <button @click="handleClose" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cerrar
      </button>
    </div>
  </div>
</template>

<style scoped>
/* Estilos generales */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Courier New', monospace;
  background-color: #f5f5f5;
}

.ticket-container {
  max-width: 350px;
  margin: 20px auto;
  padding: 20px;
}

.ticket {
  background: white;
  padding: 20px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  border-radius: 5px;
}

/* Header */
.header {
  text-align: center;
  margin-bottom: 15px;
}

.business-name {
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.business-info {
  font-size: 11px;
  line-height: 1.4;
  color: #555;
}

/* Separadores */
.separator {
  text-align: center;
  font-size: 10px;
  color: #999;
  margin: 10px 0;
  letter-spacing: -1px;
}

.separator-line {
  border-top: 1px dashed #999;
  margin: 8px 0;
}

/* Información del Ticket */
.ticket-info {
  text-align: center;
  margin-bottom: 10px;
}

.ticket-number {
  font-size: 16px;
  font-weight: bold;
  margin-bottom: 5px;
}

.ticket-date {
  font-size: 11px;
  color: #666;
}

/* Sección de información */
.info-section {
  margin-bottom: 10px;
  font-size: 12px;
  line-height: 1.6;
}

.info-section p {
  margin-bottom: 3px;
}

/* Tabla de items */
.items-section {
  margin: 10px 0;
}

.items-table {
  width: 100%;
  font-size: 11px;
  border-collapse: collapse;
}

.items-table th {
  padding: 5px 2px;
  border-bottom: 1px solid #333;
  font-weight: bold;
  font-size: 10px;
}

.items-table td {
  padding: 5px 2px;
  border-bottom: 1px dashed #ddd;
}

.items-table tbody tr:last-child td {
  border-bottom: none;
}

.item-name {
  max-width: 150px;
  word-wrap: break-word;
  line-height: 1.3;
}

.text-left {
  text-align: left;
}

.text-center {
  text-align: center;
}

.text-right {
  text-align: right;
}

/* Totales */
.totals-section {
  margin: 10px 0;
  font-size: 12px;
}

.total-row {
  display: flex;
  justify-content: space-between;
  padding: 3px 0;
}

.total-final {
  font-size: 16px;
  font-weight: bold;
  margin-top: 5px;
}

.text-danger {
  color: #dc3545;
}

/* Pago */
.payment-section {
  text-align: center;
  margin: 10px 0;
  font-size: 12px;
}

/* Footer */
.footer {
  text-align: center;
  margin-top: 15px;
}

.thanks {
  font-size: 14px;
  font-weight: bold;
  margin-bottom: 8px;
}

.small-text {
  font-size: 10px;
  color: #666;
  line-height: 1.4;
}

/* Controles */
.controls {
  text-align: center;
  margin-top: 20px;
  display: flex;
  gap: 10px;
  justify-content: center;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
  font-family: Arial, sans-serif;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s;
}

.btn-primary {
  background-color: #007bff;
  color: white;
}

.btn-primary:hover {
  background-color: #0056b3;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background-color: #545b62;
}

/* Estilos de impresión */
@media print {
  body {
    background: white;
  }

  .ticket-container {
    max-width: 80mm;
    margin: 0;
    padding: 0;
  }

  .ticket {
    box-shadow: none;
    border-radius: 0;
    padding: 5mm;
  }

  .no-print {
    display: none !important;
  }

  /* Ajustes para ticket térmico */
  .business-name {
    font-size: 18px;
  }

  .business-info {
    font-size: 9px;
  }

  .separator {
    font-size: 8px;
  }

  .ticket-info,
  .info-section,
  .payment-section {
    font-size: 10px;
  }

  .items-table {
    font-size: 9px;
  }

  .items-table th {
    font-size: 8px;
  }

  .totals-section {
    font-size: 10px;
  }

  .total-final {
    font-size: 14px;
  }

  .thanks {
    font-size: 12px;
  }

  .small-text {
    font-size: 8px;
  }

  /* Evitar saltos de página innecesarios */
  .ticket {
    page-break-inside: avoid;
  }
}

/* Para impresoras de 58mm */
@media print and (max-width: 58mm) {
  .ticket-container {
    max-width: 58mm;
  }

  .business-name {
    font-size: 16px;
  }

  .items-table th:nth-child(3),
  .items-table td:nth-child(3) {
    display: none; /* Ocultar precio unitario en tickets pequeños */
  }
}
</style>
