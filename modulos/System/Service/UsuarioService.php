<?php

declare(strict_types=1);

namespace Modulos\System\Service;

use App\Exception\ValidationException;
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
}
