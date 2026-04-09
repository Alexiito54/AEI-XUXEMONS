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
        Schema::create('colecciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('users', 'id')->onDelete('cascade');
            $table->unsignedBigInteger('id_xuxemon');
            $table->timestamp('capturado_en')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colecciones');
    }
};
