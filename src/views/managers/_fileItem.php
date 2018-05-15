<?php
use Itstructure\MFUploader\models\Mediafile;

/* @var $model Mediafile */
/* @var $baseUrl string */
?>

<?php echo $model->getPreview($baseUrl, 'fileitem') . '<span class="checked glyphicon glyphicon-ok"></span>'; ?>
<?php if ($model->isAudio() || $model->isText() || $model->isApp()): ?>
    <?php echo $model->title; ?>
<?php endif; ?>
