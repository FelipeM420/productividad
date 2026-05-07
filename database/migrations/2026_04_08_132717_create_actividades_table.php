<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->date('fecha');
            $table->decimal('ventas', 12, 2)->default(0);
            $table->integer('clientes_atendidos')->default(0);
            $table->integer('clientes_visitados')->default(0);
            $table->integer('nuevos_clientes')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};