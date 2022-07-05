<?php

declare(strict_types=1);

namespace Modulos\System\Service;

use App\Exception\ValidationException;
use DomainException;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;
use Modulos\System\Models\Usuario;
use Modulos\System\Service\TokenJwt;
use Psr\Log\LoggerInterface;
use RuntimeException;

class AuthService
{
    protected $usuario;
    protected $jwt;

    public function __construct(LoggerInterface $logger, TokenJwt $jwt, Usuario $usuario)
    {
        $this->logger = $logger;
        $this->jwt = $jwt;
        $this->model = $usuario;
    }

    public function login($data)
    {
        DB::beginTransaction();
        try {
            $user = $this->model->from('sys_usuario as u')
                ->leftJoin('sys_perfil as p', 'p.id', 'u.perfil_id')
                ->where('u.login', $data['login'])
                ->select('u.*', 'p.nome as perfil_nome', 'p.descricao as perfil_descricao')
                ->first();
            if (!$user) {
                throw new ValidationException('Usuário ou senha incorreta!', 401);
            }
            if ($user->status == 0) {
                throw new ValidationException('Usuário inativo, entre contato com o suporte!', 401);
            }
            if (!$user->checkPassword($data)) {
                throw new ValidationException('Usuário ou senha incorreta!', 401);
            }
            $token = $this->jwt->create($user, $data);
            DB::commit();
            return [
                'token_access' => $token->token_access,
                'token_refresh' => $token->token_refresh,
                'usuario' => $user->toArray(),
            ];
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function info($data)
    {
        $usuario = $this->model->from('sys_usuario as u')
            ->leftJoin('sys_perfil as p', 'p.id', 'u.perfil_id')
            ->where('u.id', $data->id)
            ->select('u.*', 'p.nome as perfil_nome', 'p.descricao as perfil_descricao')
            ->first();
        if (!$usuario) {
            throw new ValidationException('Usuário não encontrado!', 500);
        }
        return $usuario;
    }

    public function refresh($token_refresh)
    {
        try {
            $token_access = $this->jwt->refresh($token_refresh);
            if (!$token_access) {
                throw new ValidationException('Não foi possível atualizar o token!', 401);
            }
            return ['token_access' => $token_access];
        } catch (RuntimeException | DomainException $ex) {
            throw new ValidationException('Não foi possível atualizar o token!', 401);
        }
    }
}
