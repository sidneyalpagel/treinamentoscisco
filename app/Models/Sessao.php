<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sessao extends Model
{
    use HasFactory;

    protected $table = 'sessoes';

    protected $fillable = [
        'treinamento_id',
        'titulo',
        'data',
        'hora_inicio',
        'hora_fim',
        'codigo',
        'presenca_aberta',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'date',
            'presenca_aberta' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Sessao $sessao) {
            if (blank($sessao->codigo)) {
                $sessao->codigo = static::gerarCodigoUnico();
            }
        });
    }

    public static function gerarCodigoUnico(): string
    {
        // Alfabeto sem caracteres ambíguos (0/O, 1/I)
        $alfabeto = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $codigo = '';
            for ($i = 0; $i < 8; $i++) {
                $codigo .= $alfabeto[random_int(0, strlen($alfabeto) - 1)];
            }
        } while (static::where('codigo', $codigo)->exists());

        return $codigo;
    }

    public function treinamento(): BelongsTo
    {
        return $this->belongsTo(Treinamento::class);
    }

    public function presencas(): HasMany
    {
        return $this->hasMany(Presenca::class);
    }

    /* Apresentação */
    public function getHorarioAttribute(): string
    {
        $inicio = substr((string) $this->hora_inicio, 0, 5);
        $fim = $this->hora_fim ? ' às '.substr((string) $this->hora_fim, 0, 5) : '';

        return $inicio.$fim;
    }

    public function getNomeExibicaoAttribute(): string
    {
        return $this->titulo ?: $this->data->translatedFormat('d/m/Y');
    }

    public function totalPresentes(): int
    {
        return $this->presencas()->count();
    }
}
