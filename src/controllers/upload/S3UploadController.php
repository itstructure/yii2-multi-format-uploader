<?php

namespace Itstructure\MFUploader\controllers\upload;

use yii\base\InvalidConfigException;
use Itstructure\MFUploader\interfaces\UploadComponentInterface;

/**
 * Class S3UploadController
 * Upload controller class to upload files in amazon s3 buckets.
 *
 * @package Itstructure\MFUploader\controllers\upload
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class S3UploadController extends CommonUploadController
{
    /**
     * Get s3 upload component.
     *
     * @throws InvalidConfigException
     *
     * @return UploadComponentInterface
     */
    protected function getUploadComponent(): UploadComponentInterface
    {
        $uploadComponent = $this->module->get('s3-upload-component');

        if (!($uploadComponent instanceof UploadComponentInterface)) {
            throw new InvalidConfigException("s3-upload-component must be implemented of UploadComponentInterface.");
        }

        return $uploadComponent;
    }
}
