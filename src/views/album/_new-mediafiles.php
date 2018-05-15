<?php
use yii\helpers\{ArrayHelper, Html};
use Itstructure\MFUploader\Module;
use Itstructure\MFUploader\models\album\Album;
use Itstructure\MFUploader\widgets\FileSetter;

/* @var $this yii\web\View */
/* @var $model Album */
/* @var $albumType string */
/* @var $fileType string */
/* @var $ownerParams array */
/* @var $number int */
?>

<div class="media">
    <div class="media-left" id="mediafile-container-new<?php if (isset($number)): ?>-<?php echo $number; ?><?php endif; ?>">
    </div>
    <div class="media-body">
        <h4 id="title-container-new<?php if (isset($number)): ?>-<?php echo $number; ?><?php endif; ?>" class="media-heading"></h4>
        <div id="description-container-new<?php if (isset($number)): ?>-<?php echo $number; ?><?php endif; ?>"></div>
    </div>
</div>

<?php echo FileSetter::widget(ArrayHelper::merge([
        'model' => $model,
        'attribute' => $fileType.'[]',
        'neededFileType' => $fileType,
        'buttonName' => Module::t('main', 'Set '.$fileType),
        'resetButtonName' => Module::t('main', 'Clear'),
        'options' => [
            'id' => Html::getInputId($model, $fileType) . (isset($number) ? '-new-' . $number : '')
        ],
        'mediafileContainer' => '#mediafile-container-new' . (isset($number) ? '-' . $number : ''),
        'titleContainer' => '#title-container-new' . (isset($number) ? '-' . $number : ''),
        'descriptionContainer' => '#description-container-new' . (isset($number) ? '-' . $number : ''),
        'subDir' => strtolower($albumType)
    ], isset($ownerParams) && is_array($ownerParams) ? ArrayHelper::merge([
        'ownerAttribute' => $fileType
    ], $ownerParams) : [])
); ?>
