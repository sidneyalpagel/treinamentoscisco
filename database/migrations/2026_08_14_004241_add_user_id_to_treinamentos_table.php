<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('treinamentos', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->nullable()->after('id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('treinamentos', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(User::class);
        });
    }
};
