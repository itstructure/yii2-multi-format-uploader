<?php

namespace Itstructure\MFUploader\components;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use Aws\S3\{S3MultiRegionClient, S3ClientInterface};
use Itstructure\MFUploader\models\Mediafile;
use Itstructure\MFUploader\models\upload\S3Upload;
use Itstructure\MFUploader\interfaces\{UploadModelInterface, UploadComponentInterface};

/**
 * Class S3UploadComponent
 * Component class to upload files in Amazon S3 bucket.
 *
 * @property array $uploadDirs Directory for uploaded files.
 * @property array|callable $credentials AWS access key ID and secret access key.
 * @see https://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/credentials.html
 * @property string $region Region to connect to.
 * @property string $clientVersion S3 client version.
 * @property string $s3DefaultBucket Amazon web services S3 default bucket.
 * @property array $s3Buckets Buckets for upload depending on the owner.
 * @property S3MultiRegionClient|S3ClientInterface $s3Client Amazon web services SDK S3 client.
 *
 * @package Itstructure\MFUploader\components
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class S3UploadComponent extends BaseUploadComponent implements UploadComponentInterface
{
    /**
     * Directory for uploaded files.
     *
     * @var string
     */
    public $uploadDirs = [
        UploadModelInterface::FILE_TYPE_IMAGE => 'images',
        UploadModelInterface::FILE_TYPE_AUDIO => 'audio',
        UploadModelInterface::FILE_TYPE_VIDEO => 'video',
        UploadModelInterface::FILE_TYPE_APP => 'application',
        UploadModelInterface::FILE_TYPE_TEXT => 'text',
        UploadModelInterface::FILE_TYPE_OTHER => 'other',
    ];

    /**
     * AWS access key ID and secret access key.
     * @see https://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/credentials.html
     *
     * @var array|callable
     */
    public $credentials;

    /**
     * Region to connect to.
     *
     * @var string
     */
    public $region = 'us-west-2';

    /**
     * S3 client version.
     *
     * @var string
     */
    public $clientVersion = 'latest';

    /**
     * Amazon web services S3 default bucket.
     *
     * @var string
     */
    public $s3DefaultBucket;

    /**
     * Buckets to upload files depending on the owner.
     *
     * @var array
     */
    public $s3Buckets = [];

    /**
     * Amazon web services SDK S3 client.
     *
     * @var S3MultiRegionClient|S3ClientInterface
     */
    private $s3Client;

    /**
     * Initialize.
     */
    public function init()
    {
        if (null === $this->credentials && !is_callable($this->credentials)) {
            throw new InvalidConfigException('Credentials are not defined correctly.');
        }

        $this->s3Client = new S3MultiRegionClient([
            'version' => $this->clientVersion,
            'region'  => $this->region,
            'credentials' => $this->credentials,
        ]);
    }

    /**
     * Sets a mediafile model for upload file.
     *
     * @param Mediafile $mediafileModel
     *
     * @return UploadModelInterface
     */
    public function setModelForSave(Mediafile $mediafileModel): UploadModelInterface
    {
        /* @var UploadModelInterface $object */
        $object = Yii::createObject(ArrayHelper::merge([
                'class' => S3Upload::class,
                'mediafileModel' => $mediafileModel,
                'uploadDirs' => $this->uploadDirs,
                's3Client' => $this->s3Client,
                's3DefaultBucket' => $this->s3DefaultBucket,
                's3Buckets' => $this->s3Buckets,
            ], $this->getBaseConfigForSave())
        );

        return $object;
    }

    /**
     * Sets a mediafile model for delete file.
     *
     * @param Mediafile $mediafileModel
     *
     * @return UploadModelInterface
     */
    public function setModelForDelete(Mediafile $mediafileModel): UploadModelInterface
    {
        /* @var UploadModelInterface $object */
        $object = Yii::createObject([
            'class' => S3Upload::class,
            'mediafileModel' => $mediafileModel,
            's3Client' => $this->s3Client,
        ]);

        return $object;
    }
}
