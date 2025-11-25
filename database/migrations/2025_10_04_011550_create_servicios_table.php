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
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->integer('duracion_minutos'); // duración estimada del servicio

            // Adicionales útiles
            $table->string('categoria', 50)->nullable(); // corte, color, tratamiento, etc.
            $table->integer('orden')->default(0)->comment('Para ordenar en la UI');
            $table->boolean('requiere_deposito')->default(false);
            $table->decimal('deposito_minimo', 10, 2)->nullable();

            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['activo', 'categoria']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
