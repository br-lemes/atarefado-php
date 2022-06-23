<?php

namespace Modulos\System\Data;

class UsuarioData
{
    const ADMIN = [
        'id' => 1,
        'perfil_id' => 1,
        'nome' => 'Administrador',
        'email' => null,
        'login' => 'admin',
        'status' => 1,
        'token_id' => 1,
        'perfil_nome' => 'Administrador',
        'perfil_descricao' => 'Acesso completo ao sistema',
    ];
    const USER = [
        'id' => 2,
        'perfil_id' => 2,
        'nome' => 'Usuário',
        'email' => null,
        'login' => 'user',
        'status' => 1,
        'token_id' => 1,
        'perfil_nome' => 'Usuário',
        'perfil_descricao' => 'Acesso limitado ao sistema',
    ];
    const TEST = [
        'id' => 3,
        'perfil_id' => 2,
        'nome' => 'Teste',
        'email' => null,
        'login' => 'test',
        'status' => 0,
        'token_id' => 1,
        'perfil_nome' => 'Usuário',
        'perfil_descricao' => 'Acesso limitado ao sistema',
    ];
    const ALL = [
        self::ADMIN,
        self::USER,
        self::TEST,
    ];
    const ALL_ASC = self::ALL;
    const ALL_DESC = [
        self::TEST,
        self::USER,
        self::ADMIN,
    ];
}
