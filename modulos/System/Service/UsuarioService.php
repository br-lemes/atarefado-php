<?php

declare(strict_types=1);

namespace Modulos\System\Service;

use App\Exception\ValidationException;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;
use Modulos\System\Models\Usuario;
use Psr\Log\LoggerInterface;

class UsuarioService
{
    public function __construct(LoggerInterface $logger, Usuario $model)
    {
        $this->logger = $logger;
        $this->model = $model;
    }

    public function getAll($query)
    {
        $fieldMap = [
            'id' => 'u.id',
            'perfil_id' => 'u.perfil_id',
            'nome' => 'u.nome',
            'matricula' => 'u.matricula',
            'email' => 'u.email',
            'login' => 'u.login',
            'ldap' => 'u.ldap',
            'status' => 'u.status',
            'perfil_nome' => 'p.nome',
            'perfil_descricao' => 'p.descricao',
        ];
        $queryBuilder = $this->model->from('sys_usuario as u')
            ->leftJoin('sys_perfil as p', 'p.id', 'u.perfil_id')
            ->whereMap($query, $fieldMap)
            ->orderMap($query, $fieldMap)
            ->select('u.*', 'p.nome as perfil_nome', 'p.descricao as perfil_descricao');
        return $queryBuilder->get();
    }

    public function get($id)
    {
        $dados = $this->getAll(['id' => $id])->toArray();
        if (!$dados) {
            throw new ValidationException('Usuário não encontrado!', 404);
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
                    throw new ValidationException('Usuário não encontrado!', 404);
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
            if (isset($data['email'])) {
                $checkEmail = $this->model->where('email', $data['email'])->exists();
                if ($checkEmail && $save->email != $data['email']) {
                    throw new ValidationException('Já existe usuário com este e-mail!', 400);
                }
            }
            if (isset($data['login'])) {
                $checkLogin = $this->model->where('login', $data['login'])->exists();
                if ($checkLogin && $save->login != $data['login']) {
                    throw new ValidationException('Já existe usuário com este login!', 400);
                }
            }
            if (isset($data['senha'])) {
                $save->hash = time();
                $save->senha = hash('sha512', $data['senha'] . $save->hash);
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
