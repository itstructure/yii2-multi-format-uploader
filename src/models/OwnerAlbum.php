<?php

namespace Itstructure\MFUploader\models;

use yii\db\ActiveQuery;
use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;
use Itstructure\MFUploader\models\album\Album;

/**
 * This is the model class for table "owners_albums".
 *
 * @property int $albumId Album id.
 * @property Album $album
 *
 * @package Itstructure\MFUploader\models
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class OwnerAlbum extends Owner
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'owners_albums';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [
                'albumId',
                'required',
            ],
            [
                'albumId',
                'integer',
            ],
            [
                [
                    'albumId',
                    'ownerId',
                    'owner',
                    'ownerAttribute',
                ],
                'unique',
                'targetAttribute' => [
                    'albumId',
                    'ownerId',
                    'owner',
                    'ownerAttribute',
                ],
            ],
            [
                ['albumId'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Album::class,
                'targetAttribute' => ['albumId' => 'id'],
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'albumId' => 'Album ID',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlbum()
    {
        return $this->hasOne(Album::class, ['id' => 'albumId']);
    }

    /**
     * Get all albums by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     * @param string $ownerAttribute
     *
     * @return Album[]
     */
    public static function getAlbums(string $owner, int $ownerId, string $ownerAttribute = null)
    {
        return static::getAlbumsQuery(static::buildFilterOptions($ownerId, $owner, $ownerAttribute))->all();
    }

    /**
     * Get all albums Query by owner.
     *
     * @param array $args. It can be an array of the next params: owner{string}, ownerId{int}, ownerAttribute{string}.
     *
     * @return ActiveQuery
     */
    public static function getAlbumsQuery(array $args = []): ActiveQuery
    {
        return Album::find()
            ->where([
                'id' => self::getEntityIdsQuery('albumId', $args)->asArray()
            ]);
    }

    /**
     * Get image albums by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     *
     * @return Album[]
     */
    public static function getImageAlbums(string $owner, int $ownerId)
    {
        return static::getAlbums($owner, $ownerId, Album::ALBUM_TYPE_IMAGE);
    }

    /**
     * Get audio albums by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     *
     * @return Album[]
     */
    public static function getAudioAlbums(string $owner, int $ownerId)
    {
        return static::getAlbums($owner, $ownerId, Album::ALBUM_TYPE_AUDIO);
    }

    /**
     * Get video albums by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     *
     * @return Album[]
     */
    public static function getVideoAlbums(string $owner, int $ownerId)
    {
        return static::getAlbums($owner, $ownerId, Album::ALBUM_TYPE_VIDEO);
    }

    /**
     * Get application albums by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     *
     * @return Album[]
     */
    public static function getAppAlbums(string $owner, int $ownerId)
    {
        return static::getAlbums($owner, $ownerId, Album::ALBUM_TYPE_APP);
    }

    /**
     * Get text albums by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     *
     * @return Album[]
     */
    public static function getTextAlbums(string $owner, int $ownerId)
    {
        return static::getAlbums($owner, $ownerId, Album::ALBUM_TYPE_TEXT);
    }

    /**
     * Get other albums by owner.
     *
     * @param string $owner
     * @param int    $ownerId
     *
     * @return Album[]
     */
    public static function getOtherAlbums(string $owner, int $ownerId)
    {
        return static::getAlbums($owner, $ownerId, Album::ALBUM_TYPE_OTHER);
    }

    /**
     * Get model album primary key name.
     *
     * @return string
     */
    protected static function getModelKeyName(): string
    {
        return 'albumId';
    }
}
