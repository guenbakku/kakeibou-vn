<?php

use Phinx\Migration\AbstractMigration;

class DropSettingsTable extends AbstractMigration
{
    public function up()
    {
        $this->dropTable('settings');
    }
    
    public function down()
    {
        $table = $this->table('settings', ['id' => false, 'primary_key' => 'item']);
        $table->addColumn('item', 'string', ['limit' => 128])
              ->addColumn('name', 'string', ['limit' => 128])
              ->addColumn('value', 'text')
              ->create();
    }
}
