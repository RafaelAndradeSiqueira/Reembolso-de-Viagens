<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ValorDiaSemana extends Model
{
    protected $table = 'valores_dia_semana';

    protected $fillable = ['usuario_id', 'dia_semana', 'valor'];

    protected function casts(): array
    {
        return ['dia_semana' => 'integer', 'valor' => 'float'];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
