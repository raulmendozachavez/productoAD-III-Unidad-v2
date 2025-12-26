<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditoria', function (Blueprint $table) {
            $table->id('id_auditoria');

            // AHORA ES NULO
            $table->unsignedBigInteger('id_usuario')->nullable();

            $table->string('nombre_usuario', 50);
            $table->string('accion', 100);
            $table->string('modulo', 50);
            $table->text('descripcion')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->timestamp('fecha_hora')->useCurrent();

            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('usuarios')
                  ->nullOnDelete();

            $table->index('id_usuario');
            $table->index('fecha_hora');
            $table->index('modulo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditoria');
    }
};