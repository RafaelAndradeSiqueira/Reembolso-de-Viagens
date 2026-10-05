<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GerarReembolsoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ano' => ['required', 'integer', 'between:2000,2100'],
            'mes' => ['required', 'integer', 'between:1,12'],
            'datas_excluidas' => ['sometimes', 'array', 'max:31'],
            'datas_excluidas.*' => ['date_format:Y-m-d'],
        ];
    }
}
