<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detalle_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade'); // Relación con tickets

            // Polimórfica: producto o servicio
            $table->morphs('vendible'); // vendible_id, vendible_type

            $table->integer('cantidad')->default(1);
            $table->decimal('precio_unitario', 10, 2); // precio por unidad
            $table->decimal('descuento', 10, 2)->default(0); // Descuento por item
            $table->decimal('subtotal', 10, 2); // cantidad * precio_unitario

            // Útil para servicios
            $table->foreignId('empleado_id')->nullable()->constrained()
                ->comment('Quién realizó este servicio específico');

            $table->timestamps();

            $table->index(['ticket_id', 'vendible_type']); // Opcional: índice para acelerar consultas por ticket
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_tickets');
    }
};
