<?php

namespace Itstructure\MFUploader\assets;

class UploadmanagerAsset extends BaseAsset
{
    public $css = [
        'css/uploadmanager.css',
    ];

    public $js = [
        'js/uploadmanager.js',
    ];

    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        MainAsset::class,
    ];
}
