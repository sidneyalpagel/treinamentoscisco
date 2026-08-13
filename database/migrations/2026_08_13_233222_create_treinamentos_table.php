<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa a migration.
     */
    public function up(): void
    {
        Schema::create('treinamentos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('descricao')->nullable();
            $table->string('publico_alvo')->nullable();
            $table->string('instrutor')->nullable();
            $table->unsignedSmallInteger('carga_horaria')->nullable()->comment('Carga horária em horas');
            $table->string('modalidade')->default('presencial'); // presencial, online, hibrido
            $table->string('local')->nullable();
            $table->unsignedInteger('vagas')->nullable();
            $table->dateTime('data_inicio');
            $table->dateTime('data_fim')->nullable();
            $table->date('inscricoes_ate')->nullable();
            $table->string('status')->default('rascunho'); // rascunho, publicado, encerrado
            $table->boolean('permite_inscricao')->default(true);
            $table->boolean('gera_certificado')->default(true);
            $table->timestamps();

            $table->index('status');
            $table->index('data_inicio');
        });
    }

    /**
     * Reverte a migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('treinamentos');
    }
};
