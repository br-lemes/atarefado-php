<?php

use Phinx\Seed\AbstractSeed;

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
        $data = [
            [
                'id' => 1,
                'perfil_id' => 1,
                'nome' => 'Administrador',
                'login' => 'admin',
                'senha' => hash('sha512', 'admin' . $hash),
                'hash' => $hash,
                'status' => 1,
                'token_id' => 1,
            ],
            [
                'id' => 2,
                'perfil_id' => 2,
                'nome' => 'Usuário',
                'login' => 'user',
                'senha' => hash('sha512', 'user' . $hash),
                'hash' => $hash,
                'status' => 1,
                'token_id' => 1,
            ],
        ];
        $this->table('sys_usuario')
            ->insert($data)
            ->save();
    }
}
