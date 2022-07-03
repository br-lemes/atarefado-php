<?php

declare(strict_types=1);

namespace Modulos\System\Controller;

use App\Exception\ValidationException;
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
        $usuario = $request->getAttribute('usuario');
        $query = $request->getQueryParams();
        $dados = $this->service->getAll($usuario, $query);
        return $this->withJson($dados);
    }

    public function get(Request $request, Response $response)
    {
        $usuario = $request->getAttribute('usuario');
        $id = $request->getAttribute('id');
        $dados = $this->service->get($usuario, $id);
        return $this->withJson($dados);
    }

    public function post(Request $request, Response $response)
    {
        $rules = [
            'id' => V::optional(V::intVal()->min(1)->setName('ID')),
            'nome' => V::notBlank()->setName('Nome'),
            'descricao' => V::notBlank()->setName('Descrição'),
            'status' => V::optional(V::in(['0', '1'])->setName('Status')),
        ];
        return $this->createOrUpdate($request, $response, $rules);
    }

    public function put(Request $request, Response $response)
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
            if ($usuario->perfilId != 1) {
                throw new ValidationException('Não autorizado!', 401);
            }
            $data = $request->getParsedBody();
            $id = $request->getAttribute('id');
            $dados = $this->service->createOrUpdate($usuario, $data, $id);
            return $this->withJson($dados);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
