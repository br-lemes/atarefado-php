<?php

namespace App\Database;

use Illuminate\Database\Capsule\Manager as Capsule;
use Phinx\Migration\AbstractMigration;

class Migration extends AbstractMigration
{
    /** @var \Illuminate\Database\Capsule\Manager $capsule */
    public $capsule;
    /** @var \Illuminate\Database\Schema\Builder $capsule */
    public $schema;

    public function init()
    {
        $config = (require __DIR__ . '/../app/settings.php')['settings'];

        $this->capsule = new Capsule();
        $this->capsule->addConnection([
            'driver' => $config['db']['driver'],
            'database' => $config['db']['database'],
        ]);

        $this->capsule->bootEloquent();
        $this->capsule->setAsGlobal();
        $this->schema = $this->capsule->schema();
    }
}
