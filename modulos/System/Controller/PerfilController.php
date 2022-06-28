<?php

declare(strict_types=1);

namespace Modulos\System\Controller;

use App\Lib\ResponseTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Modulos\System\Service\PerfilService;
use Awurth\SlimValidation\Validator;

class PerfilController
{
    use ResponseTrait;

    public function __construct(PerfilService $service, Validator $valid)
    {
        $this->service = $service;
        $this->valid = $valid;
    }

    public function getAll(Request $request, Response $response)
    {
        $query = $request->getQueryParams();
        $dados = $this->service->getAll($query);
        return $this->withJson($dados);
    }

    public function get(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $dados = $this->service->get($id);
        return $this->withJson($dados);
    }
}
