<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Treinamento extends Model
{
    use HasFactory;

    /** Status possíveis */
    public const STATUS_RASCUNHO = 'rascunho';
    public const STATUS_PUBLICADO = 'publicado';
    public const STATUS_ENCERRADO = 'encerrado';

    /** Modalidades possíveis */
    public const MODALIDADE_PRESENCIAL = 'presencial';
    public const MODALIDADE_ONLINE = 'online';
    public const MODALIDADE_HIBRIDO = 'hibrido';

    protected $fillable = [
        'titulo',
        'slug',
        'descricao',
        'publico_alvo',
        'instrutor',
        'carga_horaria',
        'modalidade',
        'local',
        'vagas',
        'data_inicio',
        'data_fim',
        'inscricoes_ate',
        'status',
        'permite_inscricao',
        'gera_certificado',
    ];

    protected function casts(): array
    {
        return [
            'data_inicio' => 'datetime',
            'data_fim' => 'datetime',
            'inscricoes_ate' => 'date',
            'permite_inscricao' => 'boolean',
            'gera_certificado' => 'boolean',
            'carga_horaria' => 'integer',
            'vagas' => 'integer',
        ];
    }

    /**
     * Gera o slug automaticamente a partir do título quando ausente.
     */
    protected static function booted(): void
    {
        static::saving(function (Treinamento $treinamento) {
            if (blank($treinamento->slug)) {
                $treinamento->slug = static::gerarSlugUnico($treinamento->titulo, $treinamento->id);
            }
        });
    }

    public static function gerarSlugUnico(string $titulo, ?int $ignorarId = null): string
    {
        $base = Str::slug($titulo) ?: 'treinamento';
        $slug = $base;
        $i = 1;

        while (static::where('slug', $slug)
            ->when($ignorarId, fn ($q) => $q->where('id', '!=', $ignorarId))
            ->exists()) {
            $slug = $base.'-'.(++$i);
        }

        return $slug;
    }

    /* --------------------------------------------------------------------- */
    /* Escopos                                                               */
    /* --------------------------------------------------------------------- */

    public function scopePublicados($query)
    {
        return $query->where('status', self::STATUS_PUBLICADO);
    }

    public function scopeProximos($query)
    {
        return $query->where('data_inicio', '>=', now()->startOfDay())
            ->orderBy('data_inicio');
    }

    /* --------------------------------------------------------------------- */
    /* Helpers de apresentação                                               */
    /* --------------------------------------------------------------------- */

    public static function statusDisponiveis(): array
    {
        return [
            self::STATUS_RASCUNHO => 'Rascunho',
            self::STATUS_PUBLICADO => 'Publicado',
            self::STATUS_ENCERRADO => 'Encerrado',
        ];
    }

    public static function modalidadesDisponiveis(): array
    {
        return [
            self::MODALIDADE_PRESENCIAL => 'Presencial',
            self::MODALIDADE_ONLINE => 'Online',
            self::MODALIDADE_HIBRIDO => 'Híbrido',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusDisponiveis()[$this->status] ?? ucfirst($this->status);
    }

    public function getModalidadeLabelAttribute(): string
    {
        return self::modalidadesDisponiveis()[$this->modalidade] ?? ucfirst($this->modalidade);
    }

    public function estaPublicado(): bool
    {
        return $this->status === self::STATUS_PUBLICADO;
    }

    /**
     * As inscrições estão abertas? (publicado, permite inscrição e dentro do prazo)
     */
    public function inscricoesAbertas(): bool
    {
        if (! $this->estaPublicado() || ! $this->permite_inscricao) {
            return false;
        }

        if ($this->inscricoes_ate && $this->inscricoes_ate->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Usa o slug na geração de URLs públicas.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
