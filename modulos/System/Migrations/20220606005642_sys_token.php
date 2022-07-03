<?php

declare(strict_types=1);

use App\Database\Migration;

final class SysToken extends Migration
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
        $this->table('sys_token', ['signed' => false])
            ->addColumn('usuario_id', 'integer', ['signed' => false])
            ->addColumn('token_access', 'string', ['limit' => 500, 'null' => true])
            ->addColumn('token_refresh', 'string', ['limit' => 500, 'null' => true])
            ->addColumn('ip', 'string', ['limit' => 45, 'null' => true])
            ->addColumn('browser', 'string', ['limit' => 200, 'null' => true])
            ->addColumn('created_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'null' => true,
            ])
            ->addColumn('updated_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'update' => 'CURRENT_TIMESTAMP',
                'null' => true,
            ])
            ->addColumn('token_exp', 'datetime', ['null' => true])
            ->addColumn('logout_date', 'datetime', ['null' => true])
            ->addColumn('logout_user', 'integer', ['signed' => false, 'null' => true])
            ->addIndex('usuario_id')
            ->create();
    }
}
