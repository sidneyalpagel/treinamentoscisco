<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('convite_token', 64)->nullable()->unique();
            $table->timestamp('convite_enviado_em')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['convite_token']);
            $table->dropColumn(['convite_token', 'convite_enviado_em']);
        });
    }
};
