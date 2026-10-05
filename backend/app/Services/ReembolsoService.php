<?php

namespace App\Services;

use App\Exceptions\FalhaOpenRouterException;

class ReembolsoService
{
    public function __construct(
        private readonly DiasUteisService $diasUteis,
        private readonly OpenRouterService $openRouter,
    ) {
    }

    public function gerar(int $ano, int $mes, array $valores, array $datasExcluidas = []): array
    {
        $dias = $this->diasUteis->doMes($ano, $mes, $datasExcluidas);
        $contagem = $this->diasUteis->contarPorDiaSemana($dias);

        $resposta = $this->openRouter->enviarMensagens($this->montarMensagens($ano, $mes, $dias, $valores));
        $ia = $this->interpretarResposta($resposta['conteudo']);

        $totalEsperado = $this->calcularTotal($contagem, $valores);

        return [
            'ano' => $ano,
            'mes' => $mes,
            'dias_uteis' => count($dias),
            'dias' => array_map(fn (array $d) => $d + ['valor' => $valores[$d['dia_semana']]], $dias),
            'detalhamento' => $this->detalhar($contagem, $valores),
            'ia' => [
                'modelo' => $resposta['modelo'],
                'total' => $ia['total'],
                'explicacao' => $ia['explicacao'],
                'tokens' => $resposta['uso']['total_tokens'] ?? null,
            ],
            'total_esperado' => $totalEsperado,
            'confere' => abs($ia['total'] - $totalEsperado) < 0.01,
        ];
    }

    private function montarMensagens(int $ano, int $mes, array $dias, array $valores): array
    {
        $linhasValores = collect(ValorDiaSemanaService::DIAS_SEMANA)
            ->map(fn (string $nome, int $dia) => "- {$nome}: R$ ".number_format($valores[$dia], 2, '.', ''))
            ->implode("\n");

        $linhasDias = collect($dias)
            ->map(fn (array $d) => "- {$d['data']} ({$d['nome_dia']})")
            ->implode("\n");

        $mesAno = sprintf('%02d/%d', $mes, $ano);

        return [
            [
                'role' => 'system',
                'content' => 'Você é um assistente financeiro que calcula reembolso de viagens de carro. '
                    .'Seja exato nas contas. Responda SOMENTE com um JSON válido, sem markdown, no formato: '
                    .'{"total": number, "explicacao": string}. '
                    .'"total" é o valor em reais com ponto decimal (ex: 123.45). '
                    .'"explicacao" é um resumo curto em português de como chegou no valor '
                    .'(quantos dias de cada tipo x valor de cada dia).',
            ],
            [
                'role' => 'user',
                'content' => "Calcule quanto a empresa deve reembolsar no mês {$mesAno}.\n\n"
                    ."Valor pago por dia da semana:\n{$linhasValores}\n\n"
                    .'Dias úteis trabalhados ('.count($dias)." dias):\n{$linhasDias}\n\n"
                    .'Some o valor de cada dia trabalhado de acordo com o dia da semana.',
            ],
        ];
    }

    private function interpretarResposta(string $conteudo): array
    {
        if (preg_match('/\{.*\}/s', $conteudo, $encontrado)) {
            $dados = json_decode($encontrado[0], true);

            if (is_array($dados) && isset($dados['total']) && is_numeric($dados['total'])) {
                return [
                    'total' => round((float) $dados['total'], 2),
                    'explicacao' => (string) ($dados['explicacao'] ?? ''),
                ];
            }
        }

        throw new FalhaOpenRouterException('Não consegui entender a resposta do modelo. Tente gerar novamente.');
    }

    private function calcularTotal(array $contagem, array $valores): float
    {
        return round(array_sum(array_map(fn (int $dia) => $contagem[$dia] * $valores[$dia], array_keys($contagem))), 2);
    }

    private function detalhar(array $contagem, array $valores): array
    {
        return collect(ValorDiaSemanaService::DIAS_SEMANA)
            ->map(fn (string $nome, int $dia) => [
                'dia_semana' => $dia,
                'nome_dia' => $nome,
                'quantidade' => $contagem[$dia],
                'valor' => $valores[$dia],
                'subtotal' => round($contagem[$dia] * $valores[$dia], 2),
            ])
            ->values()
            ->all();
    }
}
