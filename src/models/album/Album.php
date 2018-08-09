<?php
namespace Itstructure\MFUploader\models\album;

use yii\db\ActiveQuery;
use yii\helpers\Html;
use Itstructure\MFUploader\Module;
use Itstructure\MFUploader\models\{ActiveRecord, OwnerAlbum, OwnerMediafile, Mediafile};
use Itstructure\MFUploader\interfaces\UploadModelInterface;

/**
 * This is the model class for table "albums".
 *
 * @property int|string $thumbnail Thumbnail field. Corresponds to the file type of thumbnail.
 * In the thml form field should also be called.
 * Can have the values according with the selected type of:
 * FileSetter::INSERTED_DATA_ID
 * FileSetter::INSERTED_DATA_URL
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $type
 * @property int $created_at
 * @property int $updated_at
 *
 * @package Itstructure\MFUploader\models\album
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class Album extends ActiveRecord
{
    const ALBUM_TYPE_IMAGE = UploadModelInterface::FILE_TYPE_IMAGE . 'Album';
    const ALBUM_TYPE_AUDIO = UploadModelInterface::FILE_TYPE_AUDIO . 'Album';
    const ALBUM_TYPE_VIDEO = UploadModelInterface::FILE_TYPE_VIDEO . 'Album';
    const ALBUM_TYPE_APP   = UploadModelInterface::FILE_TYPE_APP . 'Album';
    const ALBUM_TYPE_TEXT  = UploadModelInterface::FILE_TYPE_TEXT . 'Album';
    const ALBUM_TYPE_OTHER = UploadModelInterface::FILE_TYPE_OTHER . 'Album';

    /**
     * Thumbnail field. Corresponds to the file type of thumbnail.
     * In the thml form field should also be called.
     * Can have the values according with the selected type of:
     * FileSetter::INSERTED_DATA_ID
     * FileSetter::INSERTED_DATA_URL
     *
     * @var int|string thumbnail(mediafile id or url).
     */
    public $thumbnail;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'albums';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'title',
                    'type',
                ],
                'required',
            ],
            [
                [
                    'description'
                ],
                'string',
            ],
            [
                [
                    'title',
                    'type',
                ],
                'string',
                'max' => 255,
            ],
            [
                UploadModelInterface::FILE_TYPE_THUMB,
                function($attribute) {
                    if (!is_numeric($this->{$attribute}) && !is_string($this->{$attribute})) {
                        $this->addError($attribute, 'Tumbnail content must be a numeric or string.');
                    }
                },
                'skipOnError' => false,
            ],
            [
                [
                    'created_at',
                    'updated_at',
                ],
                'safe',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('main', 'ID'),
            'title' => Module::t('album', 'Title'),
            'description' => Module::t('album', 'Description'),
            'type' => Module::t('album', 'Type'),
            'created_at' => Module::t('main', 'Created date'),
            'updated_at' => Module::t('main', 'Updated date'),
        ];
    }

    /**
     * Get album types or selected type.
     *
     * @param string|null $key
     *
     * @return mixed
     */
    public static function getAlbumTypes(string $key = null)
    {
        $types = [
            self::ALBUM_TYPE_IMAGE => Module::t('album', 'Image album'),
            self::ALBUM_TYPE_AUDIO => Module::t('album', 'Audio album'),
            self::ALBUM_TYPE_VIDEO => Module::t('album', 'Video album'),
            self::ALBUM_TYPE_APP   => Module::t('album', 'Applications'),
            self::ALBUM_TYPE_TEXT  => Module::t('album', 'Documents'),
            self::ALBUM_TYPE_OTHER => Module::t('album', 'Other files'),
        ];

        if (null !== $key) {
            return array_key_exists($key, $types) ? $types[$key] : [];
        }

        return $types;
    }

    /**
     * Get file type by album type.
     *
     * @param string $albumType
     *
     * @return mixed|null
     */
    public static function getFileType(string $albumType)
    {
        $albumTypes = [
            self::ALBUM_TYPE_IMAGE => UploadModelInterface::FILE_TYPE_IMAGE,
            self::ALBUM_TYPE_AUDIO => UploadModelInterface::FILE_TYPE_AUDIO,
            self::ALBUM_TYPE_VIDEO => UploadModelInterface::FILE_TYPE_VIDEO,
            self::ALBUM_TYPE_APP   => UploadModelInterface::FILE_TYPE_APP,
            self::ALBUM_TYPE_TEXT  => UploadModelInterface::FILE_TYPE_TEXT,
            self::ALBUM_TYPE_OTHER => UploadModelInterface::FILE_TYPE_OTHER,
        ];

        return array_key_exists($albumType, $albumTypes) ? $albumTypes[$albumType] : null;
    }

    /**
     * Search models by file types.
     *
     * @param array $types
     *
     * @return ActiveRecord|array
     */
    public static function findByTypes(array $types): ActiveRecord
    {
        return static::find()->filterWhere(['in', 'type', $types])->all();
    }

    /**
     * Add owner to mediafiles table.
     *
     * @param int    $ownerId
     * @param string $owner
     * @param string $ownerAttribute
     *
     * @return bool
     */
    public function addOwner(int $ownerId, string $owner, string $ownerAttribute): bool
    {
        $ownerAlbum = new OwnerAlbum();
        $ownerAlbum->albumId = $this->id;
        $ownerAlbum->owner = $owner;
        $ownerAlbum->ownerId = $ownerId;
        $ownerAlbum->ownerAttribute = $ownerAttribute;

        return $ownerAlbum->save();
    }

    /**
     * Remove this mediafile owner.
     *
     * @param int    $ownerId
     * @param string $owner
     * @param string $ownerAttribute
     *
     * @return bool
     */
    public static function removeOwner(int $ownerId, string $owner, string $ownerAttribute): bool
    {
        $deleted = OwnerAlbum::deleteAll([
            'ownerId' => $ownerId,
            'owner' => $owner,
            'ownerAttribute' => $ownerAttribute,
        ]);

        return $deleted > 0;
    }

    /**
     * Get album's owners.
     *
     * @return ActiveQuery
     */
    public function getOwners()
    {
        return $this->hasMany(OwnerAlbum::class, ['albumId' => 'id']);
    }

    /**
     * Get album's mediafiles.
     *
     * @param string|null $ownerAttribute
     *
     * @return \Itstructure\MFUploader\models\ActiveRecord[]
     */
    public function getMediaFiles(string $ownerAttribute = null)
    {
        return OwnerMediafile::getMediaFiles($this->type, $this->id, $ownerAttribute);
    }

    /**
     * Get album's mediafiles query.
     *
     * @param string|null $ownerAttribute
     *
     * @return ActiveQuery
     */
    public function getMediaFilesQuery(string $ownerAttribute = null)
    {
        return OwnerMediafile::getMediaFilesQuery([
            'owner' => $this->type,
            'ownerId' => $this->id,
            'ownerAttribute' => $ownerAttribute,
        ]);
    }

    /**
     * Get album thumb image.
     *
     * @param array  $options
     *
     * @return mixed
     */
    public function getDefaultThumbImage(array $options = [])
    {
        /** @var Mediafile $thumbnailModel */
        $thumbnailModel = $this->getThumbnailModel();

        if (null === $thumbnailModel) {
            return null;
        }

        $url = $thumbnailModel->getThumbUrl(Module::THUMB_ALIAS_DEFAULT);

        if (empty($url)) {
            return null;
        }

        if (empty($options['alt'])) {
            $options['alt'] = $thumbnailModel->alt;
        }

        return Html::img($url, $options);
    }

    /**
     * Get album's thumbnail.
     *
     * @return array|null|\yii\db\ActiveRecord|Mediafile
     */
    public function getThumbnailModel()
    {
        if (null === $this->type || null === $this->id) {
            return null;
        }

        return OwnerMediafile::getOwnerThumbnail($this->type, $this->id);
    }
}
