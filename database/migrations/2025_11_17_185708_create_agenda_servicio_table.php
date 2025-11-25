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
        Schema::create('agenda_servicio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_id')->constrained()->cascadeOnDelete();
            $table->foreignId('servicio_id')->constrained()->cascadeOnDelete();

            // IMPORTANTE: Guardar precio/duración_minutos en el pivot para historico
            $table->decimal('precio', 10, 2);
            $table->integer('duracion_minutos');
            $table->integer('orden')->default(0)->comment('Orden de ejecución');

            $table->timestamps();

            $table->unique(['agenda_id', 'servicio_id', 'orden']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_servicio');
    }
};
