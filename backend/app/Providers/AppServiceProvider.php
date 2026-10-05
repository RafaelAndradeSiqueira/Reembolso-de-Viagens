<?php

namespace App\Providers;

use App\Services\BancoDadosService;
use App\Services\DiasUteisService;
use App\Services\OpenRouterService;
use App\Services\TokenService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TokenService::class, fn () => new TokenService(config('reembolso.validade_token')));
        $this->app->singleton(DiasUteisService::class, fn () => new DiasUteisService(config('reembolso.fuso_horario')));
        $this->app->singleton(OpenRouterService::class, fn () => new OpenRouterService(config('services.openrouter')));
    }

    public function boot(): void
    {
        RateLimiter::for('entrar', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('cadastrar', fn (Request $request) => [
            Limit::perMinute(3)->by($request->ip()),
            Limit::perHour(10)->by($request->ip()),
        ]);
        RateLimiter::for('ia', fn (Request $request) => Limit::perMinute(6)->by($request->user()?->id ?? $request->ip()));

        if (config('reembolso.migrar_automaticamente') && ! $this->app->runningInConsole()) {
            $this->app->booted(fn () => app(BancoDadosService::class)->prepararSeNecessario());
        }
    }
}
