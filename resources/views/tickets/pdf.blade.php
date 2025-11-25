<!DOCTYPE html>
<html>
<head>
  <style>
    body { font-family: Arial; font-size:12px; }
    .title { text-align:center; font-size:16px; font-weight:bold; }
    .section { margin-top:10px; }
    table { width:100%; border-collapse: collapse; }
    th, td { padding:4px; border-bottom:1px solid #eee; }
  </style>
</head>
<body>

<div class="title">StyloSoft - Ticket de Servicio</div>

<div class="section">
  <strong>Cliente:</strong> {{ $ticket->cliente->nombre }} <br>
  <strong>Empleado:</strong> {{ $ticket->empleado->nombre }} <br>
  <strong>Fecha:</strong> {{ $ticket->fecha }}
</div>

<div class="section">
<table>
  <thead>
    <tr><th>Servicio</th><th>Precio</th></tr>
  </thead>
  <tbody>
    @foreach ($ticket->detalles as $d)
    <tr>
      <td>{{ $d->vendible->nombre }}</td>
      <td>${{ number_format($d->subtotal,2) }}</td>
    </tr>
    @endforeach
  </tbody>
</table>
</div>

<div class="section" style="text-align:right;">
  <strong>Total:</strong> ${{ number_format($ticket->total,2) }}
</div>

</body>
</html>
