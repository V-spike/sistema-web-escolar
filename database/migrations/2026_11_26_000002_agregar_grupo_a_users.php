<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Un alumno pertenece a un grupo: así puede ver "Mi horario".
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('matricula', 20)->nullable()->unique()->after('email');
            $table->foreignId('grupo_id')->nullable()->after('rol')->constrained('grupos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('grupo_id');
            $table->dropUnique(['matricula']);
            $table->dropColumn('matricula');
        });
    }
};
