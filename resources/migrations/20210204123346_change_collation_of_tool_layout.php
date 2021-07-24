<?php

use Phinx\Db\Adapter\MysqlAdapter;

class ChangeCollationOfToolLayout extends Phinx\Migration\AbstractMigration
{
    public function change()
    {
        $this->table('spare_part_issues', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->changeColumn('request_user_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'issue_date',
            ])
            ->changeColumn('approve_user_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'request_user_id',
            ])
            ->changeColumn('prepare_user_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'approve_user_id',
            ])
            ->changeColumn('reject_remark', 'text', [
                'null' => false,
                'limit' => 65535,
                'collation' => 'utf8mb4_unicode_ci',
                'encoding' => 'utf8mb4',
                'after' => 'prepare_user_id',
            ])
            ->changeColumn('cancel_remark', 'text', [
                'null' => false,
                'limit' => 65535,
                'collation' => 'utf8mb4_unicode_ci',
                'encoding' => 'utf8mb4',
                'after' => 'reject_remark',
            ])
            ->changeColumn('spare_part_issue_status', 'enum', [
                'null' => false,
                'limit' => 9,
                'values' => ['SAVED', 'CONFIRMED', 'APPROVED', 'REJECTED', 'ISSUED', 'COMPLETED', 'CANCELED'],
                'after' => 'cancel_remark',
            ])
            ->changeColumn('created_at', 'datetime', [
                'null' => false,
                'after' => 'spare_part_issue_status',
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
            ->removeColumn('tool_layout_standard_id')
            ->save();
        $this->table('spare_receive_details', [
                'id' => false,
                'primary_key' => ['id'],
                'engine' => 'InnoDB',
                'encoding' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => '',
                'row_format' => 'DYNAMIC',
            ])
            ->addColumn('spare_part_id', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'spare_receive_id',
            ])
            ->changeColumn('spare_qty', 'integer', [
                'null' => false,
                'limit' => MysqlAdapter::INT_REGULAR,
                'after' => 'spare_part_id',
            ])
            ->changeColumn('thb_price', 'decimal', [
                'null' => false,
                'precision' => '10',
                'scale' => '2',
                'after' => 'spare_qty',
            ])
            ->changeColumn('created_at', 'datetime', [
                'null' => false,
                'after' => 'thb_price',
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
