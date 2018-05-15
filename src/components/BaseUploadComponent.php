<?php

namespace Itstructure\MFUploader\components;

use yii\base\Component;
use Itstructure\MFUploader\interfaces\UploadModelInterface;

/**
 * Class BaseUploadComponent
 *
 * @property bool $renameFiles Rename file after upload.
 * @property array $fileExtensions File extensions.
 * @property bool $checkExtensionByMimeType Check extension by MIME type (they are must match).
 * @property int $fileMaxSize Maximum file size.
 * @property array $thumbsConfig Thumbs config with their types and sizes.
 * @property string $thumbFilenameTemplate Thumbnails name template.
 * Values can be the next: {original}, {width}, {height}, {alias}, {extension}
 *
 * @package Itstructure\MFUploader\components
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class BaseUploadComponent extends Component
{
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
     * Get base config for save.
     * @return array
     */
    protected function getBaseConfigForSave(): array
    {
        return [
            'renameFiles' => $this->renameFiles,
            'fileExtensions' => $this->fileExtensions,
            'checkExtensionByMimeType' => $this->checkExtensionByMimeType,
            'fileMaxSize' => $this->fileMaxSize,
            'thumbsConfig' => $this->thumbsConfig,
            'thumbFilenameTemplate' => $this->thumbFilenameTemplate,
        ];
    }
}
