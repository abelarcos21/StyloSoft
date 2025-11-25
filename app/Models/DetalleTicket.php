<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DetalleTicket extends Model
{
    use HasFactory;

    protected $table = 'detalle_tickets';

    protected $fillable = [
        'ticket_id',
        'vendible_type',
        'vendible_id',
        'cantidad',
        'precio_unitario',
        'descuento',
        'subtotal',
        'empleado_id',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'descuento' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relaciones
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function vendible(): MorphTo
    {
        return $this->morphTo();
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    // Accessors
    public function getTipoVendibleAttribute(): string
    {
        return class_basename($this->vendible_type);
    }

    public function getEsServicioAttribute(): bool
    {
        return $this->vendible_type === Servicio::class;
    }

    public function getEsProductoAttribute(): bool
    {
        return $this->vendible_type === Producto::class;
    }

    // Métodos de negocio
    public function calcularSubtotal(): void
    {
        $this->subtotal = ($this->cantidad * $this->precio_unitario) - $this->descuento;
        $this->save();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($detalle) {
            if ($detalle->subtotal === null) {
                $detalle->subtotal = ($detalle->cantidad * $detalle->precio_unitario) - ($detalle->descuento ?? 0);
            }
        });

        static::created(function ($detalle) {
            // Reducir stock si es producto
            if ($detalle->es_producto) {
                $detalle->vendible->reducirStock($detalle->cantidad);
            }
        });
    }
}
