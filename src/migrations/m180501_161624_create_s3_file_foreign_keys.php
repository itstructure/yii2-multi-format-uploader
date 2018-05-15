<?php

use yii\db\Migration;

/**
 * Class m180501_161624_create_s3_file_foreign_keys
 */
class m180501_161624_create_s3_file_foreign_keys extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'idx-s3_file_options-mediafileId',
            's3_file_options',
            'mediafileId'
        );

        $this->addForeignKey(
            'fk-s3_file_options-mediafileId',
            's3_file_options',
            'mediafileId',
            'mediafiles',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-s3_file_options-mediafileId',
            's3_file_options'
        );

        $this->dropIndex(
            'idx-s3_file_options-mediafileId',
            's3_file_options'
        );
    }
}
