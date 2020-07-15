<?php

use yii\db\Migration;

/**
 * Class m180501_161144_create_s3_file_options
 */
class m180501_161144_create_s3_file_options extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('s3_file_options', [
            'mediafileId' => $this->integer()->notNull(),
            'bucket' => $this->string(64)->notNull(),
            'prefix' => $this->string(128)->notNull(),
            'PRIMARY KEY (`mediafileId`, `bucket`, `prefix`)',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('s3_file_options');
    }
}
