<?php

declare(strict_types=1);

namespace Modulos\System\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Modulos\System\Models\Token;
use Selective\Config\Configuration;

class TokenJwt
{
    protected $configJwt;
    protected $model;

    public function __construct(Configuration $config, Token $model)
    {
        $this->configJwt = $config->getArray('settings.jwt');
        $this->model = $model;
    }

    private function encode($data, $exp_sec)
    {
        $issuedAt = time();
        $expire = $issuedAt + $exp_sec;
        $token = JWT::encode(
            ['iat' => $issuedAt, 'exp' => $expire, 'nbf' => $issuedAt - 1, 'data' => $data],
            $this->configJwt['secret'],
            'HS256'
        );
        return ['token' => $token, 'expire' => $expire];
    }

    public function create($usuario, $data)
    {
        $save = new $this->model;
        $save->usuario_id = $usuario->id;
        $save->ip = $data['ip'];
        $save->browser = json_encode($data['browser']);
        $save->save();
        $tokenData = [
            'id' => $usuario->id,
            'perfilId' => $usuario->perfil_id,
            'tokenId' => $save->id
        ];
        $tokenAccess = $this->encode($tokenData, $this->configJwt['exp_sec_access']);
        $tokenRefresh = $this->encode($tokenData, $this->configJwt['exp_sec_refresh']);
        $save->token_access = $tokenAccess['token'];
        $save->token_refresh = $tokenRefresh['token'];
        $save->token_exp = gmdate('Y-m-d H:i:s', $tokenRefresh['expire']);
        $save->save();
        return $save;
    }

    public function getValid($token)
    {
        $decoded = JWT::decode($token, new Key($this->configJwt['secret'], 'HS256'));
        $data = $decoded->data;
        $dbToken = $this->model->find($data->tokenId);
        if (!$dbToken || $dbToken->logout_date || $dbToken->token_access != $token) {
            return false;
        }
        return $data;
    }
}
