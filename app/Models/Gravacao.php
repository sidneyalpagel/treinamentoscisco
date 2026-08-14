<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Gravacao extends Model
{
    protected $table = 'gravacoes';

    protected $fillable = ['arquivo', 'tamanho', 'duracao_seg', 'gravado_em'];

    protected function casts(): array
    {
        return [
            'gravado_em' => 'datetime',
            'tamanho' => 'integer',
            'duracao_seg' => 'integer',
        ];
    }

    /** Treinamento ou Reuniao à qual a gravação pertence. */
    public function gravavel(): MorphTo
    {
        return $this->morphTo();
    }

    /** Título da origem (treinamento/reunião), para nome de arquivo etc. */
    public function tituloOrigem(): string
    {
        return $this->gravavel?->titulo ?? 'gravacao';
    }

    /** Nome sugerido para o download. */
    public function nomeDownload(): string
    {
        return 'gravacao-'.(Str::slug($this->tituloOrigem()) ?: 'reuniao').'.mp4';
    }

    /** Tamanho legível (ex.: "42,3 MB"). */
    public function getTamanhoLegivelAttribute(): ?string
    {
        if (is_null($this->tamanho)) {
            return null;
        }

        $mb = $this->tamanho / 1048576;

        return $mb >= 1024
            ? number_format($mb / 1024, 1, ',', '.').' GB'
            : number_format($mb, 1, ',', '.').' MB';
    }
}
