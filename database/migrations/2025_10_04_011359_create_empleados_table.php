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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('email')->unique()->nullable();
            $table->string('telefono', 20)->nullable();
            $table->text('direccion')->nullable(); // text en lugar de string

            // Mejor como tabla separada si tienes muchos puestos
            $table->string('puesto', 50);

            $table->time('hora_entrada')->nullable();
            $table->time('hora_salida')->nullable();

            // JSON está bien para días laborales
            $table->json('dias_laborales')->nullable()
                ->comment('Ej: ["lunes","martes","miercoles"]');

            $table->decimal('salario', 10, 2)->nullable();
            $table->decimal('comision_porcentaje', 5, 2)->default(0)
                ->comment('% de comisión por servicio');

            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_salida')->nullable(); // Para histórico

            $table->enum('estado', ['activo', 'inactivo', 'vacaciones'])->default('activo');

            // Útil para gestión
            $table->string('foto')->nullable();
            $table->text('especialidades')->nullable();

            $table->timestamps();
            $table->softDeletes(); // Recomendado para no perder histórico

            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
