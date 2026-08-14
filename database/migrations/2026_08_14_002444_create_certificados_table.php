<?php

use App\Models\Inscricao;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Inscricao::class)->unique()->constrained('inscricoes')->cascadeOnDelete();
            $table->string('codigo', 20)->unique();
            $table->dateTime('emitido_em');
            $table->unsignedSmallInteger('carga_horaria')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificados');
    }
};
