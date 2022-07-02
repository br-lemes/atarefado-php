<?php

use Modulos\System\Data\PerfilData;
use Phinx\Seed\AbstractSeed;

class SysPerfilSeed extends AbstractSeed
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
        $data = PerfilData::ALL;
        $posts = $this->table('sys_perfil');
        $posts->insert($data)
            ->save();
    }
}
