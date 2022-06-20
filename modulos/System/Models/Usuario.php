<?php

namespace Modulos\System\Models;

use App\Models\BaseModel;

class Usuario extends BaseModel
{
    protected $table = 'sys_usuario';
    protected $fillable = ['perfil_id', 'nome', 'email', 'login', 'senha', 'hash', 'status'];
    protected $hidden = ['senha', 'hash'];

    public function checkPassword($user)
    {
        if (!isset($user['senha']) or !isset($this->senha)) {
            return false;
        }
        return hash('sha512', $user['senha'] . $this->hash) == $this->senha;
    }
}
