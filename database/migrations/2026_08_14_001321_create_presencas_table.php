<?php

use App\Models\Inscricao;
use App\Models\Sessao;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presencas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Sessao::class)->constrained('sessoes')->cascadeOnDelete();
            $table->foreignIdFor(Inscricao::class)->constrained('inscricoes')->cascadeOnDelete();
            $table->dateTime('registrado_em');
            $table->string('origem')->default('admin'); // admin, auto
            $table->timestamps();

            // Uma presença por participante por sessão
            $table->unique(['sessao_id', 'inscricao_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presencas');
    }
};
