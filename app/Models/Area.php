<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    use HasFactory;

    protected $table = 'areas';

    protected $fillable = [
        'nome',
        'sigla',
        'descricao',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    public function getNomeCompletoAttribute(): string
    {
        return $this->sigla ? "{$this->nome} ({$this->sigla})" : $this->nome;
    }
}
