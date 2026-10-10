<?php

use Phinx\Migration\AbstractMigration;

class ChangeTypeOfIdColumn extends AbstractMigration
{
    public function up()
    {
        $foreignKeys = [
            'account_id' => ['table' => 'accounts', 'constraint' => 'inout_records_ibfk_1'],
            'category_id' => ['table' => 'categories', 'constraint' => 'inout_records_ibfk_2'],
        ];
        $table = $this->table('inout_records');
        foreach ($foreignKeys as $column => $foreignKey) {
            if ($table->hasForeignKey($column, $foreignKey['constraint'])) {
                $table->dropForeignKey($column, $foreignKey['constraint'])->update();
            }
        }

        foreach ($foreignKeys as $column => $foreignKey) {
            $table->changeColumn($column, 'integer', ['limit' => 11])
                ->update()
            ;

            $foreignTable = $this->table($foreignKey['table']);
            $foreignTable->changeColumn('id', 'integer', ['limit' => 11, 'identity' => true])
                ->update()
            ;
        }

        foreach ($foreignKeys as $column => $foreignKey) {
            $table->addForeignKey($column, $foreignKey['table'], 'id', [
                'delete' => 'RESTRICT',
                'update' => 'CASCADE',
            ])->update();
        }
    }
}
