<?php

use Phinx\Db\Adapter\MysqlAdapter;

class ChangeCollationOfStatus extends Phinx\Migration\AbstractMigration
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
            ->changeColumn('spare_part_issue_status', 'enum', [
                'null' => false,
                'limit' => 9,
                'values' => ['SAVED', 'CONFIRMED', 'APPROVED', 'REJECTED', 'ISSUED', 'COMPLETED', 'CANCELED'],
                'after' => 'cancel_remark',
            ])
            ->save();
    }
}
