<?php

use App\Http\Controllers\Api\AutenticacaoController;
use App\Http\Controllers\Api\ReembolsoController;
use App\Http\Controllers\Api\ValorDiaSemanaController;
use App\Http\Middleware\AutenticarToken;
use Illuminate\Support\Facades\Route;

Route::post('/entrar', [AutenticacaoController::class, 'entrar'])->middleware('throttle:entrar');
Route::post('/cadastrar', [AutenticacaoController::class, 'cadastrar'])->middleware('throttle:cadastrar');

Route::middleware(AutenticarToken::class)->group(function () {
    Route::get('/eu', [AutenticacaoController::class, 'eu']);

    Route::get('/valores', [ValorDiaSemanaController::class, 'mostrar']);
    Route::put('/valores', [ValorDiaSemanaController::class, 'atualizar']);

    Route::post('/reembolsos/gerar', [ReembolsoController::class, 'gerar'])->middleware('throttle:ia');
});
