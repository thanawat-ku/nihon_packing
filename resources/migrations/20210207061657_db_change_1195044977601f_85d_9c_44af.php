<?php

use Phinx\Db\Adapter\MysqlAdapter;

class DbChange1195044977601f85d9c44af extends Phinx\Migration\AbstractMigration
{
    public function change()
    {
        $this->table('spare_part_issue_details', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->changeColumn('tool_layout_detail_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'tool_layout_standard_id',
            ])
            ->changeColumn('spare_part_id', 'integer', [
                'null' => false,
                'default' => '0',
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'tool_layout_detail_id',
            ])
            ->addColumn('prepare_remark', 'text', [
                'null' => false,
                'limit' => 65535,
                'collation' => 'utf8mb4_unicode_ci',
                'encoding' => 'utf8mb4',
                'after' => 'request_remark',
            ])
            ->changeColumn('created_at', 'datetime', [
                'null' => false,
                'after' => 'prepare_remark',
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
