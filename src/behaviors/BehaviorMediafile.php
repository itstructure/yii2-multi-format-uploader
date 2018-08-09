<?php

namespace Itstructure\MFUploader\behaviors;

use yii\db\ActiveRecordInterface;
use Itstructure\MFUploader\models\Mediafile;

/**
 * Class BehaviorMediafile
 * BehaviorMediafile class to add, update and remove owners of Mediafile model.
 *
 * @package Itstructure\MFUploader\behaviors
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class BehaviorMediafile extends Behavior
{
    /**
     * Load Mediafile model by conditions.
     *
     * @param array $conditions
     *
     * @return Mediafile|ActiveRecordInterface|null
     */
    protected function loadModel(array $conditions)
    {
        return Mediafile::findOne($conditions);
    }

    /**
     * Remove owner of Mediafile model.
     *
     * @param int    $ownerId
     * @param string $owner
     * @param string $ownerAttribute
     *
     * @return bool
     */
    protected function removeOwner(int $ownerId, string $owner, string $ownerAttribute): bool
    {
        return Mediafile::removeOwner($ownerId, $owner, $ownerAttribute);
    }
}
