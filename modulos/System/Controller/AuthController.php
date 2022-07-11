<?php

declare(strict_types=1);

namespace Modulos\System\Controller;

use App\Lib\ResponseTrait;
use Awurth\SlimValidation\Validator;
use Modulos\System\Service\AuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator as V;

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
        $data = $request->getParsedBody();
        $data['ip'] = $request->getServerParams()['REMOTE_ADDR'];
        $data['browser'] = $request->getHeader('User-Agent');
        $data = $this->service->login($data);
        return $this->withJson($data);
    }

    public function info(Request $request, Response $response)
    {
        $usuario = $request->getAttribute('usuario');
        $dados = $this->service->info($usuario);
        return $this->withJson($dados);
    }

    public function refresh(Request $request, Response $response)
    {
        $rules = [
            'token_refresh' => V::notBlank()->setName('Token Refresh'),
        ];
        $this->valid->validate($request, $rules);
        if (!$this->valid->isValid()) {
            return $this->withJson($this->valid->getErrors(), 400);
        }
        $data = $request->getParsedBody();
        $data = $this->service->refresh($data['token_refresh']);
        return $this->withJson($data);
    }

    public function logout(Request $request, Response $response)
    {
        $rules = [
            'token_id' => V::optional(V::intVal()->min(1)->setName('Token ID')),
        ];
        $this->valid->validate($request, $rules);
        if (!$this->valid->isValid()) {
            return $this->withJson($this->valid->getErrors(), 400);
        }
        $usuario = $request->getAttribute('usuario');
        $data = $request->getParsedBody();
        $data = $this->service->logout($usuario, @$data['token_id']);
        return $this->withJson($data);
    }
}
