<?php

namespace Itstructure\MFUploader\behaviors;

use yii\db\ActiveRecordInterface;
use Itstructure\MFUploader\models\OwnerAlbum;
use Itstructure\MFUploader\models\album\Album;

/**
 * Class BehaviorAlbum
 * BehaviorAlbum class to add, update and remove owners of Album model.
 *
 * @package Itstructure\MFUploader\behaviors
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class BehaviorAlbum extends Behavior
{
    /**
     * Load Album model by conditions.
     *
     * @param array $conditions
     *
     * @return Album|ActiveRecordInterface|null
     */
    protected function loadModel(array $conditions)
    {
        return Album::findOne($conditions);
    }

    /**
     * Remove owner of Album model.
     *
     * @param int    $ownerId
     * @param string $owner
     * @param string $ownerAttribute
     *
     * @return bool
     */
    protected function removeOwner(int $ownerId, string $owner, string $ownerAttribute): bool
    {
        return OwnerAlbum::removeOwner($ownerId, $owner, $ownerAttribute);
    }
}
