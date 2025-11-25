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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('email')->unique()->nullable();
            $table->string('telefono', 20)->nullable()->index(); // Índice para búsquedas
            $table->text('direccion')->nullable();

            // Campos adicionales muy útiles
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('genero', ['masculino', 'femenino', 'otro', 'prefiero_no_decir'])->nullable();

            // Marketing y fidelización
            $table->boolean('acepta_marketing')->default(false);
            $table->integer('puntos_fidelidad')->default(0);
            $table->date('ultima_visita')->nullable();

            // Notas importantes (alergias, preferencias, etc.)
            $table->text('notas')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['nombre', 'apellido']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
