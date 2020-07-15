<?php

use yii\db\Migration;

/**
 * Handles the creation of table `mediafiles`.
 */
class m180220_081105_create_mediafiles_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('mediafiles', [
            'id' => $this->primaryKey(),
            'filename' => $this->string(128)->notNull(),
            'type' => $this->string(64)->notNull(),
            'url' => $this->text()->notNull(),
            'alt' => $this->string(64),
            'size' => $this->integer()->notNull(),
            'title' => $this->string(64),
            'description' => $this->text(),
            'thumbs' => $this->text(),
            'storage' => $this->string(12)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('mediafiles');
    }
}
