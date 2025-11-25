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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('codigo', 50)->unique();
            $table->string('marca', 100)->nullable();
            $table->text('descripcion')->nullable();

            $table->decimal('precio_venta', 10, 2);
            $table->decimal('precio_compra', 10, 2)->nullable();
            $table->decimal('precio_mayoreo', 10, 2)->nullable(); // Para ventas bulk

            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(5)->comment('Alerta de reorden');

            $table->string('categoria', 50)->nullable();
            $table->string('unidad_medida', 20)->default('pieza'); // pieza, ml, kg

            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['activo', 'categoria']);
            $table->index('stock'); // Para reportes de inventario
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
