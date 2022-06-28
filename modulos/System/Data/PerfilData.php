<?php

namespace Modulos\System\Data;

class PerfilData
{
    const ADMIN = [
        'id' => 1,
        'nome' => 'Administrador',
        'descricao' => 'Acesso completo ao sistema',
        'status' => 1,
        'token_id' => 1,
    ];
    const USER = [
        'id' => 2,
        'nome' => 'Usuário',
        'descricao' => 'Acesso limitado ao sistema',
        'status' => 1,
        'token_id' => 1,
    ];
    const ALL = [
        self::ADMIN,
        self::USER,
    ];
    const ALL_ASC = self::ALL;
    const ALL_DESC = [
        self::USER,
        self::ADMIN,
    ];
}
