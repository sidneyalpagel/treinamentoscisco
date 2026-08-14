<?php

namespace Database\Factories;

use App\Models\Reuniao;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Reuniao>
 */
class ReuniaoFactory extends Factory
{
    protected $model = Reuniao::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'titulo' => fake()->sentence(3),
            'data_inicio' => now()->addDay(),
            'sala_codigo' => 'reuniao-'.Str::lower(Str::random(8)),
        ];
    }
}
