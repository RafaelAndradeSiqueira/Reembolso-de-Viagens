<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usuario extends Model
{
    protected $table = 'usuarios';

    protected $fillable = ['nome', 'email', 'senha'];

    protected $hidden = ['senha'];

    protected function casts(): array
    {
        return ['senha' => 'hashed'];
    }

    public function valoresDiaSemana(): HasMany
    {
        return $this->hasMany(ValorDiaSemana::class, 'usuario_id');
    }

    public function paraResposta(): array
    {
        return ['id' => $this->id, 'nome' => $this->nome, 'email' => $this->email];
    }
}
