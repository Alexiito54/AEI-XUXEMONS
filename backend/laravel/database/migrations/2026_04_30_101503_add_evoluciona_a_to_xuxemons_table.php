<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('xuxemons', function (Blueprint $table) {
            $table->unsignedBigInteger('evoluciona_a')->nullable()->after('imagen');
            $table->foreign('evoluciona_a')->references('id')->on('xuxemons')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('xuxemons', function (Blueprint $table) {
            $table->dropForeign(['evoluciona_a']);
            $table->dropColumn('evoluciona_a');
        });
    }
};