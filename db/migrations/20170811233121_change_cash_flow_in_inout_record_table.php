<?php

use Phinx\Migration\AbstractMigration;

class ChangeCashFlowInInoutRecordTable extends AbstractMigration
{
    public function up()
    {
        $sql = "UPDATE inout_records 
                SET `cash_flow` = 'internal' 
                WHERE `category_id` IN (1,2,3,4,5,6)";
        $this->execute($sql);
    }
}
