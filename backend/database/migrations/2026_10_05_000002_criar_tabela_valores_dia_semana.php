<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('valores_dia_semana', function (Blueprint $tabela) {
            $tabela->id();
            $tabela->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $tabela->unsignedTinyInteger('dia_semana');
            $tabela->decimal('valor', 10, 2);
            $tabela->timestamps();

            $tabela->unique(['usuario_id', 'dia_semana']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('valores_dia_semana');
    }
};
