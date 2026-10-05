<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsuarioService
{
    public function cadastrar(string $nome, string $email, string $senha): Usuario
    {
        return Usuario::create([
            'nome' => trim($nome),
            'email' => Str::lower(trim($email)),
            'senha' => $senha,
        ]);
    }

    public function autenticar(string $email, string $senha): ?Usuario
    {
        $usuario = Usuario::where('email', Str::lower(trim($email)))->first();
        $hash = $usuario?->senha ?? Hash::make(Str::random(32));

        return Hash::check($senha, $hash) && $usuario ? $usuario : null;
    }

    public function buscarPorId(int $id): ?Usuario
    {
        return Usuario::find($id);
    }
}
