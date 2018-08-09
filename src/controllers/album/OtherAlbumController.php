<?php

namespace Itstructure\MFUploader\controllers\album;

use Itstructure\MFUploader\models\album\OtherAlbum;

/**
 * OtherAlbumController extends the base abstract AlbumController.
 *
 * @package Itstructure\MFUploader\controllers\album
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class OtherAlbumController extends AlbumController
{
    /**
     * Returns the name of the OtherAlbum model.
     *
     * @return string
     */
    protected function getModelName():string
    {
        return OtherAlbum::class;
    }

    /**
     * Returns the type of other album.
     *
     * @return string
     */
    protected function getAlbumType():string
    {
        return OtherAlbum::ALBUM_TYPE_OTHER;
    }
}
