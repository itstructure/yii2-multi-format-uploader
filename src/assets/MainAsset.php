<?php

namespace Itstructure\MFUploader\assets;

class MainAsset extends BaseAsset
{
    public $css = [
        'css/main.css',
    ];

    public $js = [
        'js/main.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
    ];
}
