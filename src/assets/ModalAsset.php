<?php

namespace Itstructure\MFUploader\assets;

/**
 * Class ModalAsset
 *
 * @package Itstructure\MFUploader\assets
 */
class ModalAsset extends BaseAsset
{
    public $css = [
        'css/modal.css',
    ];

    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
}
