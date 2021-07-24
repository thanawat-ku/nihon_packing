<?php

use Phinx\Db\Adapter\MysqlAdapter;

class ChangeDB extends Phinx\Migration\AbstractMigration
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
            ->addColumn('created_at', 'datetime', [
                'null' => false,
                'after' => 'model_code',
            ])
            ->removeColumn('cerated_at')
            ->save();
    }
}
