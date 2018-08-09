<?php

use yii\helpers\Html;
use Itstructure\MFUploader\Module;
use Itstructure\MFUploader\models\Mediafile;
use Itstructure\MFUploader\assets\FilemanagerAsset;

/** @var $this yii\web\View */
/** @var $model Mediafile */
/** @var $fileAttributeName string */
/* @var $updateUrl string */
/* @var $deleteUrl string */
/* @var $urlToSetFile string */
/* @var $widthToSetFile int|null */

$bundle = FilemanagerAsset::register($this);
?>

<div class="media">
    <div <?php if ($model->isImage() || $model->isApp()): ?>class="media-left"<?php endif; ?> >
        <?php echo $model->getPreview($bundle->baseUrl, 'fileinfo') ?>
    </div>
    <div class="media-body">
        <h4 class="media-heading"><?php echo Module::t('filemanager', 'File information') ?></h4>
        <h6><?php echo Module::t('filemanager', 'File type') ?> <span class="label label-default"><?php echo $model->type ?></span></h6>
        <h6><?php echo Module::t('filemanager', 'Created') ?> <span class="label label-default"><?php echo Yii::$app->formatter->asDatetime($model->created_at) ?></span></h6>
        <h6><?php echo Module::t('filemanager', 'Updated') ?> <span class="label label-default"><?php echo Yii::$app->formatter->asDatetime($model->updated_at) ?></span></h6>
        <h6><?php echo Module::t('filemanager', 'File size') ?> <span class="label label-default"><?php echo $model->getFileSize() ?></span></h6>
    </div>
</div>

<div class="inputs" role="file-inputs"
     data-file-id="<?php echo $model->id ?>"
     data-file-url="<?php echo $urlToSetFile ?>"
     data-file-type="<?php echo $model->type ?>"
     data-file-attribute-name="<?php echo $fileAttributeName ?>"
     data-update-url="<?php echo $updateUrl ?>"
     data-delete-url="<?php echo $deleteUrl ?>"
     data-confirm-message="<?php echo Module::t('main', 'Are you sure you want to do this action?') ?>"
     data-is-image="<?php echo $model->isImage() ?>"
     data-base-url="<?php echo $bundle->baseUrl ?>"
     data-original-preview-width="<?php echo $widthToSetFile ?>">

    <?php if ($model->isImage()): ?>
        <div class="input-group input-group-sm">
            <span class="input-group-addon" id="file-alt"><?php echo Module::t('filemanager', 'Alt') ?></span>
            <input type="text" class="form-control" placeholder="<?php echo Module::t('filemanager', 'Alt') ?>"
                   aria-describedby="file-alt" name="alt" role="file-alt" value="<?php echo $model->alt ?>">
        </div>
    <?php endif; ?>

    <div class="input-group input-group-sm">
        <span class="input-group-addon" id="file-title"><?php echo Module::t('filemanager', 'Title') ?></span>
        <input type="text" class="form-control" placeholder="<?php echo Module::t('filemanager', 'Title') ?>"
               aria-describedby="file-title" name="title" role="file-title" value="<?php echo $model->title ?>">
    </div>

    <div class="input-group input-group-sm">
        <span class="input-group-addon" id="file-description"><?php echo Module::t('filemanager', 'Description') ?></span>
        <textarea class="form-control" placeholder="<?php echo Module::t('filemanager', 'Description') ?>"
               aria-describedby="file-description" name="description" role="file-description"><?php echo $model->description ?></textarea>
    </div>

    <div class="input-group input-group-sm">
        <span class="input-group-addon" id="file-new"><?php echo Module::t('filemanager', 'New file') ?></span>
        <input type="file" class="form-control" placeholder="<?php echo Module::t('filemanager', 'New file') ?>"
               aria-describedby="file-new" name="<?php echo $fileAttributeName ?>" role="file-new" multiple>
    </div>

    <?php echo Html::button(Module::t('main', 'Update'), [
        'role' => 'update',
        'class' => 'btn btn-warning btn-sm'
    ]) ?>

    <?php echo Html::button(Module::t('main', 'Insert'), [
        'role' => 'insert',
        'class' => 'btn btn-success btn-sm'
    ]) ?>

    <?php echo Html::button(Module::t('main', 'Delete'), [
        'role' => 'delete',
        'class' => 'btn btn-danger btn-sm'
    ]) ?>

</div>


