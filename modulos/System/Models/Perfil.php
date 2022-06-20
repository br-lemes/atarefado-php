<?php

namespace Modulos\System\Models;

use App\Models\BaseModel;

class Perfil extends BaseModel
{
    protected $table = 'sys_perfil';
    protected $fillable = ['nome', 'descricao', 'status'];
}
