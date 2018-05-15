<?php

use yii\db\Migration;

/**
 * Class m180220_101910_add_owners_albums_foreign_keys
 */
class m180220_101910_add_owners_albums_foreign_keys extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'idx-owners_albums-albumId',
            'owners_albums',
            'albumId'
        );

        $this->addForeignKey(
            'fk-owners_albums-albumId',
            'owners_albums',
            'albumId',
            'albums',
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
            'fk-owners_albums-albumId',
            'owners_albums'
        );

        $this->dropIndex(
            'idx-owners_albums-albumId',
            'owners_albums'
        );
    }
}
