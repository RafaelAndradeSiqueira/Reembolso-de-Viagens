<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\FalhaOpenRouterException;
use App\Http\Controllers\Controller;
use App\Http\Requests\GerarReembolsoRequest;
use App\Services\ReembolsoService;
use App\Services\ValorDiaSemanaService;
use Illuminate\Http\JsonResponse;

class ReembolsoController extends Controller
{
    public function __construct(
        private readonly ReembolsoService $reembolsos,
        private readonly ValorDiaSemanaService $valores,
    ) {
    }

    public function gerar(GerarReembolsoRequest $request): JsonResponse
    {
        $valores = $this->valores->buscar($request->user());

        if (! $valores) {
            return response()->json(['message' => 'Configure os valores de cada dia da semana primeiro.'], 422);
        }

        try {
            $resultado = $this->reembolsos->gerar(
                (int) $request->validated('ano'),
                (int) $request->validated('mes'),
                $valores,
                $request->validated('datas_excluidas', []),
            );
        } catch (FalhaOpenRouterException $erro) {
            return response()->json(['message' => $erro->getMessage()], 502);
        }

        return response()->json($resultado);
    }
}
