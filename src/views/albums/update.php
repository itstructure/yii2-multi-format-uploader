<?php
use yii\data\Pagination;
use Itstructure\MFUploader\Module;
use Itstructure\MFUploader\models\Mediafile;
use Itstructure\MFUploader\models\album\Album;

/* @var $this yii\web\View */
/* @var $model Album */
/* @var $mediafiles Mediafile[] */
/* @var $pages Pagination */
/* @var $albumType string */
/* @var $ownerParams array */

$this->title = Module::t('album', 'Update '.$model->getFileType($albumType).' album') . ': ' . $model->title;
$this->params['breadcrumbs'][] = [
    'label' => Module::t('album', ucfirst($model->getFileType($albumType)).' albums'),
    'url' => [
        'index'
    ]
];
$this->params['breadcrumbs'][] = [
    'label' => $model->title,
    'url' => [
        'view', 'id' => $model->id
    ]
];
$this->params['breadcrumbs'][] = Module::t('main', 'Update');
?>
<div class="album-update">

    <?php echo $this->render('_form', [
        'model' => $model,
        'mediafiles' => $mediafiles,
        'pages' => $pages,
        'albumType' => $albumType,
        'ownerParams' => $ownerParams,
    ]) ?>

</div>
