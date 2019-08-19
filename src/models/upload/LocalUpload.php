<?php

namespace Itstructure\MFUploader\models\upload;

use Yii;
use yii\imagine\Image;
use yii\base\InvalidConfigException;
use yii\helpers\{BaseFileHelper, Inflector};
use yii\web\UploadedFile;
use Itstructure\MFUploader\Module;
use Itstructure\MFUploader\models\Mediafile;
use Itstructure\MFUploader\components\ThumbConfig;
use Itstructure\MFUploader\interfaces\{ThumbConfigInterface, UploadModelInterface};

/**
 * Class LocalUpload
 *
 * @property string $uploadRoot Root directory for uploaded files.
 * @property string $directoryForDelete Directory for delete with all content.
 * @property Mediafile $mediafileModel Mediafile model to save files data.
 * @property UploadedFile $file File object.
 *
 * @package Itstructure\MFUploader\models
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class LocalUpload extends BaseUpload implements UploadModelInterface
{
    const DIR_LENGTH_FIRST = 2;
    const DIR_LENGTH_SECOND = 4;

    /**
     * Root directory for uploaded files.
     *
     * @var string
     */
    public $uploadRoot;

    /**
     * Directory for delete with all content.
     *
     * @var string
     */
    private $directoryForDelete = [];

    /**
     * Initialize.
     */
    public function init()
    {
        if (null === $this->uploadRoot || !is_string($this->uploadRoot)) {
            throw new InvalidConfigException('The uploadRoot is not defined correctly.');
        }

        $this->uploadRoot = trim(trim($this->uploadRoot, '/'), '\\');
    }

    /**
     * Get storage type - local.
     *
     * @return string
     */
    protected function getStorageType(): string
    {
        return Module::STORAGE_TYPE_LOCAL;
    }

    /**
     * Set some params for send.
     * It is needed to set the next parameters:
     * $this->uploadDir
     * $this->uploadPath
     * $this->outFileName
     * $this->databaseUrl
     *
     * @throws InvalidConfigException
     *
     * @return void
     */
    protected function setParamsForSend(): void
    {
        $uploadDir = trim(trim($this->getUploadDirConfig($this->file->type), '/'), '\\');

        if (!empty($this->subDir)) {
            $uploadDir = $uploadDir .
                DIRECTORY_SEPARATOR .
                trim(trim($this->subDir, '/'), '\\');
        }

        $this->uploadDir = $uploadDir .
            DIRECTORY_SEPARATOR . substr(md5(time()), 0, self::DIR_LENGTH_FIRST) .
            DIRECTORY_SEPARATOR . substr(md5(microtime().$this->file->tempName), 0, self::DIR_LENGTH_SECOND);

        $this->uploadPath = $this->uploadRoot . DIRECTORY_SEPARATOR . $this->uploadDir;

        $this->outFileName = $this->renameFiles ?
            md5(md5(microtime()).$this->file->tempName).'.'.$this->file->extension :
            Inflector::slug($this->file->baseName).'.'. $this->file->extension;

        $this->databaseUrl = DIRECTORY_SEPARATOR . $this->uploadDir . DIRECTORY_SEPARATOR . $this->outFileName;
    }

    /**
     * Set some params for delete.
     * It is needed to set the next parameters:
     * $this->directoryForDelete
     *
     * @return void
     */
    protected function setParamsForDelete(): void
    {
        $originalFile = pathinfo($this->mediafileModel->url);

        $dirname = ltrim($originalFile['dirname'], DIRECTORY_SEPARATOR);

        $dirnameParent = substr($dirname, 0, -(self::DIR_LENGTH_SECOND+1));

        if (count(BaseFileHelper::findDirectories($dirnameParent)) == 1) {
            $this->directoryForDelete = $this->uploadRoot . DIRECTORY_SEPARATOR . $dirnameParent;
        } else {
            $this->directoryForDelete = $this->uploadRoot . DIRECTORY_SEPARATOR . $dirname;
        }
    }

    /**
     * Save file in local directory.
     *
     * @return bool
     */
    protected function sendFile(): bool
    {
        BaseFileHelper::createDirectory($this->uploadPath, 0777);

        $savePath = $this->uploadPath . DIRECTORY_SEPARATOR . $this->outFileName;

        $this->file->saveAs($savePath);

        return file_exists($savePath);
    }

    /**
     * Delete local directory with original file and thumbs.
     *
     * @return void
     */
    protected function deleteFiles(): void
    {
        BaseFileHelper::removeDirectory($this->directoryForDelete);
    }

    /**
     * Create thumb.
     *
     * @param ThumbConfigInterface|ThumbConfig $thumbConfig
     *
     * @return string
     */
    protected function createThumb(ThumbConfigInterface $thumbConfig)
    {
        $originalFile = pathinfo($this->mediafileModel->url);

        $thumbUrl = $originalFile['dirname'] .
                    DIRECTORY_SEPARATOR .
                    $this->getThumbFilename($originalFile['filename'],
                        $originalFile['extension'],
                        $thumbConfig->alias,
                        $thumbConfig->width,
                        $thumbConfig->height
                    );

        Image::thumbnail($this->uploadRoot . DIRECTORY_SEPARATOR . $this->mediafileModel->url,
            $thumbConfig->width,
            $thumbConfig->height,
            $thumbConfig->mode
        )->save($this->uploadRoot . DIRECTORY_SEPARATOR . trim(trim($thumbUrl, '\\'), '/'));

        return $thumbUrl;
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
    }
}
