<?php

use Phinx\Seed\AbstractSeed;

class SysPerfil extends AbstractSeed
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
        $data = [
            [
                'id' => 1,
                'nome' => 'Administrador',
                'descricao' => 'Acesso completo ao sistema',
                'status' => 1,
                'token_id' => 1,
            ],
            [
                'id' => 2,
                'nome' => 'Usuário',
                'descricao' => 'Acesso limitado ao sistema',
                'status' => 1,
                'token_id' => 1,
            ],
        ];
        $posts = $this->table('sys_perfil');
        $posts->insert($data)
            ->save();
    }
}
