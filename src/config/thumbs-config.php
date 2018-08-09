<?php

use Itstructure\MFUploader\Module;

/**
 * Thumbs config with their types and sizes.
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
return [
    Module::THUMB_ALIAS_SMALL => [
        'name' => 'Small size',
        'size' => [120, 80],
    ],
    Module::THUMB_ALIAS_MEDIUM => [
        'name' => 'Medium size',
        'size' => [300, 240],
    ],
    Module::THUMB_ALIAS_LARGE => [
        'name' => 'Large size',
        'size' => [800, 600],
    ],
];
