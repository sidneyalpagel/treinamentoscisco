<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificado extends Model
{
    use HasFactory;

    protected $table = 'certificados';

    protected $fillable = [
        'inscricao_id',
        'codigo',
        'emitido_em',
        'carga_horaria',
    ];

    protected function casts(): array
    {
        return [
            'emitido_em' => 'datetime',
            'carga_horaria' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Certificado $certificado) {
            if (blank($certificado->codigo)) {
                $certificado->codigo = static::gerarCodigoUnico();
            }
        });
    }

    public static function gerarCodigoUnico(): string
    {
        $alfabeto = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $blocos = [];
            for ($b = 0; $b < 3; $b++) {
                $bloco = '';
                for ($i = 0; $i < 4; $i++) {
                    $bloco .= $alfabeto[random_int(0, strlen($alfabeto) - 1)];
                }
                $blocos[] = $bloco;
            }
            $codigo = implode('-', $blocos); // ex.: ABCD-2345-EFGH
        } while (static::where('codigo', $codigo)->exists());

        return $codigo;
    }

    public function inscricao(): BelongsTo
    {
        return $this->belongsTo(Inscricao::class);
    }

    public function getRouteKeyName(): string
    {
        return 'codigo';
    }
}
