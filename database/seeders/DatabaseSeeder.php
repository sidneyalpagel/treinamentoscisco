<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Inscricao;
use App\Models\Treinamento;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Áreas / setores
        $areasDados = [
            ['nome' => 'Atenção à Saúde', 'sigla' => 'SAUDE'],
            ['nome' => 'Administrativo', 'sigla' => 'ADM'],
            ['nome' => 'Tecnologia da Informação', 'sigla' => 'TI'],
            ['nome' => 'Recursos Humanos', 'sigla' => 'RH'],
        ];
        $areas = [];
        foreach ($areasDados as $dados) {
            $areas[$dados['sigla']] = Area::updateOrCreate(['nome' => $dados['nome']], $dados);
        }

        // Administrador Geral (super-admin)
        User::updateOrCreate(
            ['email' => 'admin@treinamentos.gov.br'],
            [
                'name' => 'Administrador Geral',
                'password' => 'admin123',
                'role' => User::ROLE_ADMIN,
                'area_id' => null,
                'ativo' => true,
            ]
        );

        // Gestores de treinamentos
        $gestorSaude = User::updateOrCreate(
            ['email' => 'gestor.saude@treinamentos.gov.br'],
            [
                'name' => 'Gestor de Saúde',
                'password' => 'gestor123',
                'role' => User::ROLE_GESTOR,
                'area_id' => $areas['SAUDE']->id,
                'ativo' => true,
            ]
        );
        $gestorAdm = User::updateOrCreate(
            ['email' => 'gestor.adm@treinamentos.gov.br'],
            [
                'name' => 'Gestor Administrativo',
                'password' => 'gestor123',
                'role' => User::ROLE_GESTOR,
                'area_id' => $areas['ADM']->id,
                'ativo' => true,
            ]
        );

        // Treinamentos (com dono)
        $exemplos = [
            [
                'slug' => 'atendimento-humanizado-ao-cidadao',
                'titulo' => 'Atendimento Humanizado ao Cidadão',
                'descricao' => "Capacitação voltada à melhoria da qualidade do atendimento ao público, com foco no acolhimento, na empatia e na comunicação eficaz.\n\nConteúdo:\n- Princípios do acolhimento\n- Comunicação não violenta\n- Gestão de conflitos no atendimento",
                'publico_alvo' => 'Servidores da recepção e atendimento ao público',
                'instrutor' => 'Equipe de Desenvolvimento de Pessoas',
                'carga_horaria' => 8,
                'modalidade' => Treinamento::MODALIDADE_PRESENCIAL,
                'local' => 'Auditório Central',
                'vagas' => 40,
                'dias' => 7,
                'status' => Treinamento::STATUS_PUBLICADO,
                'dono' => $gestorSaude->id,
            ],
            [
                'slug' => 'seguranca-do-paciente',
                'titulo' => 'Segurança do Paciente e Controle de Infecção',
                'descricao' => 'Treinamento sobre protocolos de segurança do paciente, higienização das mãos e prevenção de infecções relacionadas à assistência à saúde.',
                'publico_alvo' => 'Profissionais da área da saúde',
                'instrutor' => 'Comissão de Controle de Infecção',
                'carga_horaria' => 16,
                'modalidade' => Treinamento::MODALIDADE_PRESENCIAL,
                'local' => 'Centro de Especialidades',
                'vagas' => 25,
                'dias' => 15,
                'status' => Treinamento::STATUS_PUBLICADO,
                'dono' => $gestorSaude->id,
            ],
            [
                'slug' => 'lgpd-na-administracao-publica',
                'titulo' => 'LGPD na Administração Pública',
                'descricao' => 'Fundamentos da Lei Geral de Proteção de Dados aplicados ao setor público: tratamento de dados pessoais, direitos dos titulares e boas práticas.',
                'publico_alvo' => 'Todos os servidores',
                'instrutor' => 'Assessoria Jurídica',
                'carga_horaria' => 4,
                'modalidade' => Treinamento::MODALIDADE_ONLINE,
                'local' => 'Transmissão online',
                'vagas' => null,
                'dias' => 22,
                'status' => Treinamento::STATUS_PUBLICADO,
                'dono' => $gestorAdm->id,
            ],
            [
                'slug' => 'primeiros-socorros',
                'titulo' => 'Primeiros Socorros no Ambiente de Trabalho',
                'descricao' => 'Noções básicas de primeiros socorros, suporte básico de vida e procedimentos de emergência.',
                'publico_alvo' => 'Brigadistas e interessados',
                'instrutor' => 'Corpo de Bombeiros (parceria)',
                'carga_horaria' => 6,
                'modalidade' => Treinamento::MODALIDADE_PRESENCIAL,
                'local' => 'Sala de Treinamento 2',
                'vagas' => 30,
                'dias' => 4,
                'status' => Treinamento::STATUS_PUBLICADO,
                'dono' => $gestorSaude->id,
            ],
            [
                'slug' => 'gestao-de-processos-e-qualidade',
                'titulo' => 'Gestão de Processos e Qualidade',
                'descricao' => 'Introdução ao mapeamento de processos, indicadores e melhoria contínua na administração pública.',
                'publico_alvo' => 'Gestores e coordenadores',
                'instrutor' => 'Setor de Planejamento',
                'carga_horaria' => 12,
                'modalidade' => Treinamento::MODALIDADE_HIBRIDO,
                'local' => 'Auditório Central + Online',
                'vagas' => 35,
                'dias' => 30,
                'status' => Treinamento::STATUS_RASCUNHO,
                'dono' => $gestorAdm->id,
            ],
        ];

        foreach ($exemplos as $dados) {
            $dias = $dados['dias'];
            $dono = $dados['dono'];
            unset($dados['dias'], $dados['dono']);

            $inicio = Carbon::now()->addDays($dias)->setTime(9, 0);

            Treinamento::updateOrCreate(
                ['slug' => $dados['slug']],
                array_merge($dados, [
                    'user_id' => $dono,
                    'data_inicio' => $inicio,
                    'data_fim' => (clone $inicio)->addHours(min($dados['carga_horaria'], 8)),
                    'inscricoes_ate' => (clone $inicio)->subDays(2)->toDateString(),
                    'permite_inscricao' => true,
                    'gera_certificado' => true,
                ])
            );
        }

        // Treinamentos sem dono (criados antes do controle de acesso) vão para o gestor de saúde
        Treinamento::whereNull('user_id')->update(['user_id' => $gestorSaude->id]);

        // Inscrições e sessões de exemplo
        $inscricoesExemplo = [
            'atendimento-humanizado-ao-cidadao' => [
                ['nome' => 'Maria Silva Santos', 'email' => 'maria.santos@exemplo.gov.br', 'orgao' => 'Secretaria de Saúde', 'cargo' => 'Recepcionista', 'status' => Inscricao::STATUS_CONFIRMADA],
                ['nome' => 'João Pereira Souza', 'email' => 'joao.souza@exemplo.gov.br', 'orgao' => 'Secretaria de Saúde', 'cargo' => 'Atendente', 'status' => Inscricao::STATUS_CONFIRMADA],
                ['nome' => 'Ana Carolina Oliveira', 'email' => 'ana.oliveira@exemplo.gov.br', 'orgao' => 'Administração Geral', 'cargo' => 'Assistente Administrativo', 'status' => Inscricao::STATUS_PENDENTE],
            ],
            'primeiros-socorros' => [
                ['nome' => 'Carlos Eduardo Lima', 'email' => 'carlos.lima@exemplo.gov.br', 'orgao' => 'Manutenção', 'cargo' => 'Brigadista', 'status' => Inscricao::STATUS_CONFIRMADA],
                ['nome' => 'Fernanda Costa', 'email' => 'fernanda.costa@exemplo.gov.br', 'orgao' => 'Recursos Humanos', 'cargo' => 'Analista', 'status' => Inscricao::STATUS_CONFIRMADA],
            ],
        ];

        foreach ($inscricoesExemplo as $slug => $pessoas) {
            $treinamento = Treinamento::where('slug', $slug)->first();
            if (! $treinamento) {
                continue;
            }
            foreach ($pessoas as $pessoa) {
                Inscricao::updateOrCreate(
                    ['treinamento_id' => $treinamento->id, 'email' => $pessoa['email']],
                    $pessoa
                );
            }

            if ($treinamento->sessoes()->count() === 0) {
                $treinamento->sessoes()->create([
                    'titulo' => 'Encontro único',
                    'data' => $treinamento->data_inicio->toDateString(),
                    'hora_inicio' => $treinamento->data_inicio->format('H:i'),
                    'hora_fim' => $treinamento->data_fim?->format('H:i'),
                ]);
            }
        }
    }
}
