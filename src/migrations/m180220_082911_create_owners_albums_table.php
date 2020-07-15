<?php

use yii\db\Migration;

/**
 * Handles the creation of table `owners_albums`.
 */
class m180220_082911_create_owners_albums_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('owners_albums', [
            'albumId' => $this->integer()->notNull(),
            'ownerId' => $this->integer()->notNull(),
            'owner' => $this->string(64)->notNull(),
            'ownerAttribute' => $this->string(64)->notNull(),
            'PRIMARY KEY (`albumId`, `ownerId`, `owner`, `ownerAttribute`)',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('owners_albums');
    }
}
