<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inscricao extends Model
{
    use HasFactory;

    protected $table = 'inscricoes';

    public const STATUS_CONFIRMADA = 'confirmada';
    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_CANCELADA = 'cancelada';

    protected $fillable = [
        'treinamento_id',
        'nome',
        'email',
        'cpf',
        'telefone',
        'orgao',
        'cargo',
        'status',
        'observacoes',
    ];

    public function treinamento(): BelongsTo
    {
        return $this->belongsTo(Treinamento::class);
    }

    /* Escopos */
    public function scopeConfirmadas($query)
    {
        return $query->where('status', self::STATUS_CONFIRMADA);
    }

    public function scopeCanceladas($query)
    {
        return $query->where('status', self::STATUS_CANCELADA);
    }

    /* Apresentação */
    public static function statusDisponiveis(): array
    {
        return [
            self::STATUS_CONFIRMADA => 'Confirmada',
            self::STATUS_PENDENTE => 'Pendente',
            self::STATUS_CANCELADA => 'Cancelada',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusDisponiveis()[$this->status] ?? ucfirst($this->status);
    }

    public function estaCancelada(): bool
    {
        return $this->status === self::STATUS_CANCELADA;
    }
}
