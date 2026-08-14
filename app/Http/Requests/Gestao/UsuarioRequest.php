<?php

namespace App\Http\Requests\Gestao;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $dados = ['ativo' => $this->boolean('ativo')];

        // Administrador Geral não fica vinculado a uma área
        if ($this->input('role') === User::ROLE_ADMIN) {
            $dados['area_id'] = null;
        }

        $this->merge($dados);
    }

    public function rules(): array
    {
        $usuarioId = $this->route('usuario')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuarioId)],
            'role' => ['required', Rule::in(array_keys(User::rolesDisponiveis()))],
            'area_id' => [
                Rule::requiredIf(fn () => $this->input('role') === User::ROLE_GESTOR),
                'nullable',
                'exists:areas,id',
            ],
            'ativo' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'email' => 'e-mail',
            'role' => 'papel',
            'area_id' => 'área',
            'password' => 'senha',
        ];
    }
}
