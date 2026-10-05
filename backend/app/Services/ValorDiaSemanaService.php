<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class ValorDiaSemanaService
{
    public const DIAS_SEMANA = [
        1 => 'Segunda-feira',
        2 => 'Terça-feira',
        3 => 'Quarta-feira',
        4 => 'Quinta-feira',
        5 => 'Sexta-feira',
    ];

    public function buscar(Usuario $usuario): ?array
    {
        $salvos = $usuario->valoresDiaSemana()->pluck('valor', 'dia_semana');

        if ($salvos->isEmpty()) {
            return null;
        }

        return $this->normalizar($salvos->all());
    }

    public function salvar(Usuario $usuario, array $valores): array
    {
        $valores = $this->normalizar($valores);

        DB::transaction(function () use ($usuario, $valores) {
            foreach ($valores as $diaSemana => $valor) {
                $usuario->valoresDiaSemana()->updateOrCreate(
                    ['dia_semana' => $diaSemana],
                    ['valor' => $valor],
                );
            }
        });

        return $valores;
    }

    private function normalizar(array $valores): array
    {
        $normalizados = [];

        foreach (array_keys(self::DIAS_SEMANA) as $dia) {
            $normalizados[$dia] = round((float) ($valores[$dia] ?? 0), 2);
        }

        return $normalizados;
    }
}
