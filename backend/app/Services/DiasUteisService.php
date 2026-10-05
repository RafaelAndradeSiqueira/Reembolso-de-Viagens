<?php

namespace App\Services;

use Carbon\CarbonImmutable;

class DiasUteisService
{
    public function __construct(private readonly string $fusoHorario)
    {
    }

    public function doMes(int $ano, int $mes, array $datasExcluidas = []): array
    {
        $excluidas = array_flip($datasExcluidas);
        $dias = [];

        for (
            $data = CarbonImmutable::create($ano, $mes, 1, 0, 0, 0, $this->fusoHorario);
            $data->month === $mes;
            $data = $data->addDay()
        ) {
            $diaSemana = $data->dayOfWeekIso;

            if ($diaSemana > 5 || isset($excluidas[$data->toDateString()])) {
                continue;
            }

            $dias[] = [
                'data' => $data->toDateString(),
                'dia' => $data->day,
                'dia_semana' => $diaSemana,
                'nome_dia' => ValorDiaSemanaService::DIAS_SEMANA[$diaSemana],
            ];
        }

        return $dias;
    }

    public function contarPorDiaSemana(array $dias): array
    {
        $contagem = array_fill_keys(array_keys(ValorDiaSemanaService::DIAS_SEMANA), 0);

        foreach ($dias as $dia) {
            $contagem[$dia['dia_semana']]++;
        }

        return $contagem;
    }
}
