<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presenca extends Model
{
    use HasFactory;

    protected $table = 'presencas';

    public const ORIGEM_ADMIN = 'admin';
    public const ORIGEM_AUTO = 'auto';

    protected $fillable = [
        'sessao_id',
        'inscricao_id',
        'registrado_em',
        'origem',
    ];

    protected function casts(): array
    {
        return [
            'registrado_em' => 'datetime',
        ];
    }

    public function sessao(): BelongsTo
    {
        return $this->belongsTo(Sessao::class);
    }

    public function inscricao(): BelongsTo
    {
        return $this->belongsTo(Inscricao::class);
    }
}
