<?php

namespace Itstructure\MFUploader\controllers\album;

use Itstructure\MFUploader\models\album\AppAlbum;

/**
 * ApplicationAlbumController extends the base abstract AlbumController.
 *
 * @package Itstructure\MFUploader\controllers\album
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class ApplicationAlbumController extends AlbumController
{
    /**
     * Returns the name of the AppAlbum model.
     *
     * @return string
     */
    protected function getModelName():string
    {
        return AppAlbum::class;
    }

    /**
     * Returns the type of application album.
     *
     * @return string
     */
    protected function getAlbumType():string
    {
        return AppAlbum::ALBUM_TYPE_APP;
    }
}
