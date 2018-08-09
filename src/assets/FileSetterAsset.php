<?php

namespace Itstructure\MFUploader\assets;

/**
 * Class FileSetterAsset
 *
 * @package Itstructure\MFUploader\assets
 */
class FileSetterAsset extends BaseAsset
{
    public $css = [
        'css/filesetter.css',
    ];

    public $js = [
        'js/filesetter.js',
    ];

    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
        ModalAsset::class,
        MainAsset::class,
    ];
}
