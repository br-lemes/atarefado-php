<?php

use Phinx\Seed\AbstractSeed;
use Modulos\System\Data\UsuarioData;

class SysUsuarioSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {
        $hash = time();
        $data = UsuarioData::ALL;
        foreach ($data as &$user) {
            $user['hash'] = $hash;
            $user['senha'] = hash('sha512', $user['login'] . $hash);
            unset($user['perfil_nome']);
            unset($user['perfil_descricao']);
        }
        $this->table('sys_usuario')
            ->insert($data)
            ->save();
    }
}
