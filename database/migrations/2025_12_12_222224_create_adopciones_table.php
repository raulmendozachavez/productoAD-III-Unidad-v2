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
        Schema::create('adopciones', function (Blueprint $table) {
            $table->id('id_adopcion');
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_mascota');
            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada', 'completada'])->default('pendiente');
            $table->text('notas')->nullable();
            
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('id_mascota')->references('id_mascota')->on('mascotas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adopciones');
    }
};
