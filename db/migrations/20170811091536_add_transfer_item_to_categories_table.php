<?php

use Phinx\Migration\AbstractMigration;

class AddTransferItemToCategoriesTable extends AbstractMigration
{
    public function up()
    {
        $rows = [
            [
                'id' => 7,
                'name' => 'Chuyển khoản*',
                'inout_type_id' => 2,
                'order_no' => 4,
                'restrict_delete' => 1,
            ],
            [
                'id' => 8,
                'name' => 'Chuyển khoản*',
                'inout_type_id' => 1,
                'order_no' => 4,
                'restrict_delete' => 1,
            ],
        ];
        
        $this->insert('categories', $rows);
    }
}
