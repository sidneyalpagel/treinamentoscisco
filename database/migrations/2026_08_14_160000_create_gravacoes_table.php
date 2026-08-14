<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gravacoes', function (Blueprint $table) {
            $table->id();
            $table->morphs('gravavel'); // pertence a um Treinamento ou a uma Reuniao
            $table->string('arquivo'); // caminho relativo no servidor Jibri (ex.: <sessao>/<arquivo>.mp4)
            $table->unsignedBigInteger('tamanho')->nullable();
            $table->unsignedInteger('duracao_seg')->nullable();
            $table->timestamp('gravado_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gravacoes');
    }
};
