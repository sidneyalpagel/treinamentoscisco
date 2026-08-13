<?php

namespace App\Http\Requests\Admin;

use App\Models\Treinamento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TreinamentoRequest extends FormRequest
{
    /**
     * A rota já está protegida pelo middleware de autenticação.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normaliza os checkboxes antes da validação.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'permite_inscricao' => $this->boolean('permite_inscricao'),
            'gera_certificado' => $this->boolean('gera_certificado'),
        ]);
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'publico_alvo' => ['nullable', 'string', 'max:255'],
            'instrutor' => ['nullable', 'string', 'max:255'],
            'carga_horaria' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'modalidade' => ['required', Rule::in(array_keys(Treinamento::modalidadesDisponiveis()))],
            'local' => ['nullable', 'string', 'max:255'],
            'vagas' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'data_inicio' => ['required', 'date'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
            'inscricoes_ate' => ['nullable', 'date'],
            'status' => ['required', Rule::in(array_keys(Treinamento::statusDisponiveis()))],
            'permite_inscricao' => ['boolean'],
            'gera_certificado' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'titulo' => 'título',
            'descricao' => 'descrição',
            'publico_alvo' => 'público-alvo',
            'carga_horaria' => 'carga horária',
            'data_inicio' => 'data de início',
            'data_fim' => 'data de término',
            'inscricoes_ate' => 'prazo de inscrição',
        ];
    }

    public function messages(): array
    {
        return [
            'data_fim.after_or_equal' => 'A data de término deve ser igual ou posterior à data de início.',
        ];
    }
}
