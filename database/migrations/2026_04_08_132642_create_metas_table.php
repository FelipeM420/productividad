<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->tinyInteger('mes')->comment('1=Enero … 12=Diciembre');
            $table->year('año');
            $table->decimal('ventas_meta', 12, 2)->default(0);
            $table->integer('clientes_atendidos_meta')->default(0);
            $table->integer('clientes_visitados_meta')->default(0);
            $table->integer('nuevos_clientes_meta')->default(0);
            $table->timestamps();

            // Un vendedor solo puede tener UNA meta por mes/año
            $table->unique(['id_usuario', 'mes', 'año']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metas');
    }
};