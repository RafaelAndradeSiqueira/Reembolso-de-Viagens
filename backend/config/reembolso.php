<?php

return [
    'validade_token' => (int) env('AUTH_TOKEN_TTL', 60 * 24 * 7),

    'fuso_horario' => 'America/Sao_Paulo',

    'migrar_automaticamente' => (bool) env('DB_MIGRAR_AUTOMATICAMENTE', false),
];
