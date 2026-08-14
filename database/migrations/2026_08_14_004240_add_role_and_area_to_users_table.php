<?php

use App\Models\Area;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('gestor')->after('email'); // admin, gestor
            $table->foreignIdFor(Area::class)->nullable()->after('role')->constrained()->nullOnDelete();
            $table->boolean('ativo')->default(true)->after('area_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(Area::class);
            $table->dropColumn(['role', 'ativo']);
        });
    }
};
