<?php

use Phinx\Db\Adapter\MysqlAdapter;

class ChangeOnBrandTable extends Phinx\Migration\AbstractMigration
{
    public function change()
    {
        $this->table('brands', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('brand_name', 'string', [
                'null' => false,
                'limit' => 100,
                'collation' => 'utf8mb4_unicode_ci',
                'encoding' => 'utf8mb4',
                'after' => 'id',
            ])
            ->changeColumn('created_at', 'datetime', [
                'null' => false,
                'after' => 'brand_name',
            ])
            ->changeColumn('updated_at', 'datetime', [
                'null' => false,
                'after' => 'created_user_id',
            ])
            ->removeColumn('brande_name')
            ->save();
        $this->table('machines', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('model_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'machine_name',
            ])
            ->changeColumn('created_at', 'datetime', [
                'null' => false,
                'after' => 'model_id',
            ])
            ->changeColumn('created_user_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'created_at',
            ])
            ->changeColumn('updated_at', 'datetime', [
                'null' => false,
                'after' => 'created_user_id',
            ])
            ->changeColumn('updated_user_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'updated_at',
            ])
            ->save();
    }
}
