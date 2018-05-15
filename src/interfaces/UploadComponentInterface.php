<?php

namespace Itstructure\MFUploader\interfaces;

use Itstructure\MFUploader\models\Mediafile;

/**
 * Interface UploadComponentInterface
 *
 * @package Itstructure\MFUploader\interfaces
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
interface UploadComponentInterface
{
    /**
     * Sets a mediafile model for upload file.
     * @param Mediafile $mediafileModel
     * @return UploadModelInterface
     */
    public function setModelForSave(Mediafile $mediafileModel): UploadModelInterface;

    /**
     * Sets a mediafile model for delete file.
     * @param Mediafile $mediafileModel
     * @return UploadModelInterface
     */
    public function setModelForDelete(Mediafile $mediafileModel): UploadModelInterface;
}
