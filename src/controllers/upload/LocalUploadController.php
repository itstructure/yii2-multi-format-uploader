<?php

namespace Itstructure\MFUploader\controllers\upload;

use yii\base\InvalidConfigException;
use Itstructure\MFUploader\interfaces\UploadComponentInterface;

/**
 * Class LocalUploadController
 * Upload controller class to upload files in local directory.
 *
 * @package Itstructure\MFUploader\controllers\upload
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class LocalUploadController extends CommonUploadController
{
    /**
     * Get local upload component.
     *
     * @throws InvalidConfigException
     *
     * @return UploadComponentInterface
     */
    protected function getUploadComponent(): UploadComponentInterface
    {
        $uploadComponent = $this->module->get('local-upload-component');

        if (!($uploadComponent instanceof UploadComponentInterface)) {
            throw new InvalidConfigException("local-upload-component must be implemented of UploadComponentInterface.");
        }

        return $uploadComponent;
    }
}
