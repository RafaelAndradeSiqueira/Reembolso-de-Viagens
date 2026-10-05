<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AtualizarValoresRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'valores' => ['required', 'array'],
            'valores.1' => ['required', 'numeric', 'min:0', 'max:100000'],
            'valores.2' => ['required', 'numeric', 'min:0', 'max:100000'],
            'valores.3' => ['required', 'numeric', 'min:0', 'max:100000'],
            'valores.4' => ['required', 'numeric', 'min:0', 'max:100000'],
            'valores.5' => ['required', 'numeric', 'min:0', 'max:100000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'valores.1' => 'segunda-feira',
            'valores.2' => 'terça-feira',
            'valores.3' => 'quarta-feira',
            'valores.4' => 'quinta-feira',
            'valores.5' => 'sexta-feira',
        ];
    }
}
