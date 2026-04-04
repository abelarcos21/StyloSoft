<script setup>
import { onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
  ticket: Object
})

// Helper de Moneda para mantener consistencia
const formatoMoneda = (valor) => {
  if (valor === undefined || valor === null) return '$0.00'
  return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(valor)
}

const handlePrint = () => {
  window.print()
}

const handleClose = () => {
  window.close()
}

onMounted(() => {
  // Escucha cuando el diálogo de impresión se cierra (ya sea por imprimir o cancelar)
  window.onafterprint = () => {
    window.close()
  }

  // Auto-imprimir cuando carga la página
  setTimeout(() => {
    window.print()
  }, 500)
})
</script>

<template>
  <Head :title="`Ticket #${ticket.numero_ticket}`" />

  <div class="ticket-container">
    <div class="ticket">

      <div class="header">
        <h1 class="business-name">TU NEGOCIO</h1>
        <p class="business-info">
          Dirección de tu negocio<br>
          Tel: (123) 456-7890<br>
          RFC: XAXX010101000
        </p>
      </div>

      <div class="divider"></div>

      <div class="ticket-info">
        <p class="ticket-number">
          <strong>Folio: {{ ticket.numero_ticket }}</strong>
        </p>
        <p class="ticket-date">{{ ticket.fecha }}</p>
      </div>

      <div class="info-section">
        <p>
          <strong>Cliente:</strong> {{ typeof ticket.cliente === 'object' ? ticket.cliente?.nombre_completo : ticket.cliente || 'General' }}
        </p>
        <p>
          <strong>Atendió:</strong> {{ typeof ticket.empleado === 'object' ? ticket.empleado?.nombre_completo : ticket.empleado || 'Cajero' }}
        </p>
      </div>

      <div class="divider"></div>

      <div class="items-section">
        <table class="items-table">
          <thead>
            <tr>
              <th class="text-left">CANT/DESC</th>
              <th class="text-right">IMPORTE</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="detalle in ticket.detalles" :key="detalle.id">
              <td class="text-left item-cell">
                <div class="item-name">{{ detalle.nombre }}</div>
                <div class="item-qty">{{ detalle.cantidad }} x {{ formatoMoneda(detalle.precio_unitario) }}</div>
              </td>
              <td class="text-right vertical-bottom">
                {{ formatoMoneda(detalle.subtotal) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="divider"></div>

      <div class="totals-section">
        <div class="total-row">
          <span>Subtotal:</span>
          <span>{{ formatoMoneda(ticket.subtotal) }}</span>
        </div>

        <div class="total-row" v-if="parseFloat(ticket.descuento) > 0">
          <span>Descuento:</span>
          <span class="text-danger">-{{ formatoMoneda(ticket.descuento) }}</span>
        </div>

        <div class="total-row">
          <span>IVA (16%):</span>
          <span>{{ formatoMoneda(ticket.impuesto) }}</span>
        </div>

        <div class="divider-dashed"></div>

        <div class="total-row total-final">
          <span>TOTAL:</span>
          <span>{{ formatoMoneda(ticket.total) }}</span>
        </div>
      </div>

      <div class="payment-section">
        <p>
          Pago en: <span class="text-uppercase fw-bold">{{ ticket.metodo_pago }}</span>
        </p>
      </div>

      <div class="divider"></div>

      <div class="footer">
        <p class="thanks">¡Gracias por su compra!</p>
        <p class="small-text">
          Conserve este ticket como comprobante<br>
          de su transacción.
        </p>
      </div>

    </div>

    <div class="no-print controls">
      <button @click="handlePrint" class="btn btn-brand rounded-pill px-4">
        <i class="fas fa-print me-2"></i> Imprimir
      </button>
      <button @click="handleClose" class="btn btn-light border rounded-pill px-4">
        <i class="fas fa-times me-2"></i> Cerrar
      </button>
    </div>
  </div>
</template>

<style scoped>
/* Estilos generales de la vista (Pantalla) */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  background-color: #f8f9fa;
}

/* Tipografía optimizada para tickets */
.ticket-container {
  font-family: 'Courier New', Courier, monospace;
  max-width: 380px;
  margin: 40px auto;
  padding: 20px;
  color: #000;
}

.ticket {
  background: white;
  padding: 30px 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  border-radius: 8px;
}

/* Header */
.header {
  text-align: center;
}

.business-name {
  font-size: 22px;
  font-weight: bold;
  margin-bottom: 5px;
  text-transform: uppercase;
  font-family: Arial, sans-serif; /* A veces el logo/nombre se ve mejor en Sans-serif */
}

.business-info {
  font-size: 12px;
  line-height: 1.4;
}

/* Separadores CSS (Mejor que texto para impresoras térmicas) */
.divider {
  border-top: 1px solid #000;
  margin: 12px 0;
}

.divider-dashed {
  border-top: 1px dashed #000;
  margin: 8px 0;
}

/* Información del Ticket */
.ticket-info {
  text-align: center;
}

.ticket-number {
  font-size: 16px;
}

.ticket-date {
  font-size: 12px;
  margin-top: 2px;
}

/* Sección de información */
.info-section {
  font-size: 12px;
  line-height: 1.6;
}

/* Tabla de items (Optimizada a 2 columnas para 58mm y 80mm) */
.items-table {
  width: 100%;
  font-size: 12px;
  border-collapse: collapse;
}

.items-table th {
  padding: 4px 0;
  border-bottom: 1px solid #000;
  font-size: 11px;
}

.item-cell {
  padding: 8px 0;
  border-bottom: 1px dashed #ccc;
}

.vertical-bottom {
  vertical-align: bottom;
  padding-bottom: 8px;
  border-bottom: 1px dashed #ccc;
}

.item-name {
  font-weight: bold;
  word-wrap: break-word;
  line-height: 1.2;
}

.item-qty {
  font-size: 11px;
  margin-top: 3px;
}

.items-table tbody tr:last-child td {
  border-bottom: none;
}

.text-left { text-align: left; }
.text-right { text-align: right; }

/* Totales */
.totals-section {
  font-size: 13px;
}

.total-row {
  display: flex;
  justify-content: space-between;
  padding: 2px 0;
}

.total-final {
  font-size: 18px;
  font-weight: bold;
  margin-top: 5px;
}

/* Utilidades */
.text-uppercase { text-transform: uppercase; }
.fw-bold { font-weight: bold; }

/* Pago */
.payment-section {
  text-align: center;
  font-size: 14px;
}

/* Footer */
.footer {
  text-align: center;
}

.thanks {
  font-size: 15px;
  font-weight: bold;
  margin-bottom: 5px;
}

.small-text {
  font-size: 11px;
  line-height: 1.4;
}

/* Controles de UI (Pantalla) */
.controls {
  margin-top: 30px;
  display: flex;
  gap: 10px;
  justify-content: center;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}

.btn-brand {
  background-color: #d84b72;
  border: 1px solid #d84b72;
  color: white;
  padding: 10px 20px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-brand:hover {
  background-color: #c03e61;
}

/* Reglas de Impresión */
@media print {
  body {
    background: transparent;
  }

  .ticket-container {
    max-width: 80mm; /* Tamaño estándar grande */
    margin: 0;
    padding: 0;
  }

  .ticket {
    box-shadow: none;
    border-radius: 0;
    padding: 0;
    border: none;
  }

  .no-print {
    display: none !important;
  }

  .ticket-container,
  .items-table,
  .totals-section {
    color: #000; /* Asegura contraste en térmico */
  }
}

/* Ajustes finos para impresoras de 58mm */
@media print and (max-width: 58mm) {
  .ticket-container {
    max-width: 58mm;
  }

  .business-name { font-size: 18px; }
  .items-table { font-size: 10px; }
  .item-qty { font-size: 9px; }
  .total-final { font-size: 15px; }
}
</style>