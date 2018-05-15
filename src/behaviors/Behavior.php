<?php

namespace Itstructure\MFUploader\behaviors;

use yii\db\{ActiveRecord, ActiveRecordInterface};
use yii\base\Behavior as BaseBehavior;
use Itstructure\MFUploader\models\album\Album;
use Itstructure\MFUploader\models\Mediafile;

/**
 * Class Behavior
 * Base Behavior class to add, update and remove owners of media model.
 *
 * @property string $name Owner name.
 * @property array $attributes Owner attribute names.
 * @property string $findModelKey Key, which is used to find model record.
 *
 * @package Itstructure\MFUploader\behaviors
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
abstract class Behavior extends BaseBehavior
{
    /**
     * Owner name.
     * @var string
     */
    public $name = '';

    /**
     * Owner attribute names.
     * @var array
     */
    public $attributes = [];

    /**
     * Key, which is used to find model record.
     * @var string
     */
    public $findModelKey = 'id';

    /**
     * Load media model by conditions.
     * @param array $conditions
     * @return Mediafile|Album|ActiveRecordInterface|null
     */
    abstract protected function loadModel(array $conditions);

    /**
     * Remove media model owner.
     * @param int    $ownerId
     * @param string $owner
     * @param string $ownerAttribute
     * @return bool
     */
    abstract protected function removeOwner(int $ownerId, string $owner, string $ownerAttribute):bool;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'addOwners',
            ActiveRecord::EVENT_AFTER_UPDATE => 'updateOwners',
            ActiveRecord::EVENT_BEFORE_DELETE => 'deleteOwners',
        ];
    }

    /**
     * Add owners to media model
     * @return void
     */
    public function addOwners(): void
    {
        foreach ($this->attributes as $attributeName) {
            $this->linkModelWithOwner($attributeName, $this->owner->{$attributeName});
        }
    }

    /**
     * Update owners of media model
     * @return void
     */
    public function updateOwners(): void
    {
        foreach ($this->attributes as $attributeName) {
            $this->removeOwner($this->owner->primaryKey, $this->name, $attributeName);
            $this->linkModelWithOwner($attributeName, $this->owner->{$attributeName});
        }
    }

    /**
     * Delete owners of media model
     * @return void
     */
    public function deleteOwners(): void
    {
        foreach ($this->attributes as $attributeName) {
            $this->removeOwner($this->owner->primaryKey, $this->name, $attributeName);
        }
    }

    /**
     * Link media model with owner.
     * @param $attributeName
     * @param $attributeValue
     * @return void
     */
    protected function linkModelWithOwner($attributeName, $attributeValue): void
    {
        if (is_array($attributeValue)){
            foreach ($attributeValue as $item) {
                if (empty($item)){
                    continue;
                }
                $this->linkModelWithOwner($attributeName, $item);
            }

        } else {
            if (!empty($attributeValue) && $model = $this->loadModel([$this->findModelKey => $attributeValue])) {
                $model->addOwner($this->owner->primaryKey, $this->name, $attributeName);
            }
        }
    }
}
