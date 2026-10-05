<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AtualizarValoresRequest;
use App\Services\ValorDiaSemanaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ValorDiaSemanaController extends Controller
{
    public function __construct(private readonly ValorDiaSemanaService $valores)
    {
    }

    public function mostrar(Request $request): JsonResponse
    {
        return response()->json([
            'dias_semana' => ValorDiaSemanaService::DIAS_SEMANA,
            'valores' => $this->valores->buscar($request->user()),
        ]);
    }

    public function atualizar(AtualizarValoresRequest $request): JsonResponse
    {
        return response()->json([
            'valores' => $this->valores->salvar($request->user(), $request->validated('valores')),
        ]);
    }
}
