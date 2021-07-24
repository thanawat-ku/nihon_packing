<?php

use Phinx\Db\Adapter\MysqlAdapter;

class ChangeOnModelTable extends Phinx\Migration\AbstractMigration
{
    public function change()
    {
        $this->table('models', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('model_name', 'string', [
                'null' => false,
                'limit' => 100,
                'collation' => 'utf8mb4_unicode_ci',
                'encoding' => 'utf8mb4',
                'after' => 'id',
            ])
            ->removeColumn('model')
            ->save();
    }
}
