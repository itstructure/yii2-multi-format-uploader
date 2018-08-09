<?php

namespace Itstructure\MFUploader\controllers\album;

use Itstructure\MFUploader\models\album\ImageAlbum;

/**
 * ImageAlbumController extends the base abstract AlbumController.
 *
 * @package Itstructure\MFUploader\controllers\album
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class ImageAlbumController extends AlbumController
{
    /**
     * Returns the name of the ImageAlbum model.
     *
     * @return string
     */
    protected function getModelName():string
    {
        return ImageAlbum::class;
    }

    /**
     * Returns the type of image album.
     *
     * @return string
     */
    protected function getAlbumType():string
    {
        return ImageAlbum::ALBUM_TYPE_IMAGE;
    }
}
