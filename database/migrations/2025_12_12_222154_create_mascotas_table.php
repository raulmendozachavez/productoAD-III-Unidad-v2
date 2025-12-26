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
        Schema::create('mascotas', function (Blueprint $table) {
            $table->id('id_mascota');
            $table->string('nombre', 50);
            $table->enum('tipo', ['perros', 'gatos', 'otros']);
            $table->string('raza', 50)->nullable();
            $table->string('edad', 20)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('imagen', 255)->nullable();
            $table->enum('estado', ['disponible', 'adoptado', 'en_proceso'])->default('disponible');
            $table->date('fecha_ingreso')->nullable();
            $table->boolean('es_rescate')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mascotas');
    }
};
