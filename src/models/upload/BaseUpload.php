<?php

namespace Itstructure\MFUploader\models\upload;

use yii\base\{Model, InvalidConfigException};
use yii\web\UploadedFile;
use yii\imagine\Image;
use Itstructure\MFUploader\Module;
use Itstructure\MFUploader\models\Mediafile;
use Itstructure\MFUploader\interfaces\{ThumbConfigInterface, UploadModelInterface};

/**
 * Class BaseUpload
 *
 * @property string $alt Alt text for the file.
 * @property string $title Title for the file.
 * @property string $description File description.
 * @property string $subDir Addition sub-directory for uploaded files.
 * @property string $owner Owner name (post, page, article e.t.c.).
 * @property int $ownerId Owner id.
 * @property string $ownerAttribute Owner attribute (image, audio, thumbnail e.t.c.).
 * @property string $neededFileType Needed file type for validation (thumbnail, image e.t.c.).
 * @property bool $renameFiles Rename file after upload.
 * @property array $fileExtensions File extensions.
 * @property bool $checkExtensionByMimeType Check extension by MIME type (they are must match).
 * @property int $fileMaxSize Maximum file size.
 * @property array $thumbsConfig Thumbs config with their types and sizes.
 * @property string $thumbFilenameTemplate Thumbnails name template.
 * Default template: {original}-{width}-{height}-{alias}.{extension}
 * @property array $uploadDirs Directories for uploaded files depending on the file type.
 * @property string $uploadDir Directory for uploaded files.
 * @property string $uploadPath Full directory path to upload file.
 * @property string $outFileName Prepared file name to save in database and storage.
 * @property string $databaseUrl File url path for database.
 * @property UploadedFile $file File object.
 * @property Mediafile $mediafileModel Mediafile model to save files data.
 *
 * @package Itstructure\MFUploader\models
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
abstract class BaseUpload extends Model
{
    /**
     * Scripts Constants.
     * Required for certain validation rules to work for specific scenarios.
     */
    const SCENARIO_UPLOAD = 'upload';
    const SCENARIO_UPDATE = 'update';

    /**
     * Alt text for the file.
     * @var string
     */
    public $alt;

    /**
     * Title for the file.
     * @var string
     */
    public $title;

    /**
     * File description.
     * @var string
     */
    public $description;

    /**
     * Addition sub-directory for uploaded files.
     * @var string
     */
    public $subDir;

    /**
     * Owner name (post, page, article e.t.c.).
     * @var string
     */
    public $owner;

    /**
     * Owner id.
     * @var int
     */
    public $ownerId;

    /**
     * Owner attribute (image, audio, thumbnail e.t.c.).
     * @var string
     */
    public $ownerAttribute;

    /**
     * Needed file type for validation (thumbnail, image e.t.c.).
     * @var string
     */
    public $neededFileType;

    /**
     * Rename file after upload.
     * @var bool
     */
    public $renameFiles = true;

    /**
     * File extensions.
     * @var array
     */
    public $fileExtensions = [
        UploadModelInterface::FILE_TYPE_THUMB => [
            'png', 'jpg', 'jpeg', 'gif',
        ],
        UploadModelInterface::FILE_TYPE_IMAGE => [
            'png', 'jpg', 'jpeg', 'gif',
        ],
        UploadModelInterface::FILE_TYPE_AUDIO => [
            'mp3',
        ],
        UploadModelInterface::FILE_TYPE_VIDEO => [
            'mp4', 'ogg', 'ogv', 'oga', 'ogx', 'webm',
        ],
        UploadModelInterface::FILE_TYPE_APP => [
            'doc', 'docx', 'rtf', 'pdf', 'rar', 'zip', 'jar', 'mcd', 'xls',
        ],
        UploadModelInterface::FILE_TYPE_TEXT => [
            'txt',
        ],
        UploadModelInterface::FILE_TYPE_OTHER => null,
    ];

    /**
     * Check extension by MIME type (they are must match).
     * @var bool
     */
    public $checkExtensionByMimeType = true;

    /**
     * Maximum file size.
     * @var int
     */
    public $fileMaxSize = 1024*1024*64;

    /**
     * Thumbs config with their types and sizes.
     * @var array
     */
    public $thumbsConfig = [];

    /**
     * Thumbnails name template.
     * Values can be the next: {original}, {width}, {height}, {alias}, {extension}
     * @var string
     */
    public $thumbFilenameTemplate = '{original}-{width}-{height}-{alias}.{extension}';

    /**
     * Directories for uploaded files depending on the file type.
     * @var array
     */
    public $uploadDirs;

    /**
     * Directory for uploaded files.
     * @var string
     */
    protected $uploadDir;

    /**
     * Full directory path to upload file.
     * @var string
     */
    protected $uploadPath;

    /**
     * Prepared file name to save in database and storage.
     * @var string
     */
    protected $outFileName;

    /**
     * File url path for database.
     * @var string
     */
    protected $databaseUrl;

    /**
     * File object.
     * @var UploadedFile
     */
    private $file;

    /**
     * Mediafile model to save files data.
     * @var Mediafile
     */
    private $mediafileModel;

    /**
     * Set params for send file.
     * @return void
     */
    abstract protected function setParamsForSend(): void;

    /**
     * Set some params for delete.
     * @return void
     */
    abstract protected function setParamsForDelete(): void;

    /**
     * Send file to local directory or send file to remote storage.
     * @return bool
     */
    abstract protected function sendFile(): bool;

    /**
     * Delete files from local directory or from remote storage.
     * @return void
     */
    abstract protected function deleteFiles(): void;

    /**
     * Create thumb.
     * @param ThumbConfigInterface $thumbConfig
     * @return string|null
     */
    abstract protected function createThumb(ThumbConfigInterface $thumbConfig);

    /**
     * Get storage type (local, s3, e.t.c...).
     * @return string
     */
    abstract protected function getStorageType(): string;

    /**
     * Actions after main save.
     * @return mixed
     */
    abstract protected function afterSave();

    /**
     * Scenarios.
     * @return array
     */
    public function scenarios(): array
    {
        return [
            self::SCENARIO_UPLOAD => $this->attributes(),
            self::SCENARIO_UPDATE => $this->attributes(),
        ];
    }

    /**
     * Attributes.
     * @return array
     */
    public function attributes()
    {
        return [
            'file',
            'subDir',
            'owner',
            'ownerId',
            'ownerAttribute',
            'neededFileType',
            'alt',
            'title',
            'description',
        ];
    }

        /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'neededFileType',
                ],
                'string',
                'on' => [
                    self::SCENARIO_UPLOAD,
                    self::SCENARIO_UPDATE,
                ],
            ],
            [
                'file',
                'required',
                'on' => [
                    self::SCENARIO_UPLOAD,
                ],
            ],
            [
                'file',
                'file',
                'on' => [
                    self::SCENARIO_UPLOAD,
                    self::SCENARIO_UPDATE,
                ],
                'skipOnEmpty' => true,
                'extensions' => array_key_exists($this->neededFileType, $this->fileExtensions) ? $this->fileExtensions[$this->neededFileType] : null,
                'checkExtensionByMimeType' => $this->checkExtensionByMimeType,
                'maxSize' => $this->fileMaxSize
            ],
            [
                'subDir',
                'string',
                'on' => [
                    self::SCENARIO_UPLOAD,
                    self::SCENARIO_UPDATE,
                ],
                'max' => 24,
            ],
            [
                'subDir',
                'filter',
                'on' => [
                    self::SCENARIO_UPLOAD,
                    self::SCENARIO_UPDATE,
                ],
                'filter' => function($value){
                    return trim($value, DIRECTORY_SEPARATOR);
                }
            ],
            [
                [
                    'alt',
                    'title',
                    'description',
                ],
                'string',
                'on' => [
                    self::SCENARIO_UPLOAD,
                    self::SCENARIO_UPDATE,
                ],
            ],
            [
                [
                    'owner',
                    'ownerAttribute',
                ],
                'string',
                'on' => [
                    self::SCENARIO_UPLOAD,
                ],
            ],
            [
                'ownerId',
                'integer',
                'on' => [
                    self::SCENARIO_UPLOAD,
                ],
            ],
        ];
    }

    /**
     * Set mediafile model.
     * @param Mediafile $model
     */
    public function setMediafileModel(Mediafile $model): void
    {
        $this->mediafileModel = $model;
    }

    /**
     * Get mediafile model.
     * @return Mediafile
     */
    public function getMediafileModel(): Mediafile
    {
        return $this->mediafileModel;
    }

    /**
     * Set file.
     * @param UploadedFile|null $file
     * @return void
     */
    public function setFile(UploadedFile $file = null): void
    {
        $this->file = $file;
    }

    /**
     * Set file.
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Save file in the storage and database by using a "mediafileModel".
     * @throws \Exception
     * @return bool
     */
    public function save(): bool
    {
        if ($this->mediafileModel->isNewRecord){
            $this->setScenario(self::SCENARIO_UPLOAD);
        } else {
            $this->setScenario(self::SCENARIO_UPDATE);
        }

        if (!$this->validate()){
            return false;
        }

        if (null !== $this->file){

            $this->setParamsForSend();

            if (!$this->sendFile()){
                throw new \Exception('Error upload file.', 500);
            }

            if ($this->getScenario() == self::SCENARIO_UPDATE){
                $this->setParamsForDelete();
                $this->deleteFiles();
            }

            $this->mediafileModel->url = $this->databaseUrl;
            $this->mediafileModel->filename = $this->outFileName;
            $this->mediafileModel->size = $this->file->size;
            $this->mediafileModel->type = $this->file->type;
            $this->mediafileModel->storage = $this->getStorageType();
        }

        $this->mediafileModel->alt = $this->alt;
        $this->mediafileModel->title = $this->title;
        $this->mediafileModel->description = $this->description;

        if (!$this->mediafileModel->save()){
            throw new \Exception('Error save file data in database.', 500);
        }

        $this->afterSave();

        return true;
    }

    /**
     * Delete files from local directory or from remote storage.
     * @throws \Exception
     * @return int
     */
    public function delete(): int
    {
        $this->setParamsForDelete();

        $this->deleteFiles();

        $deleted = $this->mediafileModel->delete();

        if (false === $deleted){
            throw new \Exception('Error delete file data from database.', 500);
        }

        return $deleted;
    }

    /**
     * Returns current model id.
     * @return int|string
     */
    public function getId()
    {
        return $this->mediafileModel->id;
    }

    /**
     * Create thumbs for this image
     * @throws InvalidConfigException
     * @return bool
     */
    public function createThumbs(): bool
    {
        $thumbs = [];

        Image::$driver = [Image::DRIVER_GD2, Image::DRIVER_GMAGICK, Image::DRIVER_IMAGICK];

        foreach ($this->thumbsConfig as $alias => $preset) {
            $thumbUrl = $this->createThumb(Module::configureThumb($alias, $preset));
            if (null === $thumbUrl){
                continue;
            }
            $thumbs[$alias] = $thumbUrl;
        }

        // Create default thumb.
        if (!array_key_exists(Module::DEFAULT_THUMB_ALIAS, $this->thumbsConfig)){
            $thumbUrlDefault = $this->createThumb(
                Module::configureThumb(
                    Module::DEFAULT_THUMB_ALIAS,
                    Module::getDefaultThumbConfig()
                )
            );
            if (null !== $thumbUrlDefault){
                $thumbs[Module::DEFAULT_THUMB_ALIAS] = $thumbUrlDefault;
            }
        }

        $this->mediafileModel->thumbs = serialize($thumbs);

        return $this->mediafileModel->save();
    }

    /**
     * Returns thumbnail name.
     * @param $original
     * @param $extension
     * @param $alias
     * @param $width
     * @param $height
     * @return string
     */
    public function getThumbFilename($original, $extension, $alias, $width, $height)
    {
        return strtr($this->thumbFilenameTemplate, [
            '{original}'  => $original,
            '{extension}' => $extension,
            '{alias}'     => $alias,
            '{width}'     => $width,
            '{height}'    => $height,
        ]);
    }

    /**
     * Get upload directory configuration by file type.
     * @param string $fileType
     * @throws InvalidConfigException
     * @return string
     */
    protected function getUploadDirConfig(string $fileType): string
    {
        if (!is_array($this->uploadDirs) || empty($this->uploadDirs)){
            throw new InvalidConfigException('The localUploadDirs is not defined.');
        }

        if (strpos($fileType, UploadModelInterface::FILE_TYPE_IMAGE) !== false) {
            return $this->uploadDirs[UploadModelInterface::FILE_TYPE_IMAGE];

        } elseif (strpos($fileType, UploadModelInterface::FILE_TYPE_AUDIO) !== false) {
            return $this->uploadDirs[UploadModelInterface::FILE_TYPE_AUDIO];

        } elseif (strpos($fileType, UploadModelInterface::FILE_TYPE_VIDEO) !== false) {
            return $this->uploadDirs[UploadModelInterface::FILE_TYPE_VIDEO];

        } elseif (strpos($fileType, UploadModelInterface::FILE_TYPE_APP) !== false) {
            return $this->uploadDirs[UploadModelInterface::FILE_TYPE_APP];

        } elseif (strpos($fileType, UploadModelInterface::FILE_TYPE_TEXT) !== false) {
            return $this->uploadDirs[UploadModelInterface::FILE_TYPE_TEXT];

        } else {
            return $this->uploadDirs[UploadModelInterface::FILE_TYPE_OTHER];
        }
    }
}
