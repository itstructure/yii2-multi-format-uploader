<?php

namespace Itstructure\MFUploader\controllers\album;

use Itstructure\MFUploader\models\album\VideoAlbum;

/**
 * VideoAlbumController extends the base abstract AlbumController.
 *
 * @package Itstructure\MFUploader\controllers\album
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class VideoAlbumController extends AlbumController
{
    /**
     * Returns the name of the VideoAlbum model.
     *
     * @return string
     */
    protected function getModelName():string
    {
        return VideoAlbum::class;
    }

    /**
     * Returns the type of video album.
     *
     * @return string
     */
    protected function getAlbumType():string
    {
        return VideoAlbum::ALBUM_TYPE_VIDEO;
    }
}
