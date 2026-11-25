<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('docentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->string('numero_personal', 20)->unique();
            $table->string('nombre', 60);
            $table->string('apellido_paterno', 60);
            $table->string('apellido_materno', 60)->nullable();
            $table->string('curp', 18)->unique()->nullable();
            $table->string('rfc', 13)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('correo', 100)->nullable();
            $table->string('categoria', 50)->nullable();
            $table->string('sindicato', 30)->nullable();
            $table->string('titulo', 100)->nullable();
            $table->string('cedula', 20)->nullable();
            $table->unsignedTinyInteger('horas_plaza');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('docentes');
    }
};