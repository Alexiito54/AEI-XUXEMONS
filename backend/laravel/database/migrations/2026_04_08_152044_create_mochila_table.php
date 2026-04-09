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
        Schema::create('mochila', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('users', 'id')->onDelete('cascade');
            $table->unsignedBigInteger('id_item');
            $table->integer('cantidad')->default(1);
            $table->integer('slot')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mochila');
    }
};
