<?php

declare(strict_types=1);

use \Database\Migration;

final class SysUsuario extends Migration
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
        $this->table('sys_usuario', ['signed' => false])
            ->addColumn('perfil_id', 'integer', ['signed' => false])
            ->addColumn('nome', 'string', ['limit' => 50, 'null' => true])
            ->addColumn('email', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('login', 'string', ['limit' => 255])
            ->addColumn('senha', 'string', ['limit' => 255])
            ->addColumn('hash', 'string', ['limit' => 32])
            ->addColumn('status', 'integer', ['default' => 0])
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
            ->addIndex('perfil_id')
            ->addIndex('login', ['unique' => true])
            ->create();
    }
}
