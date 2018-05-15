<?php
namespace Itstructure\MFUploader\models\album;

use yii\helpers\ArrayHelper;
use Itstructure\MFUploader\behaviors\BehaviorMediafile;
use Itstructure\MFUploader\interfaces\UploadModelInterface;
use Itstructure\MFUploader\models\{ActiveRecord, OwnersMediafiles};

/**
 * This is the model class for audio album.
 *
 * @property array $audio Audio field. Corresponds to the file type of audio.
 * In the thml form field should also be called.
 * Can have the values according with the selected type of:
 * FileSetter::INSERTED_DATA_ID
 * FileSetter::INSERTED_DATA_URL
 *
 * @package Itstructure\MFUploader\models\album
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class AudioAlbum extends Album
{
    /**
     * Audio field. Corresponds to the file type of audio.
     * In the thml form field should also be called.
     * Can have the values according with the selected type of:
     * FileSetter::INSERTED_DATA_ID
     * FileSetter::INSERTED_DATA_URL
     * @var array audio(array of 'mediafile id' or 'mediafile url').
     */
    public $audio;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [
                UploadModelInterface::FILE_TYPE_AUDIO,
                function($attribute){
                    if (!is_array($this->{$attribute})){
                        $this->addError($attribute, 'Audio field content must be an array.');
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
                'name' => self::ALBUM_TYPE_AUDIO,
                'attributes' => [
                    UploadModelInterface::FILE_TYPE_THUMB,
                    UploadModelInterface::FILE_TYPE_AUDIO,
                ],
            ]
        ]);
    }

    /**
     * Get album's audio.
     * @return ActiveRecord[]
     */
    public function getAudioFiles()
    {
        return OwnersMediafiles::getMediaFiles($this->type, $this->id, static::getFileType(self::ALBUM_TYPE_AUDIO));
    }
}
