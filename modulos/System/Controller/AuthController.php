<?php

declare(strict_types=1);

namespace Modulos\System\Controller;

use Exception;
use App\Lib\ResponseTrait;
use Awurth\SlimValidation\Validator;
use Respect\Validation\Validator as V;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Modulos\System\Service\AuthService;

class AuthController
{
    use ResponseTrait;

    public function __construct(AuthService $service, Validator $valid)
    {
        $this->service = $service;
        $this->valid = $valid;
    }

    public function login(Request $request, Response $response)
    {
        $rules = [
            'login' => V::notBlank()->setName('login'),
            'senha' => V::notBlank()->setName('senha'),
        ];
        $this->valid->validate($request, $rules);
        if (!$this->valid->isValid()) {
            return $this->withJson($this->valid->getErrors(), 400);
        }
        try {
            $data = $request->getParsedBody();
            $data['ip'] = $request->getServerParams()['REMOTE_ADDR'];
            $data['browser'] = $request->getHeader('User-Agent');
            $data = $this->service->login($data);
            return $this->withJson($data);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function info(Request $request, Response $response)
    {
        try {
            $usuario = $request->getAttribute('usuario');
            $data = $this->service->info($usuario);
            return $this->withJson($data);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
