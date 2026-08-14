<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('treinamentos', function (Blueprint $table) {
            $table->string('sala_codigo')->nullable()->unique();
            $table->timestamp('sala_criada_em')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('treinamentos', function (Blueprint $table) {
            $table->dropUnique(['sala_codigo']);
            $table->dropColumn(['sala_codigo', 'sala_criada_em']);
        });
    }
};
