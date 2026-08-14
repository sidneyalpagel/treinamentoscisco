<?php

use App\Models\Treinamento;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessoes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Treinamento::class)->constrained()->cascadeOnDelete();
            $table->string('titulo')->nullable();
            $table->date('data');
            $table->time('hora_inicio');
            $table->time('hora_fim')->nullable();
            $table->string('codigo', 12)->unique(); // token do link público de presença
            $table->boolean('presenca_aberta')->default(false);
            $table->timestamps();

            $table->index(['treinamento_id', 'data']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessoes');
    }
};
