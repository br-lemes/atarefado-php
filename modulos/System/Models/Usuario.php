<?php

namespace Modulos\System\Models;

use App\Models\BaseModel;

class Usuario extends BaseModel
{
    protected $table = 'sys_usuario';
    protected $fillable = ['perfil_id', 'nome', 'email', 'login', 'status'];
    protected $hidden = ['senha', 'hash'];

    public function checkPassword($user)
    {
        return hash('sha512', $user['senha'] . $this->hash) == $this->senha;
    }
}
