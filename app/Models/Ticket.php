<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_ticket',
        'cliente_id',
        'empleado_id',
        'agenda_id',
        'subtotal',
        'descuento',
        'impuesto',
        'total',
        'metodo_pago',
        'estado_pago',
        'fecha',
        'notas',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'total' => 'decimal:2',
        'fecha' => 'datetime',
    ];

    // Relaciones
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(Agenda::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleTicket::class);
    }

    // Accessors
    public function getTotalConLetraAttribute(): string
    {
        // Aquí podrías usar una librería para convertir números a letras
        return "$ {$this->total} MXN";
    }

    public function getEsPagadoAttribute(): bool
    {
        return $this->estado_pago === 'pagado';
    }

    // Scopes
    public function scopePagados($query)
    {
        return $query->where('estado_pago', 'pagado');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado_pago', 'pendiente');
    }

    public function scopeDelDia($query, $fecha = null)
    {
        $fecha = $fecha ?? now();
        return $query->whereDate('fecha', $fecha);
    }

    public function scopeDelMes($query, int $mes = null, int $año = null)
    {
        $mes = $mes ?? now()->month;
        $año = $año ?? now()->year;
        return $query->whereMonth('fecha', $mes)
                     ->whereYear('fecha', $año);
    }

    public function scopeDelEmpleado($query, int $empleadoId)
    {
        return $query->where('empleado_id', $empleadoId);
    }

    // Métodos de negocio
    public function calcularTotales(): void
    {
        $this->subtotal = $this->detalles->sum('subtotal');
        $this->total = $this->subtotal - $this->descuento + $this->impuesto;
        $this->save();
    }

    public function aplicarDescuento(float $monto): void
    {
        $this->descuento = $monto;
        $this->calcularTotales();
    }

    public function calcularImpuesto(float $porcentaje = 16): void
    {
        $base = $this->subtotal - $this->descuento;
        $this->impuesto = $base * ($porcentaje / 100);
        $this->calcularTotales();
    }

    public static function generarNumeroTicket(): string
    {
        $año = now()->year;
        $ultimo = static::whereYear('created_at', $año)->count() + 1;
        return sprintf('VT-%d-%05d', $año, $ultimo);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->numero_ticket)) {
                $ticket->numero_ticket = static::generarNumeroTicket();
            }
        });
    }
}
