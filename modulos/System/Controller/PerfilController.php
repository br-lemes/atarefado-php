<?php

declare(strict_types=1);

namespace Modulos\System\Controller;

use App\Lib\ResponseTrait;
use Awurth\SlimValidation\Validator;
use Exception;
use Modulos\System\Service\PerfilService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator as V;

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

    public function create(Request $request, Response $response)
    {
        $rules = [
            'nome' => V::notBlank()->setName('Nome'),
            'descricao' => V::notBlank()->setName('Descrição'),
            'status' => V::optional(V::in(['0', '1'])->setName('Status')),
        ];
        return $this->createOrUpdate($request, $response, $rules);
    }

    public function update(Request $request, Response $response)
    {
        $rules = [
            'status' => V::optional(V::in(['0', '1'])->setName('Status')),
        ];
        return $this->createOrUpdate($request, $response, $rules);
    }

    private function createOrUpdate(Request $request, Response $response, array $rules = [])
    {
        $this->valid->validate($request, $rules);
        if (!$this->valid->isValid()) {
            return $this->withJson($this->valid->getErrors(), 400);
        }
        try {
            $usuario = $request->getAttribute('usuario');
            $data = $request->getParsedBody();
            $id = $request->getAttribute('id');
            $dados = $this->service->createOrUpdate($usuario, $data, $id);
            return $this->withJson($dados);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
