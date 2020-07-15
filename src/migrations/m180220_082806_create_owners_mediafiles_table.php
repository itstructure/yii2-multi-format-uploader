<?php

use yii\db\Migration;

/**
 * Handles the creation of table `owners_mediafiles`.
 */
class m180220_082806_create_owners_mediafiles_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('owners_mediafiles', [
            'mediafileId' => $this->integer()->notNull(),
            'ownerId' => $this->integer()->notNull(),
            'owner' => $this->string(64)->notNull(),
            'ownerAttribute' => $this->string(64)->notNull(),
            'PRIMARY KEY (`mediafileId`, `ownerId`, `owner`, `ownerAttribute`)',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('owners_mediafiles');
    }
}
