<?php

use Itstructure\MFUploader\interfaces\UploadModelInterface;

/**
 * Default thumbnail stub urls according with file type.
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
return [
    UploadModelInterface::FILE_TYPE_APP => 'images'.DIRECTORY_SEPARATOR.'app.png',
    UploadModelInterface::FILE_TYPE_APP_WORD => 'images'.DIRECTORY_SEPARATOR.'word.png',
    UploadModelInterface::FILE_TYPE_APP_EXCEL => 'images'.DIRECTORY_SEPARATOR.'excel.png',
    UploadModelInterface::FILE_TYPE_APP_PDF => 'images'.DIRECTORY_SEPARATOR.'pdf.png',
    UploadModelInterface::FILE_TYPE_OTHER => 'images'.DIRECTORY_SEPARATOR.'other.png',
    UploadModelInterface::FILE_TYPE_TEXT => 'images'.DIRECTORY_SEPARATOR.'text.png',
];
