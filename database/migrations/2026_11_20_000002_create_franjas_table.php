<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * HU-12: la jornada. Los recesos también son filas (es_receso = true)
     * para poder dibujar la cuadrícula completa en orden.
     */
    public function up(): void
    {
        Schema::create('franjas', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('orden')->unique();
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->boolean('es_receso')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('franjas');
    }
};
