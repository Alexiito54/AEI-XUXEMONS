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
        Schema::create('configuracion_admin', function (Blueprint $table) {
            $table->id();
            $table->integer('xuxes_pequeno_mediano')->default(3);
            $table->integer('xuxes_mediano_grande')->default(5);
            $table->integer('porcentaje_bajón')->default(5);
            $table->integer('porcentaje_sobredosis')->default(10);
            $table->integer('porcentaje_atracón')->default(15);
            $table->time('hora_xuxes_diarias')->default('08:00');
            $table->integer('cantidad_xuxes_diarias')->default(10);
            $table->time('hora_xuxemon_diario')->default('08:00');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_admin');
    }
};
