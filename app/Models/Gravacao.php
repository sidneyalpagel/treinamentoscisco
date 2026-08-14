<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gravacao extends Model
{
    protected $table = 'gravacoes';

    protected $fillable = ['treinamento_id', 'arquivo', 'tamanho', 'duracao_seg', 'gravado_em'];

    protected function casts(): array
    {
        return [
            'gravado_em' => 'datetime',
            'tamanho' => 'integer',
            'duracao_seg' => 'integer',
        ];
    }

    public function treinamento(): BelongsTo
    {
        return $this->belongsTo(Treinamento::class);
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
