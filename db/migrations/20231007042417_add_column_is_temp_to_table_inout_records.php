<?php

use Phinx\Migration\AbstractMigration;

class AddColumnIsTempToTableInoutRecords extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('inout_records');
        $table->addColumn('is_temp', 'boolean', [
            'after' => 'skip_month_estimated',
            'null' => false,
            'default' => false,
        ])
        ->update();
        $table->addIndex(['is_temp'])->save();
    }
}
