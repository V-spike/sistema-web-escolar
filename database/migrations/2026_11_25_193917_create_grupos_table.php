<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semestre_id')->constrained('semestres');
            $table->unsignedTinyInteger('grado');
            $table->char('letra', 1);
            $table->foreignId('aula_id')->nullable()->constrained('aulas')->nullOnDelete();
            $table->unsignedTinyInteger('capacidad')->default(50);
            $table->string('area', 60)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['semestre_id', 'grado', 'letra']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};