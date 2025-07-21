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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->foreignId('cliente_id')->constrained('users');
            $table->foreignId('servicio_id')->constrained('services');
            $table->foreignId('empleado_id')->nullable()->constrained('users');
            $table->dateTime('fecha_evento');
            $table->string('ubicacion');
            $table->integer('numero_invitados')->default(50);
            $table->decimal('presupuesto', 10, 2)->nullable();
            $table->text('requisitos_especiales')->nullable();
            $table->enum('estado', ['pendiente', 'en_progreso', 'completado', 'cancelado'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
