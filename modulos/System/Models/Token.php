<?php

namespace Modulos\System\Models;

use App\Models\BaseModel;

class Token extends BaseModel
{
    protected $table = 'sys_token';
    protected $fillable = ['usuario_id', 'token', 'ip', 'browser', 'token_exp', 'logout_date'];
}
