<?php

namespace Itstructure\MFUploader\controllers\album;

use Itstructure\MFUploader\models\album\TextAlbum;

/**
 * TextAlbumController extends the base abstract AlbumController.
 *
 * @package Itstructure\MFUploader\controllers\album
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class TextAlbumController extends AlbumController
{
    /**
     * Returns the name of the TextAlbum model.
     *
     * @return string
     */
    protected function getModelName():string
    {
        return TextAlbum::class;
    }

    /**
     * Returns the type of text album.
     *
     * @return string
     */
    protected function getAlbumType():string
    {
        return TextAlbum::ALBUM_TYPE_TEXT;
    }
}
