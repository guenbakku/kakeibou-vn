<?php

use Phinx\Migration\AbstractMigration;

class AddRetrictDeleteFlagToCashAccount extends AbstractMigration
{
    public function up()
    {
        $sql = 'UPDATE accounts SET `restrict_delete`=1 WHERE `id`=1';
        $this->execute($sql);
    }
}
