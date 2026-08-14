<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Reuniao extends Model
{
    use HasFactory;

    protected $table = 'reunioes';

    protected $fillable = ['user_id', 'titulo', 'descricao', 'data_inicio', 'data_fim', 'sala_codigo'];

    protected function casts(): array
    {
        return [
            'data_inicio' => 'datetime',
            'data_fim' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gravacoes(): MorphMany
    {
        return $this->morphMany(Gravacao::class, 'gravavel')->latest('gravado_em');
    }

    public function scopeDoUsuario($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /** Gera um código de sala único a partir do título. */
    public static function gerarSalaCodigo(string $titulo): string
    {
        $base = Str::slug(Str::limit($titulo, 30, '')) ?: 'reuniao';

        do {
            $codigo = $base.'-'.Str::lower(Str::random(6));
        } while (static::where('sala_codigo', $codigo)->exists());

        return $codigo;
    }
}
