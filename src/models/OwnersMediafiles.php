<?php

namespace Itstructure\MFUploader\models;

use yii\db\ActiveQuery;
use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;
use Itstructure\MFUploader\interfaces\UploadModelInterface;

/**
 * This is the model class for table "owners_mediafiles".
 *
 * @property int $mediafileId Mediafile id.
 * @property Mediafile $mediafile
 *
 * @package Itstructure\MFUploader\models
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class OwnersMediafiles extends Owners
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'owners_mediafiles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [
                'mediafileId',
                'required',
            ],
            [
                'mediafileId',
                'integer',
            ],
            [
                [
                    'mediafileId',
                    'ownerId',
                    'owner',
                    'ownerAttribute',
                ],
                'unique',
                'targetAttribute' => [
                    'mediafileId',
                    'ownerId',
                    'owner',
                    'ownerAttribute',
                ],
            ],
            [
                ['mediafileId'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Mediafile::class,
                'targetAttribute' => ['mediafileId' => 'id'],
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'mediafileId' => 'Mediafile ID',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaFile()
    {
        return $this->hasOne(Mediafile::class, ['id' => 'mediafileId']);
    }

    /**
     * Get all mediafiles by owner.
     * @param string $owner
     * @param int    $ownerId
     * @param null|string $ownerAttribute
     * @return Mediafile[]
     */
    public static function getMediaFiles(string $owner, int $ownerId, string $ownerAttribute = null)
    {
        return static::getMediaFilesQuery([
            'owner' => $owner,
            'ownerId' => $ownerId,
            'ownerAttribute' => $ownerAttribute,
        ])->all();
    }

    /**
     * Get all mediafiles Query by owner.
     * @param array $args. It can be an array of the next params: owner{string}, ownerId{int}, ownerAttribute{string}.
     * @return ActiveQuery
     */
    public static function getMediaFilesQuery(array $args = []): ActiveQuery
    {
        return Mediafile::find()
            ->where([
                'id' => self::getEntityIdsQuery('mediafileId', $args)->asArray()
            ]);
    }

    /**
     * Get one owner thumbnail file by owner.
     * @param string $owner
     * @param int    $ownerId
     * @return array|null|\yii\db\ActiveRecord|Mediafile
     */
    public static function getOwnerThumbnail(string $owner, int $ownerId)
    {
        $mediafileId = self::getEntityIdsQuery('mediafileId', [
            'owner' => $owner,
            'ownerId' => $ownerId,
            'ownerAttribute' => UploadModelInterface::FILE_TYPE_THUMB,
        ])->one();

        if (null === $mediafileId){
            return null;
        }

        return Mediafile::find()
            ->where([
                'id' =>  $mediafileId->mediafileId
            ])->one();
    }

    /**
     * Get image files by owner.
     * @param string $owner
     * @param int    $ownerId
     * @return Mediafile[]
     */
    public static function getImageFiles(string $owner, int $ownerId)
    {
        return static::getMediaFiles($owner, $ownerId, UploadModelInterface::FILE_TYPE_IMAGE);
    }

    /**
     * Get audio files by owner.
     * @param string $owner
     * @param int    $ownerId
     * @return Mediafile[]
     */
    public static function getAudioFiles(string $owner, int $ownerId)
    {
        return static::getMediaFiles($owner, $ownerId, UploadModelInterface::FILE_TYPE_AUDIO);
    }

    /**
     * Get video files by owner.
     * @param string $owner
     * @param int    $ownerId
     * @return Mediafile[]
     */
    public static function getVideoFiles(string $owner, int $ownerId)
    {
        return static::getMediaFiles($owner, $ownerId, UploadModelInterface::FILE_TYPE_VIDEO);
    }

    /**
     * Get app files by owner.
     * @param string $owner
     * @param int    $ownerId
     * @return Mediafile[]
     */
    public static function getAppFiles(string $owner, int $ownerId)
    {
        return static::getMediaFiles($owner, $ownerId, UploadModelInterface::FILE_TYPE_APP);
    }

    /**
     * Get text files by owner.
     * @param string $owner
     * @param int    $ownerId
     * @return Mediafile[]
     */
    public static function getTextFiles(string $owner, int $ownerId)
    {
        return static::getMediaFiles($owner, $ownerId, UploadModelInterface::FILE_TYPE_TEXT);
    }

    /**
     * Get other files by owner.
     * @param string $owner
     * @param int    $ownerId
     * @return Mediafile[]
     */
    public static function getOtherFiles(string $owner, int $ownerId)
    {
        return static::getMediaFiles($owner, $ownerId, UploadModelInterface::FILE_TYPE_OTHER);
    }
}
