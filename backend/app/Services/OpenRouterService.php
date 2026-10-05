<?php

namespace App\Services;

use App\Exceptions\FalhaOpenRouterException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class OpenRouterService
{
    public function __construct(private readonly array $config)
    {
    }

    public function enviarMensagens(array $mensagens, array $opcoes = []): array
    {
        if (empty($this->config['chave'])) {
            throw new FalhaOpenRouterException('OPENROUTER_API_KEY não configurada no .env.');
        }

        $modelos = array_values(array_unique([$this->config['modelo'], ...$this->config['modelos_reserva']]));

        try {
            $resposta = Http::withToken($this->config['chave'])
                ->withHeaders([
                    'HTTP-Referer' => $this->config['url_app'],
                    'X-Title' => $this->config['nome_app'],
                ])
                ->acceptJson()
                ->timeout($this->config['tempo_limite'])
                ->when($this->config['certificados_ca'], fn ($http, $arquivo) => $http->withOptions(['verify' => $arquivo]))
                ->post(rtrim($this->config['url_base'], '/').'/chat/completions', [
                    'model' => $modelos[0],
                    'models' => $modelos,
                    'messages' => $mensagens,
                    'temperature' => $opcoes['temperatura'] ?? 0,
                ]);
        } catch (ConnectionException) {
            throw new FalhaOpenRouterException('Não foi possível conectar ao OpenRouter. Tente novamente.');
        }

        if ($resposta->failed()) {
            $mensagemErro = $resposta->json('error.message') ?? $resposta->body();

            throw new FalhaOpenRouterException(match ($resposta->status()) {
                401 => 'Chave do OpenRouter inválida.',
                402 => 'Sem créditos no OpenRouter para esse modelo.',
                429 => 'Limite gratuito do modelo atingido. Aguarde um pouco e tente de novo.',
                default => "Erro do OpenRouter ({$resposta->status()}): {$mensagemErro}",
            });
        }

        if ($resposta->json('error')) {
            throw new FalhaOpenRouterException('Erro do OpenRouter: '.$resposta->json('error.message'));
        }

        $conteudo = $resposta->json('choices.0.message.content');

        if (! is_string($conteudo) || trim($conteudo) === '') {
            throw new FalhaOpenRouterException('O modelo devolveu uma resposta vazia. Tente novamente.');
        }

        return [
            'conteudo' => $conteudo,
            'modelo' => $resposta->json('model', $modelos[0]),
            'uso' => $resposta->json('usage', []),
        ];
    }
}
