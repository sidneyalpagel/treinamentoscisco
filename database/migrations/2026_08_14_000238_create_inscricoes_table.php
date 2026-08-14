<?php

use App\Models\Treinamento;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscricoes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Treinamento::class)->constrained()->cascadeOnDelete();
            $table->string('nome');
            $table->string('email');
            $table->string('cpf', 14)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('orgao')->nullable();
            $table->string('cargo')->nullable();
            $table->string('status')->default('confirmada'); // confirmada, pendente, cancelada
            $table->text('observacoes')->nullable();
            $table->timestamps();

            // Evita inscrição duplicada do mesmo e-mail no mesmo treinamento
            $table->unique(['treinamento_id', 'email']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscricoes');
    }
};
