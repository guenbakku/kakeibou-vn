<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class EditTypeOfOrderNoColumn extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('categories');
        $table->changeColumn('order_no', 'integer', ['limit' => MysqlAdapter::INT_TINY])
            ->update()
        ;
    }
}
