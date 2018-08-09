<?php

namespace Itstructure\MFUploader\assets;

/**
 * Class FilemanagerAsset
 *
 * @package Itstructure\MFUploader\assets
 */
class FilemanagerAsset extends BaseAsset
{
    public $css = [
        'css/filemanager.css',
    ];

    public $js = [
        'js/filemanager.js',
    ];

    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        MainAsset::class,
    ];
}
