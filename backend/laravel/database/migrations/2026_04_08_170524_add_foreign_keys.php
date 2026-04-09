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
        Schema::table('mochila', function (Blueprint $table) {
            $table->foreign('id_item')->references('id')->on('items')->onDelete('cascade');
        });

        Schema::table('colecciones', function (Blueprint $table) {
            $table->foreign('id_xuxemon')->references('id')->on('xuxemons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mochila', function (Blueprint $table) {
            $table->dropForeignKey(['id_item']);
        });

        Schema::table('colecciones', function (Blueprint $table) {
            $table->dropForeignKey(['id_xuxemon']);
        });
    }
};
