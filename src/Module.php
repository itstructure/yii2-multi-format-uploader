<?php

namespace Itstructure\MFUploader;

use Yii;
use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\base\{Module as BaseModule, InvalidConfigException};
use Imagine\Image\ImageInterface;
use Itstructure\MFUploader\interfaces\ThumbConfigInterface;
use Itstructure\MFUploader\components\{LocalUploadComponent, S3UploadComponent, ThumbConfig};

/**
 * Multi format uploader module class.
 *
 * @property null|string|array $loginUrl Login url.
 * @property array $accessRoles Array of roles to module access.
 * @property string $fileAttributeName Name of the file field to load using Ajax request.
 * @property array $previewOptions Preview options for som types of mediafiles according with their location.
 * @property array $thumbsConfig Thumbs config with their types and sizes.
 * @property string $thumbFilenameTemplate Thumbnails name template.
 * Values can be the next: {original}, {width}, {height}, {alias}, {extension}
 * @property array $thumbStubUrls Default thumbnail stub urls according with file type.
 * @property bool $enableCsrfValidation Csrf validation.
 * @property string $defaultStorageType Default storage type. Can be 'local' or 's3'.
 * @property View $_view View component to render content.
 *
 * @package Itstructure\MFUploader
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class Module extends BaseModule
{
    const DEFAULT_THUMB_ALIAS = 'default';
    const ORIGINAL_THUMB_ALIAS = 'original';
    const SMALL_THUMB_ALIAS   = 'small';
    const MEDIUM_THUMB_ALIAS  = 'medium';
    const LARGE_THUMB_ALIAS   = 'large';

    const FILE_MANAGER_SRC   = '/mfuploader/managers/filemanager';
    const UPLOAD_MANAGER_SRC = '/mfuploader/managers/uploadmanager';
    const FILE_INFO_SRC      = '/mfuploader/fileinfo/index';
    const LOCAL_SEND_SRC     = '/mfuploader/upload/local-upload/send';
    const LOCAL_UPDATE_SRC   = '/mfuploader/upload/local-upload/update';
    const LOCAL_DELETE_SRC   = '/mfuploader/upload/local-upload/delete';
    const S3_SEND_SRC        = '/mfuploader/upload/s3-upload/send';
    const S3_UPDATE_SRC      = '/mfuploader/upload/s3-upload/update';
    const S3_DELETE_SRC      = '/mfuploader/upload/s3-upload/delete';

    const BACK_URL_PARAM = '__backUrl';

    const ORIGINAL_PREVIEW_WIDTH = 300;
    const ORIGINAL_PREVIEW_HEIGHT = 240;
    const SCANTY_PREVIEW_SIZE = 50;

    const STORAGE_TYPE_LOCAL = 'local';
    const STORAGE_TYPE_S3 = 's3';

    /**
     * Login url.
     * @var null|string|array
     */
    public $loginUrl = null;

    /**
     * Array of roles to module access.
     * @var array
     */
    public $accessRoles = ['@'];

    /**
     * Name of the file field to load using Ajax request.
     * @var string
     */
    public $fileAttributeName = 'file';

    /**
     * Preview options for som types of mediafiles according with their location.
     * See how it's done in "preview-options" config file as an example.
     * @var array
     */
    public $previewOptions = [];

    /**
     * Thumbs config with their types and sizes.
     * See how it's done in "thumbs-config" config file as an example.
     * @var array of thumbnails.
     */
    public $thumbsConfig = [];

    /**
     * Thumbnails name template.
     * Values can be the next: {original}, {width}, {height}, {alias}, {extension}
     * @var string
     */
    public $thumbFilenameTemplate = '{original}-{width}-{height}-{alias}.{extension}';

    /**
     * Default thumbnail stub urls according with file type.
     * See how it's done in "thumb-stub-urls" config file as an example.
     * @var array
     */
    public $thumbStubUrls = [];

    /**
     * Csrf validation.
     * @var bool
     */
    public $enableCsrfValidation = true;

    /**
     * Default storage type. Can be 'local' or 's3'.
     * @var string
     */
    public $defaultStorageType = self::STORAGE_TYPE_LOCAL;

    /**
     * View component to render content.
     * @var View
     */
    private $_view = null;

    /**
     * Urls to send new files depending on the storage type.
     * @var array
     */
    private static $sendSrcUrls = [
        self::STORAGE_TYPE_LOCAL => self::LOCAL_SEND_SRC,
        self::STORAGE_TYPE_S3 => self::S3_SEND_SRC,
    ];

    /**
     * Urls to update existing files depending on the storage type.
     * @var array
     */
    private static $updateSrcUrls = [
        self::STORAGE_TYPE_LOCAL => self::LOCAL_UPDATE_SRC,
        self::STORAGE_TYPE_S3 => self::S3_UPDATE_SRC,
    ];

    /**
     * Urls to delete existing files depending on the storage type.
     * @var array
     */
    private static $deleteSrcUrls = [
        self::STORAGE_TYPE_LOCAL => self::LOCAL_DELETE_SRC,
        self::STORAGE_TYPE_S3 => self::S3_DELETE_SRC,
    ];

    /**
     * Module translations.
     * @var array|null
     */
    private static $_translations = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::setAlias('@mfuploader', static::getBaseDir());

        if (null !== $this->loginUrl && method_exists(Yii::$app, 'getUser')) {
            Yii::$app->getUser()->loginUrl = $this->loginUrl;
        }

        self::registerTranslations();

        $this->previewOptions = ArrayHelper::merge(
            require __DIR__ . '/config/preview-options.php',
            $this->previewOptions
        );

        $this->thumbStubUrls = ArrayHelper::merge(
            require __DIR__ . '/config/thumb-stub-urls.php',
            $this->thumbStubUrls
        );

        $this->thumbsConfig = ArrayHelper::merge(
            require __DIR__ . '/config/thumbs-config.php',
            $this->thumbsConfig
        );

        /**
         * Set aws s3 upload component.
         */
        $this->setComponents(
            ArrayHelper::merge($this->getS3UploadComponentConfig(), $this->components)
        );

        /**
         * Set local upload component.
         */
        $this->setComponents(
            ArrayHelper::merge($this->getLocalUploadComponentConfig(), $this->components)
        );
    }

    /**
     * Get the view.
     * @return View
     */
    public function getView()
    {
        if (null === $this->_view) {
            $this->_view = $this->get('view');
        }

        return $this->_view;
    }

    /**
     * Returns module root directory.
     * @return string
     */
    public static function getBaseDir(): string
    {
        return __DIR__;
    }

    /**
     * Get src to send new files.
     * @param string $storageType
     * @return string
     * @throws InvalidConfigException
     */
    public static function getSendSrc(string $storageType): string
    {
        if (!isset(self::$sendSrcUrls[$storageType])){
            throw new InvalidConfigException('There is no such storage type in the send src urls.');
        }

        return self::$sendSrcUrls[$storageType];
    }

    /**
     * Get src to update existing files.
     * @param string $storageType
     * @return string
     * @throws InvalidConfigException
     */
    public static function getUpdateSrc(string $storageType): string
    {
        if (!isset(self::$updateSrcUrls[$storageType])){
            throw new InvalidConfigException('There is no such storage type in the update src urls.');
        }

        return self::$updateSrcUrls[$storageType];
    }

    /**
     * Get src to delete existing files.
     * @param string $storageType
     * @return string
     * @throws InvalidConfigException
     */
    public static function getDeleteSrc(string $storageType): string
    {
        if (!isset(self::$deleteSrcUrls[$storageType])){
            throw new InvalidConfigException('There is no such storage type in the delete src urls.');
        }

        return self::$deleteSrcUrls[$storageType];
    }

    /**
     * Set thumb configuration.
     * @param string $alias
     * @param array  $config
     * @throws InvalidConfigException
     * @return ThumbConfigInterface
     *
     */
    public static function configureThumb(string $alias, array $config): ThumbConfigInterface
    {
        if (!isset($config['name']) ||
            !isset($config['size']) ||
            !is_array($config['size']) ||
            !isset($config['size'][0]) ||
            !isset($config['size'][1])) {

            throw new InvalidConfigException('Error in thumb configuration.');
        }

        $thumbConfig = [
            'class'  => ThumbConfig::class,
            'alias'  => $alias,
            'name'   => $config['name'],
            'width'  => $config['size'][0],
            'height' => $config['size'][1],
            'mode'   => (isset($config['mode']) ? $config['mode'] : ImageInterface::THUMBNAIL_OUTBOUND),
        ];

        /* @var ThumbConfigInterface $object */
        $object = Yii::createObject($thumbConfig);

        return $object;
    }

    /**
     * Default thumb config
     * @return array
     */
    public static function getDefaultThumbConfig(): array
    {
        return [
            'name' => 'Default size',
            'size' => [150, 150],
        ];
    }

    /**
     * Get preview options for som types of mediafiles according with their location.
     * @param string $fileType
     * @param string $location
     * @return array
     */
    public function getPreviewOptions(string $fileType, string $location): array
    {
        if (null === $fileType || null === $location){
            return [];
        }

        if (!isset($this->previewOptions[$fileType]) || !is_array($this->previewOptions[$fileType])){
            return [];
        }

        if (!isset($this->previewOptions[$fileType][$location]) || !is_array($this->previewOptions[$fileType][$location])){
            return [];
        }

        return $this->previewOptions[$fileType][$location];
    }

    /**
     * Module translator.
     * @param       $category
     * @param       $message
     * @param array $params
     * @param null  $language
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        if (null === self::$_translations){
            self::registerTranslations();
        }

        return Yii::t('modules/mfuploader/' . $category, $message, $params, $language);
    }

    /**
     * Set i18N component.
     * @return void
     */
    private function registerTranslations(): void
    {
        self::$_translations = [
            'modules/mfuploader/*' => [
                'class'          => 'yii\i18n\PhpMessageSource',
                'forceTranslation' => true,
                'sourceLanguage' => Yii::$app->language,
                'basePath'       => '@mfuploader/messages',
                'fileMap'        => [
                    'modules/mfuploader/main' => 'main.php',
                    'modules/mfuploader/album' => 'album.php',
                    'modules/mfuploader/filemanager' => 'filemanager.php',
                    'modules/mfuploader/uploadmanager' => 'uploadmanager.php',
                    'modules/mfuploader/actions' => 'actions.php',
                ],
            ]
        ];

        Yii::$app->i18n->translations = ArrayHelper::merge(
            self::$_translations,
            Yii::$app->i18n->translations
        );
    }

    /**
     * File local upload component config.
     * @return array
     */
    private function getLocalUploadComponentConfig(): array
    {
        return [
            'local-upload-component' => [
                'class' => LocalUploadComponent::class,
                'thumbsConfig' => $this->thumbsConfig,
                'thumbFilenameTemplate' => $this->thumbFilenameTemplate,
            ]
        ];
    }

    /**
     * File aws s3 upload component config.
     * @return array
     */
    private function getS3UploadComponentConfig(): array
    {
        return [
            's3-upload-component' => [
                'class' => S3UploadComponent::class,
                'thumbsConfig' => $this->thumbsConfig,
                'thumbFilenameTemplate' => $this->thumbFilenameTemplate,
            ]
        ];
    }
}
