<?php

declare(strict_types=1);

namespace Modulos\System\Service;

use App\Exception\ValidationException;
use Modulos\System\Models\Perfil;
use Psr\Log\LoggerInterface;

class PerfilService
{
    public function __construct(LoggerInterface $logger, Perfil $model)
    {
        $this->logger = $logger;
        $this->model = $model;
    }

    public function getAll($query)
    {
        $fieldMap = [
            'id' => 'id',
            'nome' => 'nome',
            'descricao' => 'descricao',
            'status' => 'status',
        ];
        $queryBuilder = $this->model->whereMap($query, $fieldMap)
            ->orderMap($query, $fieldMap);
        return $queryBuilder->get();
    }

    public function get($id)
    {
        $dados = $this->getAll(['id' => $id])->toArray();
        if (!$dados) {
            throw new ValidationException('Perfil não encontrado!', 404);
        }
        return $dados[0];
    }
}
