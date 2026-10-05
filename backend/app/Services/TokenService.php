<?php

namespace App\Services;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class TokenService
{
    public function __construct(private readonly int $validadeEmMinutos)
    {
    }

    public function gerar(int $usuarioId): array
    {
        $expiraEm = now()->addMinutes($this->validadeEmMinutos);

        return [
            'token' => Crypt::encryptString(json_encode(['id' => $usuarioId, 'expira' => $expiraEm->timestamp])),
            'expira_em' => $expiraEm->toIso8601String(),
        ];
    }

    public function validar(?string $token): ?int
    {
        if (! $token) {
            return null;
        }

        try {
            $dados = json_decode(Crypt::decryptString($token), true);
        } catch (DecryptException) {
            return null;
        }

        if (! is_array($dados) || empty($dados['id']) || ($dados['expira'] ?? 0) < now()->timestamp) {
            return null;
        }

        return (int) $dados['id'];
    }
}
