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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('numero_ticket', 20)->unique(); // VT-2025-00001

            $table->foreignId('cliente_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('empleado_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('agenda_id')->nullable()->constrained()->onDelete('set null')
                ->comment('Si viene de una cita agendada');

            // Totales
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('impuesto', 10, 2)->default(0); // IVA u otros
            $table->decimal('total', 10, 2);

            // Pagos
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia', 'mixto'])->nullable();
            $table->enum('estado_pago', ['pendiente', 'pagado', 'parcial'])->default('pagado');

            $table->dateTime('fecha');
            $table->text('notas')->nullable();

            $table->timestamps();

            $table->index(['fecha', 'empleado_id']);
            $table->index('numero_ticket');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
