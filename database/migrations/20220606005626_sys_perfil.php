<?php

declare(strict_types=1);

use \Database\Migration;

final class SysPerfil extends Migration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $this->table('sys_perfil', ['signed' => false])
            ->addColumn('nome', 'string', ['limit' => 50])
            ->addColumn('descricao', 'string', ['limit' => 255])
            ->addColumn('status', 'integer', ['default' => 1])
            ->addColumn('created_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'null' => true,
            ])
            ->addColumn('updated_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'update' => 'CURRENT_TIMESTAMP',
                'null' => true,
            ])
            ->addColumn('token_id', 'integer', ['signed' => false])
            ->create();
    }
}
