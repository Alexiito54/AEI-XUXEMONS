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
        Schema::create('xuxemon_enfermedad', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coleccion_id');
            $table->unsignedBigInteger('enfermedad_id');
            $table->timestamp('fecha_contagio')->nullable();
            $table->timestamps();

            $table->foreign('coleccion_id')->references('id')->on('colecciones')->onDelete('cascade');
            $table->foreign('enfermedad_id')->references('id')->on('enfermedades')->onDelete('cascade');
            $table->unique(['coleccion_id', 'enfermedad_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xuxemon_enfermedad');
    }
};
