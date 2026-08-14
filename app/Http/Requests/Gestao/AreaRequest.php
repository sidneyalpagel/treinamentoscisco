<?php

namespace App\Http\Requests\Gestao;

use Illuminate\Foundation\Http\FormRequest;

class AreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['ativo' => $this->boolean('ativo')]);
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'sigla' => ['nullable', 'string', 'max:20'],
            'descricao' => ['nullable', 'string'],
            'ativo' => ['boolean'],
        ];
    }
}
