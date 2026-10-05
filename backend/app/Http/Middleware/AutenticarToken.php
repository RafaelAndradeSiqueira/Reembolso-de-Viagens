<?php

namespace App\Http\Middleware;

use App\Services\TokenService;
use App\Services\UsuarioService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutenticarToken
{
    public function __construct(
        private readonly TokenService $tokens,
        private readonly UsuarioService $usuarios,
    ) {
    }

    public function handle(Request $request, Closure $proximo): Response
    {
        $usuarioId = $this->tokens->validar($request->bearerToken());
        $usuario = $usuarioId ? $this->usuarios->buscarPorId($usuarioId) : null;

        if (! $usuario) {
            return response()->json(['message' => 'Sessão expirada. Faça login novamente.'], 401);
        }

        $request->setUserResolver(fn () => $usuario);

        return $proximo($request);
    }
}
