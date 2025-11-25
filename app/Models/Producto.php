<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'codigo',
        'marca',
        'descripcion',
        'precio_venta',
        'precio_compra',
        'precio_mayoreo',
        'stock',
        'stock_minimo',
        'categoria',
        'unidad_medida',
        'activo',
    ];

    protected $casts = [
        'precio_venta' => 'decimal:2',
        'precio_compra' => 'decimal:2',
        'precio_mayoreo' => 'decimal:2',
        'stock' => 'integer',
        'stock_minimo' => 'integer',
        'activo' => 'boolean',
    ];

    // Relación polimórfica
    public function detalleTickets(): MorphMany
    {
        return $this->morphMany(DetalleTicket::class, 'vendible');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeConStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeBajoStock($query)
    {
        return $query->whereColumn('stock', '<=', 'stock_minimo');
    }

    public function scopePorCategoria($query, string $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    // Accessors
    public function getMargenGananciaAttribute(): ?float
    {
        if (!$this->precio_compra) {
            return null;
        }

        return (($this->precio_venta - $this->precio_compra) / $this->precio_compra) * 100;
    }

    public function getRequiereReordenAttribute(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }

    // Métodos de negocio
    public function reducirStock(int $cantidad): bool
    {
        if ($this->stock >= $cantidad) {
            $this->decrement('stock', $cantidad);
            return true;
        }
        return false;
    }

    public function aumentarStock(int $cantidad): void
    {
        $this->increment('stock', $cantidad);
    }

    public function aplicarDescuento(float $porcentaje): float
    {
        return $this->precio_venta * (1 - $porcentaje / 100);
    }
}
