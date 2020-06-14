<?php
use yii\data\Pagination;
use yii\widgets\LinkPager;
use yii\helpers\Html;
use yii\widgets\DetailView;
use Itstructure\MFUploader\Module;
use Itstructure\MFUploader\assets\BaseAsset;
use Itstructure\MFUploader\models\Mediafile;
use Itstructure\MFUploader\models\album\Album;

/* @var $this yii\web\View */
/* @var $model Album */
/* @var $thumbnailModel Mediafile|null */
/* @var $mediafiles Mediafile[] */
/* @var $pages Pagination */

$this->title = $model->title;
$this->params['breadcrumbs'][] = [
    'label' => Module::t('album', ucfirst($model->getFileType($model->type)).' albums'),
    'url' => [
        $this->params['urlPrefix'].'index'
    ]
];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .file-item {
        margin-bottom: 15px;
    }
    h5 {
        font-weight: bold;
        padding: 5px;
    }
</style>

<div class="album-view">

    <p>
        <?php echo Html::a(Module::t('main', 'Update'), [
            $this->params['urlPrefix'].'update', 'id' => $model->id
        ], [
            'class' => 'btn btn-primary'
        ]) ?>

        <?php echo Html::a(Module::t('main', 'Delete'), [
            $this->params['urlPrefix'].'delete', 'id' => $model->id
        ], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Module::t('main', 'Are you sure you want to do this action?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'id',
                'label' => Module::t('main', 'ID')
            ],
            [
                'attribute' => 'title',
                'label' => Module::t('album', 'Title')
            ],
            [
                'attribute' => 'description',
                'label' => Module::t('album', 'Description')
            ],
            [
                'attribute' => 'type',
                'label' => Module::t('album', 'Type'),
                'value' => function($data) {
                    return Album::getAlbumTypes($data->type);
                }
            ],
            [
                'attribute' => 'created_at',
                'format' =>  ['date', 'dd.MM.Y HH:mm:ss'],
                'label' => Module::t('main', 'Created date')
            ],
            [
                'attribute' => 'updated_at',
                'format' =>  ['date', 'dd.MM.Y HH:mm:ss'],
                'label' => Module::t('main', 'Updated date')
            ],
        ],
    ]) ?>

    <?php if (($defaultThumbImage = $model->getDefaultThumbImage()) !== null): ?>
        <div class="row">
            <div class="col-md-4">
                <h5><?php echo Module::t('main', 'Thumbnail'); ?></h5>
                <?php echo $defaultThumbImage ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if(count($mediafiles) > 0): ?>
        <div class="row">
            <div class="col-md-12">

                <h5><?php echo Module::t('main', 'Existing files'); ?></h5>
                <div class="row">

                    <?php $i=0; ?>
                    <?php foreach ($mediafiles as $mediafile): ?>
                        <div class="col-md-6 file-item">
                            <?php $i+=1; ?>
                            <div class="media">
                                <div class="media-left" id="mediafile-container-<?php echo $i; ?>">
                                    <?php echo $mediafile->getPreview(BaseAsset::register($this)->baseUrl, 'existing', $mediafile->isImage() ? [
                                        'externalTag' => [
                                            'name' => 'a',
                                            'options' => [
                                                'href' => $mediafile->getViewUrl(),
                                                'target' => '_blank'
                                            ]
                                        ]
                                    ] : []); ?>
                                </div>
                                <div class="media-body">
                                    <h4 class="media-heading">
                                        <?php echo $mediafile->title ?>
                                    </h4>
                                    <div>
                                        <?php echo $mediafile->description ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
                <?php echo LinkPager::widget(['pagination' => $pages]) ?>

            </div>
        </div>
    <?php endif; ?>

</div>
