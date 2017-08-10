<?php

use Phinx\Migration\AbstractMigration;

class ChangeTypeOfIdColumn extends AbstractMigration
{
    public function up()
    {   
        $foreignKeys = ['account_id', 'category_id'];
        $foreignTables = ['accounts', 'categories'];
        $table = $this->table('inout_records');
        foreach ($foreignKeys as $i => $foreignKey) {
            // Delete foreign key relation
            if ($table->hasForeignKey($foreignKey)) {
                $table->dropForeignKey($foreignKey);
            }
            
            // Change column type
            $table->changeColumn($foreignKey, 'integer', ['limit' => 11])
                  ->update();
            
            $foreignTable = $this->table($foreignTables[$i]);
            $foreignTable->changeColumn('id', 'integer', ['limit' => 11, 'identity' => true])
                         ->update();
            
            // Recreate foreign key relation
            $table->addForeignKey($foreignKey, $foreignTables[$i], 'id', array(
                'delete' => 'RESTRICT', 
                'update' => 'CASCADE',
            ))->update();
        }
    }
}
