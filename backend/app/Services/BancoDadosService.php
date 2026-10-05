<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class BancoDadosService
{
    public function prepararSeNecessario(): void
    {
        $conexao = config('database.default');
        $arquivo = config("database.connections.{$conexao}.database");

        if (config("database.connections.{$conexao}.driver") === 'sqlite' && ! file_exists($arquivo)) {
            if (! is_dir(dirname($arquivo))) {
                mkdir(dirname($arquivo), 0700, true);
            }
            touch($arquivo);
            @chmod($arquivo, 0600);
        }

        if (! Schema::hasTable('usuarios')) {
            Artisan::call('migrate', ['--force' => true]);
        }
    }
}
