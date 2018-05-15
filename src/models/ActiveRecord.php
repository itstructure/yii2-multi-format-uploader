<?php

namespace Itstructure\MFUploader\models;

use yii\behaviors\TimestampBehavior;

/**
 * Class ActiveRecord
 *
 * @package Itstructure\MFUploader\models
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * Connect behavior to the basic model.
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class
        ];
    }
}
