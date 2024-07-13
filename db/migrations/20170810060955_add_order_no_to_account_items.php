<?php

use Phinx\Migration\AbstractMigration;

class AddOrderNoToAccountItems extends AbstractMigration
{
    public function up()
    {
        $sql = 'SELECT * FROM accounts';
        $rows = $this->fetchAll($sql);

        foreach ($rows as $i => $row) {
            $sql = "UPDATE accounts SET `order_no`={$i} WHERE `id`={$row['id']}";
            $this->execute($sql);
        }
    }
}
