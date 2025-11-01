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

            // Relación con tickets
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');

            // Polimórfica: producto o servicio
            $table->morphs('vendible'); // genera vendible_id y vendible_type

            $table->integer('cantidad')->default(1);
            $table->decimal('precio_unitario', 10, 2); // precio por unidad
            $table->decimal('subtotal', 10, 2); // cantidad * precio_unitario
            $table->timestamps();

            // Opcional: índice para acelerar consultas por ticket
            $table->index('ticket_id');
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
