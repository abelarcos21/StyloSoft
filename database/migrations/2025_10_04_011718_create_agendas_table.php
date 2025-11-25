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
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->foreignId('empleado_id')->constrained()->onDelete('cascade');

            // Solo estas dos fechas son necesarias
            $table->dateTime('fecha_hora_inicio')->index();
            $table->dateTime('fecha_hora_fin');

            // Enum más descriptivo
            $table->enum('tipo', ['express', 'estandar'])->default('estandar')
                ->comment('express: servicio rápido sin cita previa, estandar: cita programada');

            $table->enum('estado', [
                'pendiente',
                'confirmada',
                'en_proceso',
                'completada',
                'cancelada',
                'no_asistio'
            ])->default('pendiente');

            // Campos adicionales útiles
            $table->text('notas')->nullable()->comment('Notas especiales del cliente');
            $table->string('cancelado_por')->nullable()->comment('cliente, empleado, sistema');
            $table->text('razon_cancelacion')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Índices para mejorar performance
            $table->index(['empleado_id', 'fecha_hora_inicio']);
            $table->index(['cliente_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
