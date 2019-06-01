<?php

namespace Itstructure\MFUploader;

use Yii;
use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\base\{Module as BaseModule, InvalidConfigException};
use Imagine\Image\ImageInterface;
use Itstructure\MFUploader\interfaces\ThumbConfigInterface;
use Itstructure\MFUploader\components\{LocalUploadComponent, S3UploadComponent, ThumbConfig};
use Itstructure\MFUploader\models\Mediafile;

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
 * @property View $view View component to render content.
 *
 * @package Itstructure\MFUploader
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class Module extends BaseModule
{
    const MODULE_NAME = 'mfuploader';

    const THUMB_ALIAS_DEFAULT  = 'default';
    const THUMB_ALIAS_ORIGINAL = 'original';
    const THUMB_ALIAS_SMALL    = 'small';
    const THUMB_ALIAS_MEDIUM   = 'medium';
    const THUMB_ALIAS_LARGE    = 'large';

    const URL_FILE_MANAGER   = '/'.self::MODULE_NAME.'/managers/filemanager';
    const URL_UPLOAD_MANAGER = '/'.self::MODULE_NAME.'/managers/uploadmanager';
    const URL_FILE_INFO      = '/'.self::MODULE_NAME.'/fileinfo/index';
    const URL_LOCAL_SEND     = '/'.self::MODULE_NAME.'/upload/local-upload/send';
    const URL_LOCAL_UPDATE   = '/'.self::MODULE_NAME.'/upload/local-upload/update';
    const URL_LOCAL_DELETE   = '/'.self::MODULE_NAME.'/upload/local-upload/delete';
    const URL_S3_SEND        = '/'.self::MODULE_NAME.'/upload/s3-upload/send';
    const URL_S3_UPDATE      = '/'.self::MODULE_NAME.'/upload/s3-upload/update';
    const URL_S3_DELETE      = '/'.self::MODULE_NAME.'/upload/s3-upload/delete';

    const BACK_URL_PARAM = '__backUrl';

    const ORIGINAL_PREVIEW_WIDTH = 300;
    const ORIGINAL_PREVIEW_HEIGHT = 240;
    const SCANTY_PREVIEW_SIZE = 50;

    const STORAGE_TYPE_LOCAL = 'local';
    const STORAGE_TYPE_S3 = 's3';

    /**
     * Login url.
     *
     * @var null|string|array
     */
    public $loginUrl = null;

    /**
     * Array of roles to module access.
     *
     * @var array
     */
    public $accessRoles = ['@'];

    /**
     * Name of the file field to load using Ajax request.
     *
     * @var string
     */
    public $fileAttributeName = 'file';

    /**
     * Preview options for som types of mediafiles according with their location.
     * See how it's done in "preview-options" config file as an example.
     *
     * @var array
     */
    public $previewOptions = [];

    /**
     * Thumbs config with their types and sizes.
     * See how it's done in "thumbs-config" config file as an example.
     *
     * @var array of thumbnails.
     */
    public $thumbsConfig = [];

    /**
     * Thumbnails name template.
     * Values can be the next: {original}, {width}, {height}, {alias}, {extension}
     *
     * @var string
     */
    public $thumbFilenameTemplate = '{original}-{width}-{height}-{alias}.{extension}';

    /**
     * Default thumbnail stub urls according with file type.
     * See how it's done in "thumb-stub-urls" config file as an example.
     *
     * @var array
     */
    public $thumbStubUrls = [];

    /**
     * Csrf validation.
     *
     * @var bool
     */
    public $enableCsrfValidation = true;

    /**
     * Default storage type. Can be 'local' or 's3'.
     *
     * @var string
     */
    public $defaultStorageType = self::STORAGE_TYPE_LOCAL;

    /**
     * View component to render content.
     *
     * @var View
     */
    private $_view = null;

    /**
     * Urls to send new files depending on the storage type.
     *
     * @var array
     */
    private static $sendUrls = [
        self::STORAGE_TYPE_LOCAL => self::URL_LOCAL_SEND,
        self::STORAGE_TYPE_S3 => self::URL_S3_SEND,
    ];

    /**
     * Urls to update existing files depending on the storage type.
     *
     * @var array
     */
    private static $updateUrls = [
        self::STORAGE_TYPE_LOCAL => self::URL_LOCAL_UPDATE,
        self::STORAGE_TYPE_S3 => self::URL_S3_UPDATE,
    ];

    /**
     * Urls to delete existing files depending on the storage type.
     *
     * @var array
     */
    private static $deleteUrls = [
        self::STORAGE_TYPE_LOCAL => self::URL_LOCAL_DELETE,
        self::STORAGE_TYPE_S3 => self::URL_S3_DELETE,
    ];

    /**
     * Module translations.
     *
     * @var array|null
     */
    private static $_translations = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::setAlias('@'.self::MODULE_NAME.'', static::getBaseDir());

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
     *
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
     *
     * @return string
     */
    public static function getBaseDir(): string
    {
        return __DIR__;
    }

    /**
     * Get src to send new files.
     *
     * @param string $storageType
     *
     * @return string
     *
     * @throws InvalidConfigException
     */
    public static function getSendUrl(string $storageType): string
    {
        if (!isset(self::$sendUrls[$storageType])) {
            throw new InvalidConfigException('There is no such storage type in the send src urls.');
        }

        return self::$sendUrls[$storageType];
    }

    /**
     * Get src to update existing files.
     *
     * @param string $storageType
     *
     * @return string
     *
     * @throws InvalidConfigException
     */
    public static function getUpdateUrl(string $storageType): string
    {
        if (!isset(self::$updateUrls[$storageType])) {
            throw new InvalidConfigException('There is no such storage type in the update src urls.');
        }

        return self::$updateUrls[$storageType];
    }

    /**
     * Get src to delete existing files.
     *
     * @param string $storageType
     *
     * @return string
     *
     * @throws InvalidConfigException
     */
    public static function getDeleteUrl(string $storageType): string
    {
        if (!isset(self::$deleteUrls[$storageType])) {
            throw new InvalidConfigException('There is no such storage type in the delete src urls.');
        }

        return self::$deleteUrls[$storageType];
    }

    /**
     * Set thumb configuration.
     *
     * @param string $alias
     * @param array  $config
     *
     * @throws InvalidConfigException
     *
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
     *
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
     *
     * @param string $fileType
     * @param string $location
     * @param Mediafile|null $mediafile
     *
     * @return array
     */
    public function getPreviewOptions(string $fileType, string $location, Mediafile $mediafile = null): array
    {
        if (null === $fileType || null === $location) {
            return [];
        }

        if (!isset($this->previewOptions[$fileType]) || !is_array($this->previewOptions[$fileType])) {
            return [];
        }

        $previewOptions = $this->previewOptions[$fileType];

        if (!isset($previewOptions[$location])) {
            return [];
        }

        $result = [];

        if (is_callable($previewOptions[$location])) {
            $result = call_user_func($previewOptions[$location], $mediafile);

            if (!is_array($result)) {
                return [];
            }

        } else if (is_array($previewOptions[$location])) {
            $result = $previewOptions[$location];
        }

        return $result;
    }

    /**
     * Module translator.
     *
     * @param       $category
     * @param       $message
     * @param array $params
     * @param null  $language
     *
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        if (null === self::$_translations) {
            self::registerTranslations();
        }

        return Yii::t('modules/'.self::MODULE_NAME.'/' . $category, $message, $params, $language);
    }

    /**
     * Set i18N component.
     *
     * @return void
     */
    private function registerTranslations(): void
    {
        self::$_translations = [
            'modules/'.self::MODULE_NAME.'/*' => [
                'class'          => 'yii\i18n\PhpMessageSource',
                'forceTranslation' => true,
                'sourceLanguage' => Yii::$app->language,
                'basePath'       => '@'.self::MODULE_NAME.'/messages',
                'fileMap'        => [
                    'modules/'.self::MODULE_NAME.'/main' => 'main.php',
                    'modules/'.self::MODULE_NAME.'/album' => 'album.php',
                    'modules/'.self::MODULE_NAME.'/filemanager' => 'filemanager.php',
                    'modules/'.self::MODULE_NAME.'/uploadmanager' => 'uploadmanager.php',
                    'modules/'.self::MODULE_NAME.'/actions' => 'actions.php',
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
     *
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
     *
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
