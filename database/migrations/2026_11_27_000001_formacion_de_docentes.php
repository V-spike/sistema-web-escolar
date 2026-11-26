<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * La escuela registra la formación del docente en 4 columnas (como en "Docentes Horas Perfil.xlsx"):
 * licenciatura, especialidad, maestría y doctorado. Reemplazan al campo único "titulo".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('docentes', function (Blueprint $table) {
            $table->string('licenciatura', 150)->nullable()->after('cedula');
            $table->string('especialidad', 150)->nullable()->after('licenciatura');
            $table->string('maestria', 150)->nullable()->after('especialidad');
            $table->string('doctorado', 150)->nullable()->after('maestria');
        });

        // Lo que ya estaba en "titulo" pasa a licenciatura para no perderlo
        DB::table('docentes')->whereNotNull('titulo')->update(['licenciatura' => DB::raw('titulo')]);

        Schema::table('docentes', function (Blueprint $table) {
            $table->dropColumn('titulo');
        });
    }

    public function down(): void
    {
        Schema::table('docentes', function (Blueprint $table) {
            $table->string('titulo', 100)->nullable();
        });

        DB::table('docentes')->whereNotNull('licenciatura')->update(['titulo' => DB::raw('licenciatura')]);

        Schema::table('docentes', function (Blueprint $table) {
            $table->dropColumn(['licenciatura', 'especialidad', 'maestria', 'doctorado']);
        });
    }
};
