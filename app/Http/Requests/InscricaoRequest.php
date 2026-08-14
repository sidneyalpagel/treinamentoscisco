<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InscricaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $treinamentoId = $this->route('treinamento')?->id;

        return [
            'nome' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('inscricoes', 'email')->where('treinamento_id', $treinamentoId),
            ],
            'cpf' => ['nullable', 'string', 'max:14'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'orgao' => ['nullable', 'string', 'max:255'],
            'cargo' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome',
            'email' => 'e-mail',
            'orgao' => 'órgão / setor',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Já existe uma inscrição com este e-mail para este treinamento.',
        ];
    }
}
