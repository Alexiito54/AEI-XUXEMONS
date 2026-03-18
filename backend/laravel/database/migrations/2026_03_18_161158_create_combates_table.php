<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('combates', function (Blueprint $table) {
            $table->id();
            $table->string('retador_id');    // quien inicia el combate
            $table->string('rival_id');      // quien recibe el reto
            $table->foreignId('xuxemon_retador_id')->constrained('xuxemons')->onDelete('cascade');
            $table->foreignId('xuxemon_rival_id')->constrained('xuxemons')->onDelete('cascade');
            $table->foreign('retador_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('rival_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'en_curso', 'finalizado'])->default('pendiente');
            $table->string('ganador_id')->nullable(); // user_id del ganador
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combates');
    }
};
