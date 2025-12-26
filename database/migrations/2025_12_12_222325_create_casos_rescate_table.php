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
        Schema::create('casos_rescate', function (Blueprint $table) {
            $table->id('id_rescate');
            $table->unsignedBigInteger('id_mascota');
            $table->text('situacion');
            $table->text('historia');
            $table->text('tratamiento')->nullable();
            $table->enum('urgencia', ['baja', 'media', 'alta'])->default('media');
            $table->date('fecha_rescate')->nullable();
            
            $table->foreign('id_mascota')->references('id_mascota')->on('mascotas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('casos_rescate');
    }
};
