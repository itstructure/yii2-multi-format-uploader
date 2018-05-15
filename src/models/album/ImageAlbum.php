<?php
namespace Itstructure\MFUploader\models\album;

use yii\helpers\ArrayHelper;
use Itstructure\MFUploader\behaviors\BehaviorMediafile;
use Itstructure\MFUploader\interfaces\UploadModelInterface;
use Itstructure\MFUploader\models\{ActiveRecord, OwnersMediafiles};

/**
 * This is the model class for image album.
 *
 * @property array $image Image field. Corresponds to the file type of image.
 * In the thml form field should also be called.
 * Can have the values according with the selected type of:
 * FileSetter::INSERTED_DATA_ID
 * FileSetter::INSERTED_DATA_URL
 *
 * @package Itstructure\MFUploader\models\album
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class ImageAlbum extends Album
{
    /**
     * Image field. Corresponds to the file type of image.
     * In the thml form field should also be called.
     * Can have the values according with the selected type of:
     * FileSetter::INSERTED_DATA_ID
     * FileSetter::INSERTED_DATA_URL
     * @var array image(array of 'mediafile id' or 'mediafile url').
     */
    public $image;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [
                UploadModelInterface::FILE_TYPE_IMAGE,
                function($attribute){
                    if (!is_array($this->{$attribute})){
                        $this->addError($attribute, 'Image field content must be an array.');
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
                'name' => self::ALBUM_TYPE_IMAGE,
                'attributes' => [
                    UploadModelInterface::FILE_TYPE_THUMB,
                    UploadModelInterface::FILE_TYPE_IMAGE,
                ],
            ]
        ]);
    }

    /**
     * Get album's images.
     * @return ActiveRecord[]
     */
    public function getImageFiles()
    {
        return OwnersMediafiles::getMediaFiles($this->type, $this->id, static::getFileType(self::ALBUM_TYPE_IMAGE));
    }
}
