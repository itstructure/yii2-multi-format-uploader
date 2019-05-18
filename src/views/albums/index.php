<?php

use yii\helpers\{Html, Url};
use yii\grid\GridView;
use Itstructure\MFUploader\Module;
use Itstructure\MFUploader\models\album\Album;

/* @var $this yii\web\View */
/* @var $searchModel Itstructure\MFUploader\models\album\AlbumSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('album', ucfirst($searchModel->getFileType($searchModel->type)).' albums');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="album-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a(Module::t('album', 'Create album'), [
            $this->params['urlPrefix'].'create'
        ], [
            'class' => 'btn btn-success'
        ]) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => Module::t('main', 'ID'),
                'value' => function($data) {
                    /* @var $data Album */
                    return Html::a(
                        Html::encode($data->id),
                        Url::to([
                            $this->params['urlPrefix'].'view',
                            'id' => $data->id
                        ])
                    );
                },
                'format' => 'raw',
            ],
            [
                'label' => Module::t('main', 'Thumbnail'),
                'value' => function($data) {
                    /* @var $data Album */
                    $defaultThumbImage = $data->getDefaultThumbImage();
                    return !empty($defaultThumbImage) ? Html::a($defaultThumbImage, Url::to([
                        $this->params['urlPrefix'].'view',
                        'id' => $data->id
                    ])) : '';
                },
                'format' => 'raw',
            ],
            [
                'label' => Module::t('album', 'Title'),
                'value' => function($data) {
                    /* @var $data Album */
                    return Html::a(
                        Html::encode($data->title),
                        Url::to([
                            $this->params['urlPrefix'].'view',
                            'id' => $data->id
                        ])
                    );
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'description',
                'label' =>  Module::t('album', 'Description'),
            ],
            [
                'attribute' => 'type',
                'label' =>  Module::t('album', 'Type'),
                'value' => function($data) {
                    /* @var Album */
                    return Album::getAlbumTypes($data->type);
                }
            ],
            [
                'attribute' => 'created_at',
                'label' => Module::t('main', 'Created date'),
                'format' =>  ['date', 'dd.MM.YY HH:mm:ss'],
            ],
            [
                'attribute' => 'updated_at',
                'label' => Module::t('main', 'Updated date'),
                'format' =>  ['date', 'dd.MM.Y HH:mm:ss'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Module::t('main', 'Actions'),
                'template' => '{view} {update} {delete}',
                'urlCreator'=>function($action, $model, $key, $index){
                    return Url::to([
                        $this->params['urlPrefix'].$action,
                        'id' => $model->id
                    ]);
                }
            ],
        ],
    ]); ?>
</div>
