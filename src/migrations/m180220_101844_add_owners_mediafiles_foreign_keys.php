<?php

use yii\db\Migration;

/**
 * Class m180220_101844_add_owners_mediafiles_foreign_keys
 */
class m180220_101844_add_owners_mediafiles_foreign_keys extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'idx-owners_mediafiles-mediafileId',
            'owners_mediafiles',
            'mediafileId'
        );

        $this->addForeignKey(
            'fk-owners_mediafiles-mediafileId',
            'owners_mediafiles',
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
            'fk-owners_mediafiles-mediafileId',
            'owners_mediafiles'
        );

        $this->dropIndex(
            'idx-owners_mediafiles-mediafileId',
            'owners_mediafiles'
        );
    }
}
