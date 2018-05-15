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
            'filename' => $this->string()->notNull(),
            'type' => $this->string()->notNull(),
            'url' => $this->string()->notNull(),
            'alt' => $this->text(),
            'size' => $this->integer()->notNull(),
            'title' => $this->string(),
            'description' => $this->text(),
            'thumbs' => $this->text(),
            'storage' => $this->string()->notNull(),
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
