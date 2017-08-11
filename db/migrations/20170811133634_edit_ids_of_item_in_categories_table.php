<?php

use Phinx\Migration\AbstractMigration;

class EditIdsOfItemInCategoriesTable extends AbstractMigration
{
    public function up()
    {
        $sqls = [
            "UPDATE categories SET `id` = 999 WHERE `id` = 3",
            "UPDATE categories SET `id` = 3 WHERE `id` = 4",
            "UPDATE categories SET `id` = 4 WHERE `id` = 999",
        ];
        
        foreach ($sqls as $sql) {
            $this->execute($sql);
        }
    }
}
