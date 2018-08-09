<?php

namespace Itstructure\MFUploader\models\upload;

use yii\imagine\Image;
use yii\db\ActiveRecord;
use yii\base\{InvalidConfigException, InvalidValueException};
use yii\helpers\{ArrayHelper, Inflector};
use yii\web\UploadedFile;
use Aws\S3\{S3ClientInterface, S3MultiRegionClient};
use Itstructure\MFUploader\models\S3FileOptions;
use Itstructure\MFUploader\Module;
use Itstructure\MFUploader\models\Mediafile;
use Itstructure\MFUploader\interfaces\{ThumbConfigInterface, UploadModelInterface};

/**
 * Class S3Upload
 *
 * @property string $s3DefaultBucket Amazon web services S3 default bucket for upload files (not for delete).
 * @property array $s3Buckets Buckets for upload depending on the owner.
 * @property S3MultiRegionClient|S3ClientInterface $s3Client Amazon web services SDK S3 client.
 * @property string $originalContent Binary contente of the original file.
 * @property array $objectsForDelete Objects for delete (files in the S3 directory).
 * @property string $bucketForDelete Bucket, in which the located files will be deleted.
 * @property string $bucketForUpload Bucket for upload new files.
 * @property ActiveRecord|S3FileOptions $s3FileOptions S3 file options (bucket, prefix).
 * @property Mediafile $mediafileModel Mediafile model to save files data.
 * @property UploadedFile $file File object.
 *
 * @package Itstructure\MFUploader\models
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class S3Upload extends BaseUpload implements UploadModelInterface
{
    const DIR_LENGTH_FIRST = 2;
    const DIR_LENGTH_SECOND = 4;

    const BUCKET_DIR_SEPARATOR = '/';

    /**
     * Amazon web services S3 default bucket for upload files (not for delete).
     *
     * @var string
     */
    public $s3DefaultBucket;

    /**
     * Buckets for upload depending on the owner.
     *
     * @var array
     */
    public $s3Buckets = [];

    /**
     * Amazon web services SDK S3 client.
     *
     * @var S3ClientInterface|S3MultiRegionClient
     */
    private $s3Client;

    /**
     * Binary contente of the original file.
     *
     * @var string
     */
    private $originalContent;

    /**
     * Objects for delete (files in the S3 directory).
     *
     * @var array
     */
    private $objectsForDelete = [];

    /**
     * Bucket, in which the located files will be deleted.
     *
     * @var string
     */
    private $bucketForDelete;

    /**
     * Bucket for upload new files.
     *
     * @var string
     */
    private $bucketForUpload;

    /**
     * S3 file options (bucket, prefix).
     *
     * @var ActiveRecord|S3FileOptions
     */
    private $s3FileOptions;

    /**
     * Initialize.
     */
    public function init()
    {
        if (null === $this->s3Client) {
            throw new InvalidConfigException('S3 client is not defined correctly.');
        }
    }

    /**
     * Set s3 client.
     *
     * @param S3ClientInterface $s3Client
     */
    public function setS3Client(S3ClientInterface $s3Client): void
    {
        $this->s3Client = $s3Client;
    }

    /**
     * Get s3 client.
     *
     * @return S3ClientInterface|null
     */
    public function getS3Client()
    {
        return $this->s3Client;
    }

    /**
     * Get storage type - aws.
     *
     * @return string
     */
    protected function getStorageType(): string
    {
        return Module::STORAGE_TYPE_S3;
    }

    /**
     * Set some params for upload.
     * It is needed to set the next parameters:
     * $this->uploadDir
     * $this->outFileName
     * $this->bucketForUpload
     *
     * @throws InvalidConfigException
     *
     * @return void
     */
    protected function setParamsForSend(): void
    {
        $uploadDir = $this->getUploadDirConfig($this->file->type);
        $uploadDir = trim(str_replace('\\', self::BUCKET_DIR_SEPARATOR, $uploadDir), self::BUCKET_DIR_SEPARATOR);

        if (!empty($this->subDir)) {
            $uploadDir = $uploadDir .
                self::BUCKET_DIR_SEPARATOR .
                trim(str_replace('\\', self::BUCKET_DIR_SEPARATOR, $this->subDir), self::BUCKET_DIR_SEPARATOR);
        }

        $this->uploadDir = $uploadDir .
            self::BUCKET_DIR_SEPARATOR . substr(md5(time()), 0, self::DIR_LENGTH_FIRST) .
            self::BUCKET_DIR_SEPARATOR . substr(md5(time()+1), 0, self::DIR_LENGTH_SECOND);

        $this->outFileName = $this->renameFiles ?
            md5(time()+2).'.'.$this->file->extension :
            Inflector::slug($this->file->baseName).'.'. $this->file->extension;

        $this->bucketForUpload = null !== $this->owner && isset($this->s3Buckets[$this->owner]) ?
            $this->s3Buckets[$this->owner] : $this->s3DefaultBucket;
    }

    /**
     * Set some params for delete.
     * It is needed to set the next parameters:
     * $this->objectsForDelete
     * $this->bucketForDelete
     *
     * @return void
     */
    protected function setParamsForDelete(): void
    {
        $s3fileOptions = $this->getS3FileOptions();
        
        $objects = $this->s3Client->listObjects([
            'Bucket' => $s3fileOptions->bucket,
            'Prefix' => $s3fileOptions->prefix
        ]);

        $this->objectsForDelete = null === $objects['Contents'] ? [] : array_map(function ($item) {
            return [
                'Key' => $item
            ];
        }, ArrayHelper::getColumn($objects['Contents'], 'Key'));

        $this->bucketForDelete = $s3fileOptions->bucket;
    }

    /**
     * Send file to remote storage.
     *
     * @throws InvalidConfigException
     *
     * @return bool
     */
    protected function sendFile(): bool
    {
        if (null === $this->bucketForUpload || !is_string($this->bucketForUpload)) {
            throw new InvalidConfigException('S3 bucket for upload is not defined correctly.');
        }

        $result = $this->s3Client->putObject([
            'ACL' => 'public-read',
            'SourceFile' => $this->file->tempName,
            'Key' => $this->uploadDir . self::BUCKET_DIR_SEPARATOR . $this->outFileName,
            'Bucket' => $this->bucketForUpload
        ]);

        if ($result['ObjectURL']) {
            $this->databaseUrl = $result['ObjectURL'];
            return true;
        }

        return false;
    }

    /**
     * Delete storage directory with original file and thumbs.
     *
     * @return void
     */
    protected function deleteFiles(): void
    {
        if (count($this->objectsForDelete) > 0) {
            $this->s3Client->deleteObjects([
                'Bucket' => $this->bucketForDelete,
                'Delete' => [
                    'Objects' => $this->objectsForDelete,
                ]
            ]);
        }
    }

    /**
     * Create thumb.
     *
     * @param ThumbConfigInterface $thumbConfig
     *
     * @return mixed
     */
    protected function createThumb(ThumbConfigInterface $thumbConfig)
    {
        $originalFile = pathinfo($this->mediafileModel->url);

        if (null === $this->s3FileOptions) {
            $this->s3FileOptions = $this->getS3FileOptions();
        }

        $uploadThumbUrl = $this->s3FileOptions->prefix .
                    self::BUCKET_DIR_SEPARATOR .
                    $this->getThumbFilename($originalFile['filename'],
                        $originalFile['extension'],
                        $thumbConfig->getAlias(),
                        $thumbConfig->getWidth(),
                        $thumbConfig->getHeight()
                    );

        $thumbContent = Image::thumbnail(Image::getImagine()->load($this->getOriginalContent()),
            $thumbConfig->getWidth(),
            $thumbConfig->getHeight(),
            $thumbConfig->getMode()
        )->get($originalFile['extension']);

        $result = $this->s3Client->putObject([
            'ACL' => 'public-read',
            'Body' => $thumbContent,
            'Key' => $uploadThumbUrl,
            'Bucket' => $this->s3FileOptions->bucket
        ]);

        if ($result['ObjectURL'] && !empty($result['ObjectURL'])) {
            return $result['ObjectURL'];
        }

        return null;
    }

    /**
     * Actions after main save.
     *
     * @return mixed
     */
    protected function afterSave()
    {
        if (null !== $this->owner && null !== $this->ownerId && null != $this->ownerAttribute) {
            $this->mediafileModel->addOwner($this->ownerId, $this->owner, $this->ownerAttribute);
        }

        if (null !== $this->bucketForUpload && null !== $this->uploadDir) {
            $this->setS3FileOptions($this->bucketForUpload, $this->uploadDir);
        }
    }

    /**
     * Get binary contente of the original file.
     *
     * @throws InvalidValueException
     *
     * @return string
     */
    private function getOriginalContent()
    {
        if (null === $this->originalContent) {
            $this->originalContent = file_get_contents($this->mediafileModel->url);
        }

        if (!$this->originalContent) {
            throw new InvalidValueException('Content from '.$this->mediafileModel->url.' can not be read.');
        }

        return $this->originalContent;
    }

    /**
     * S3 file options (bucket, prefix).
     *
     * @return ActiveRecord|S3FileOptions
     */
    private function getS3FileOptions()
    {
        return S3FileOptions::find()->where([
            'mediafileId' => $this->mediafileModel->id
        ])->one();
    }

    /**
     * Set S3 options for uploaded file in amazon S3 storage.
     *
     * @param string $bucket
     * @param string $prefix
     *
     * @return void
     */
    private function setS3FileOptions(string $bucket, string $prefix): void
    {
        if (null !== $this->file) {
            S3FileOptions::deleteAll([
                'mediafileId' => $this->mediafileModel->id
            ]);
            $optionsModel = new S3FileOptions();
            $optionsModel->mediafileId = $this->mediafileModel->id;
            $optionsModel->bucket = $bucket;
            $optionsModel->prefix = $prefix;
            $optionsModel->save();
        }
    }
}
