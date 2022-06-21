<?php

declare(strict_types=1);

namespace Modulos\System\Controller;

use App\Lib\ResponseTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Modulos\System\Service\UsuarioService;
use Awurth\SlimValidation\Validator;
use Exception;

class UsuarioController
{
    use ResponseTrait;

    public function __construct(UsuarioService $service, Validator $valid)
    {
        $this->service = $service;
        $this->valid = $valid;
    }

    public function getAll(Request $request, Response $response)
    {
        try {
            $query = $request->getQueryParams();
            $dados = $this->service->getAll($query);
            return $this->withJson($dados);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function get(Request $request, Response $response)
    {
        try {
            $id = $request->getAttribute('id');
            $usuario = $this->service->get($id);
            return $this->withJson($usuario);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
