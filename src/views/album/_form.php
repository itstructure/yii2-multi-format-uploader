<?php
use yii\data\Pagination;
use yii\widgets\ActiveForm;
use Itstructure\MFUploader\Module;
use Itstructure\MFUploader\models\Mediafile;
use Itstructure\MFUploader\models\album\Album;
use Itstructure\MFUploader\helpers\Html;
use Itstructure\FieldWidgets\{Fields, FieldType};

/* @var $this yii\web\View */
/* @var $model Album */
/* @var $mediafiles Mediafile[] */
/* @var $pages Pagination */
/* @var $albumType string */
/* @var $form yii\widgets\ActiveForm */
/* @var $ownerParams array */
?>

<style>
    h5 {
        border-top: 1px solid #8ca68c;
        font-weight: bold;
        padding: 5px;
    }
</style>

<div class="album-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">
            <?php echo Fields::widget([
                'fields' => [
                    [
                        'name' => 'title',
                        'type' => FieldType::FIELD_TYPE_TEXT,
                        'label' => Module::t('album', 'Title')
                    ],
                    [
                        'name' => 'description',
                        'type' => FieldType::FIELD_TYPE_TEXT_AREA,
                        'label' => Module::t('album', 'Description')
                    ],
                ],
                'model' => $model,
                'form'  => $form,
            ]) ?>
        </div>
    </div>

    <?php echo Html::activeHiddenInput($model, 'type', [
        'value' => $albumType
    ]); ?>

    <!-- Thumbnail begin -->
    <div class="row">
        <div class="col-md-4">
            <h5><?php echo Module::t('main', 'Thumbnail'); ?></h5>
            <?php echo $this->render('_thumbnail', [
                'model' => $model,
                'albumType' => $albumType,
                'ownerParams' => isset($ownerParams) && is_array($ownerParams) ? $ownerParams : null,
            ]) ?>
        </div>
    </div>
    <!-- Thumbnail end -->

    <!-- New files begin -->
    <div class="row">
        <div class="col-md-12">
            <h5><?php echo Module::t('main', 'New files'); ?></h5>
            <?php for ($i=1; $i < 5; $i++): ?>
                <?php echo $this->render('_new-mediafiles', [
                    'model' => $model,
                    'albumType' => $albumType,
                    'fileType' => $model->getFileType($albumType),
                    'ownerParams' => isset($ownerParams) && is_array($ownerParams) ? $ownerParams : null,
                    'number' => $i,
                ]) ?>
            <?php endfor; ?>
        </div>
    </div>
    <!-- New files end -->

    <!-- Existing files begin -->
    <?php if (!$model->isNewRecord): ?>
        <div class="row">
            <div class="col-md-12">
                <h5><?php echo Module::t('main', 'Existing files'); ?></h5>
                <?php echo $this->render('_existing-mediafiles', [
                    'model' => $model,
                    'mediafiles' => $mediafiles,
                    'pages' => $pages,
                    'albumType' => $albumType,
                    'fileType' => $model->getFileType($albumType),
                    'ownerParams' => isset($ownerParams) && is_array($ownerParams) ? $ownerParams : null,
                ]) ?>
            </div>
        </div>
    <?php endif; ?>
    <!-- Existing files end -->

    <div class="form-group">
        <?php echo Html::submitButton(Module::t('main', 'Save'), [
            'class' => 'btn btn-success',
            'style' => 'margin-top: 15px;'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
