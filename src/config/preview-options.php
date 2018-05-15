<?php

use Itstructure\MFUploader\Module;
use Itstructure\MFUploader\interfaces\UploadModelInterface;

/**
 * Preview options for som types of mediafiles according with their location.
 *
 * Keys of the first level - the file types, for which the following location parameters are specified:
 *      existing - if view template renders files, which are exist;
 *      fileinfo - for "fileinfo/index" view template of "filemanager";
 *      fileitem - for "_fileItem" view template of "filemanager".
 * Each location parametr(array key) must contain mainTag attribute.
 * mainTag - main preview html tag. Can contain different html tag options.
 *           And also can contain "alias" from the number of module constant aliases:
 *           default, original, small, medium, large.
 * Else each location parametr(array key) can contain addition attributes:
 *      leftTag - tag that is located to the left of the "mainTag";
 *      rightTag - tag that is located to the right of the "mainTag";
 *      externalTag - tag in which the mainTag with leftTag and rightTag are embedded.
 * Importantly! Addition tag keys must contain the next attributes:
 * name - the name of addition tag.
 * options - html options of addition tag.
 * You can insert configurations values of addition tags through the third parameter
 * of getPreview() function from Mediafile model.
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
return [
    UploadModelInterface::FILE_TYPE_IMAGE => [
        'existing' => [
            'mainTag' => [
                'alias' => Module::MEDIUM_THUMB_ALIAS
            ]
        ],
        'fileinfo' => [
            'mainTag' => [
                'alias' => Module::DEFAULT_THUMB_ALIAS
            ]
        ],
        'fileitem' => [
            'mainTag' => [
                'alias' => Module::DEFAULT_THUMB_ALIAS
            ]
        ],
    ],
    UploadModelInterface::FILE_TYPE_AUDIO => [
        'existing' => [
            'mainTag' => [
                'width' => Module::ORIGINAL_PREVIEW_WIDTH
            ]
        ],
        'fileinfo' => [
            'mainTag' => [
                'width' => Module::ORIGINAL_PREVIEW_WIDTH
            ]
        ],
        'fileitem' => [
            'mainTag' => [
                'width' => Module::ORIGINAL_PREVIEW_WIDTH
            ]
        ],
    ],
    UploadModelInterface::FILE_TYPE_VIDEO => [
        'existing' => [
            'mainTag' => [
                'width' => Module::ORIGINAL_PREVIEW_WIDTH,
                'height' => Module::ORIGINAL_PREVIEW_HEIGHT,
            ]
        ],
        'fileinfo' => [
            'mainTag' => [
                'width' => Module::ORIGINAL_PREVIEW_WIDTH,
                'height' => Module::ORIGINAL_PREVIEW_HEIGHT,
            ]
        ],
        'fileitem' => [
            'mainTag' => [
                'width' => Module::ORIGINAL_PREVIEW_WIDTH,
                'height' => Module::ORIGINAL_PREVIEW_HEIGHT,
            ]
        ],
    ],
    UploadModelInterface::FILE_TYPE_APP => [
        'fileitem' => [
            'mainTag' => [
                'width' => Module::SCANTY_PREVIEW_SIZE,
            ]
        ],
    ],
    UploadModelInterface::FILE_TYPE_TEXT => [
        'fileitem' => [
            'mainTag' => [
                'width' => Module::SCANTY_PREVIEW_SIZE,
            ]
        ],
    ],
    UploadModelInterface::FILE_TYPE_OTHER => [
        'fileitem' => [
            'mainTag' => [
                'width' => Module::SCANTY_PREVIEW_SIZE,
            ]
        ],
    ],
];
