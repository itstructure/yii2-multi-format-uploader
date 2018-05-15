<?php
use yii\helpers\ArrayHelper;
use Itstructure\MFUploader\Module;
use Itstructure\MFUploader\models\Mediafile;
use Itstructure\MFUploader\models\album\Album;
use Itstructure\MFUploader\widgets\FileSetter;
use Itstructure\MFUploader\interfaces\UploadModelInterface;

/* @var $this yii\web\View */
/* @var $model Album */
/* @var $albumType string */
/* @var $thumbnailModel Mediafile|null */
/* @var $ownerParams array */
?>

<div id="thumbnail-container">
    <?php echo $model->getDefaultThumbImage(); ?>
</div>
<?php echo FileSetter::widget(ArrayHelper::merge([
        'model' => $model,
        'attribute' => UploadModelInterface::FILE_TYPE_THUMB,
        'neededFileType' => UploadModelInterface::FILE_TYPE_THUMB,
        'buttonName' => Module::t('main', 'Set thumbnail'),
        'resetButtonName' => Module::t('main', 'Clear'),
        'options' => [
            'value' => ($thumbnailModel = $model->getThumbnailModel()) !== null ? $thumbnailModel->{FileSetter::INSERTED_DATA_ID} : null,
        ],
        'mediafileContainer' => '#thumbnail-container',
        'subDir' => strtolower($albumType)
    ], isset($ownerParams) && is_array($ownerParams) ? ArrayHelper::merge([
        'ownerAttribute' => UploadModelInterface::FILE_TYPE_THUMB
    ], $ownerParams) : [])
); ?>
