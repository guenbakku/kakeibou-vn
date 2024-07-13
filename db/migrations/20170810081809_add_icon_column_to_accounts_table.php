<?php

use Phinx\Migration\AbstractMigration;

class AddIconColumnToAccountsTable extends AbstractMigration
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
        // Add column
        $table = $this->table('accounts');
        $table->addColumn('icon', 'string', ['limit' => 50, 'after' => 'order_no'])
            ->update()
        ;

        // Fill data
        $sql = 'SELECT * FROM accounts';
        $rows = $this->fetchAll($sql);
        foreach ($rows as $row) {
            $icon = $row['restrict_delete'] == 1 ? 'fa-money' : 'fa-bank';
            $id = $row['id'];
            $sql = sprintf("UPDATE accounts SET `icon` = '%s' WHERE `id` = %s", $icon, $id);
            $this->execute($sql);
        }
    }
}
