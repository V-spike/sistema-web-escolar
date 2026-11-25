<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materias', function (Blueprint $table) {
            $table->string('clave_corta', 10)->nullable()->after('clave');
            $table->unsignedTinyInteger('semestre')->default(1)->after('nombre');
            $table->enum('tipo', ['normal', 'bloque_comun', 'tutoria'])->default('normal')->after('horas_semana');
        });
    }

    public function down(): void
    {
        Schema::table('materias', function (Blueprint $table) {
            $table->dropColumn(['clave_corta', 'semestre', 'tipo']);
        });
    }
};