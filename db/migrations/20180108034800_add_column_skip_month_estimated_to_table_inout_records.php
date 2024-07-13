<?php

use Phinx\Migration\AbstractMigration;

class AddColumnSkipMonthEstimatedToTableInoutRecords extends AbstractMigration
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
        $table->addColumn('skip_month_estimated', 'boolean', [
            'after' => 'date',
            'null' => false,
            'default' => false,
        ])
            ->update()
        ;

        $this->fillValue();
    }

    /**
     * Fill value to new added column `skip_month_estimated`.
     * Record which has category type is `skip_month_estimated`
     * will has value `true` in that column.
     */
    protected function fillValue()
    {
        $sql = '
            SELECT `inout_records`.`id`, `categories`.`is_month_fixed_money` 
            FROM `inout_records`
            JOIN `categories` ON `categories`.`id` = `inout_records`.`category_id`
        ';
        $rows = $this->fetchAll($sql);
        foreach ($rows as $row) {
            if (1 == $row['is_month_fixed_money']) {
                $sql = "UPDATE inout_records SET skip_month_estimated=1 WHERE id={$row['id']}";
                $this->execute($sql);
            }
        }
    }
}
