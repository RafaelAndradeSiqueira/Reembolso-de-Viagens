<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CadastrarUsuarioRequest;
use App\Http\Requests\EntrarRequest;
use App\Models\Usuario;
use App\Services\TokenService;
use App\Services\UsuarioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AutenticacaoController extends Controller
{
    public function __construct(
        private readonly UsuarioService $usuarios,
        private readonly TokenService $tokens,
    ) {
    }

    public function entrar(EntrarRequest $request): JsonResponse
    {
        $usuario = $this->usuarios->autenticar($request->input('email'), $request->input('senha'));

        if (! $usuario) {
            return response()->json(['message' => 'E-mail ou senha inválidos.'], 422);
        }

        return $this->respostaComToken($usuario);
    }

    public function cadastrar(CadastrarUsuarioRequest $request): JsonResponse
    {
        $usuario = $this->usuarios->cadastrar(
            $request->validated('nome'),
            $request->validated('email'),
            $request->validated('senha'),
        );

        return $this->respostaComToken($usuario, 201);
    }

    public function eu(Request $request): JsonResponse
    {
        return response()->json(['usuario' => $request->user()->paraResposta()]);
    }

    private function respostaComToken(Usuario $usuario, int $status = 200): JsonResponse
    {
        return response()->json([
            'usuario' => $usuario->paraResposta(),
            ...$this->tokens->gerar($usuario->id),
        ], $status);
    }
}
