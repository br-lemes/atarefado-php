<?php

declare(strict_types=1);

namespace Modulos\System\Service;

use Firebase\JWT\JWT;
use Selective\Config\Configuration;

class TokenJwt
{
    protected $config;
    protected $configJwt;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
        $this->configJwt = $this->config->getArray('settings.jwt');
    }

    private function tokenParam(array $data): array
    {
        $issuedAt = time();
        $expire = $issuedAt + $this->configJwt['exp_sec_access'];
        return ['iat' => $issuedAt, 'exp' => $expire, 'nbf' => $issuedAt - 1, 'data' => $data];
    }

    public function encodeAccess(array $data)
    {
        $tokenParam = $this->tokenParam($data);
        return JWT::encode($tokenParam, $this->configJwt['secret'], 'HS256');
    }

    public function encodeRefresh(array $data)
    {
        $tokenParam = $this->tokenParam($data);
        $jwtToken = JWT::encode($tokenParam, $this->configJwt['secret'], 'HS256');
        return ['token' => $jwtToken, 'exp' => $tokenParam['exp']];
    }

    public function decode($jwt)
    {
        return JWT::decode($jwt, $this->configJwt['secret'], ['HS256']);
    }
}
