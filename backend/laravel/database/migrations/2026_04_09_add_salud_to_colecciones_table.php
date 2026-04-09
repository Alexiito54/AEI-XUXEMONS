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
        Schema::table('colecciones', function (Blueprint $table) {
            $table->enum('tamaño_actual', ['Pequeño', 'Mediano', 'Grande'])->default('Pequeño');
            $table->integer('nivel')->default(0);
            $table->integer('alimentaciones_pendientes')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colecciones', function (Blueprint $table) {
            $table->dropColumn(['tamaño_actual', 'nivel', 'alimentaciones_pendientes']);
        });
    }
};
