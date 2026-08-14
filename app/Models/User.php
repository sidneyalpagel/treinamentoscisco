<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password', 'role', 'area_id', 'ativo'])]
#[Hidden(['password', 'remember_token', 'convite_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_GESTOR = 'gestor';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'convite_enviado_em' => 'datetime',
            'password' => 'hashed',
            'ativo' => 'boolean',
        ];
    }

    /* Convite / ativação de cadastro */

    /** O usuário foi convidado mas ainda não confirmou o cadastro (definiu senha). */
    public function pendenteAtivacao(): bool
    {
        return ! is_null($this->convite_token);
    }

    /** Gera um novo token de convite (não persiste — chame save()). */
    public function gerarConvite(): string
    {
        $this->convite_token = Str::random(64);
        $this->convite_enviado_em = now();

        return $this->convite_token;
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function treinamentos(): HasMany
    {
        return $this->hasMany(Treinamento::class);
    }

    /* Papéis */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isGestor(): bool
    {
        return $this->role === self::ROLE_GESTOR;
    }

    public static function rolesDisponiveis(): array
    {
        return [
            self::ROLE_ADMIN => 'Administrador Geral',
            self::ROLE_GESTOR => 'Gestor de Treinamentos',
        ];
    }

    public function getRoleLabelAttribute(): string
    {
        return self::rolesDisponiveis()[$this->role] ?? ucfirst($this->role);
    }
}
