<?php

namespace Database\Factories;

use App\Models\Treinamento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Treinamento>
 */
class TreinamentoFactory extends Factory
{
    protected $model = Treinamento::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'titulo' => fake()->sentence(3),
            'modalidade' => Treinamento::MODALIDADE_ONLINE,
            'status' => Treinamento::STATUS_PUBLICADO,
            'data_inicio' => now()->addDays(3),
            'permite_inscricao' => true,
            'gera_certificado' => false,
        ];
    }

    /** Treinamento com sala de videoconferência já criada. */
    public function comSala(): static
    {
        return $this->state(fn () => [
            'sala_codigo' => 'sala-'.Str::lower(Str::random(8)),
            'sala_criada_em' => now(),
        ]);
    }

    public function presencial(): static
    {
        return $this->state(fn () => ['modalidade' => Treinamento::MODALIDADE_PRESENCIAL]);
    }
}
