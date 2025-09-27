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
        Schema::table('citas_medicas', function (Blueprint $table) {
            //
            $table->dropColumn('estado');
            $table->enum('estado', ["Programada","Pendiente","Completada","Cancelada","Rechazada"])->default('Programada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('citas_medicas', function (Blueprint $table) {
            //
            $table->enum('estado', ["Programada","Completada","Cancelada","Rechazada"])->default('Programada');
        });
    }
};
