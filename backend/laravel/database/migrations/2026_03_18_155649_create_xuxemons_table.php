<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('xuxemons', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->enum('tipo', ['fuego', 'agua', 'tierra', 'aire', 'electrico', 'normal']);
            $table->enum('evolucion', ['base', 'media', 'final']);
            $table->integer('vida');
            $table->integer('ataque');
            $table->integer('defensa');
            $table->string('imagen')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('xuxemons');
    }
};
