<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTypeOfOrderNoColumn extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('categories');
        $table->changeColumn('order_no', 'integer', array('limit' => MysqlAdapter::INT_TINY))
              ->update();
    }
}
