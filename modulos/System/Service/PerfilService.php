<?php

declare(strict_types=1);

namespace Modulos\System\Service;

use App\Exception\ValidationException;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;
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

    public function createOrUpdate($usuario, $data, $id = null)
    {
        try {
            DB::beginTransaction();
            if (!$id && isset($data['id'])) {
                $id = $data['id'];
            }
            if ($id) {
                $save = $this->model->find($id);
                if (!$save) {
                    throw new ValidationException('Perfil não encontrado!', 404);
                }
                if (!is_array($data)) {
                    DB::rollBack();
                    return $save;
                }
            } else {
                $save = new $this->model;
                $save->status = 1;
                $save->token_id = $usuario->tokenId;
            }
            $save->fill($data);
            $save->save();
            DB::commit();
            return $save;
        } catch (Exception $ex) {
            DB::rollBack();
            $this->logger->error($ex->getMessage());
            throw $ex;
        }
    }
}
