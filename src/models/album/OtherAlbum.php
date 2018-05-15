<?php
namespace Itstructure\MFUploader\models\album;

use yii\helpers\ArrayHelper;
use Itstructure\MFUploader\behaviors\BehaviorMediafile;
use Itstructure\MFUploader\interfaces\UploadModelInterface;
use Itstructure\MFUploader\models\{ActiveRecord, OwnersMediafiles};

/**
 * This is the model class for other album.
 *
 * @property array $other Other field. Corresponds to the file type of other.
 * In the thml form field should also be called.
 * Can have the values according with the selected type of:
 * FileSetter::INSERTED_DATA_ID
 * FileSetter::INSERTED_DATA_URL
 *
 * @package Itstructure\MFUploader\models\album
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class OtherAlbum extends Album
{
    /**
     * Other field. Corresponds to the file type of other.
     * In the thml form field should also be called.
     * Can have the values according with the selected type of:
     * FileSetter::INSERTED_DATA_ID
     * FileSetter::INSERTED_DATA_URL
     * @var array other(array of 'mediafile id' or 'mediafile url').
     */
    public $other;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [
                UploadModelInterface::FILE_TYPE_OTHER,
                function($attribute){
                    if (!is_array($this->{$attribute})){
                        $this->addError($attribute, 'Other field content must be an array.');
                    }
                },
                'skipOnError' => false,
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'mediafile' => [
                'class' => BehaviorMediafile::class,
                'name' => self::ALBUM_TYPE_OTHER,
                'attributes' => [
                    UploadModelInterface::FILE_TYPE_THUMB,
                    UploadModelInterface::FILE_TYPE_OTHER,
                ],
            ]
        ]);
    }

    /**
     * Get album's other files.
     * @return ActiveRecord[]
     */
    public function getOtherFiles()
    {
        return OwnersMediafiles::getMediaFiles($this->type, $this->id, static::getFileType(self::ALBUM_TYPE_OTHER));
    }
}
