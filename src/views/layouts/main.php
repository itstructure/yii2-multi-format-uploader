<?php
use Itstructure\MFUploader\Module;
use yii\helpers\{Html, BaseUrl};

/** @var $this yii\web\View */
/** @var $content string */
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?php echo Yii::$app->language ?>">
    <head>
        <meta charset="<?php echo Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php echo Html::csrfMetaTags() ?>
        <title><?php echo Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>

        <header id="header">
            <div class="left">
                <?php echo Html::a(
                    '<span class="glyphicon glyphicon-file"></span> ' . Module::t('filemanager', 'File manager'),
                    $this->params['manager'] == 'filemanager' ? '#' : null === BaseUrl::previous(Module::BACK_URL_PARAM) ? Module::FILE_MANAGER_SRC : BaseUrl::previous(Module::BACK_URL_PARAM),
                    [
                        'class' => $this->params['manager'] == 'filemanager' ? 'btn btn-default active' : 'btn btn-success',
                    ])
                ?>
                <?php echo Html::a(
                    '<span class="glyphicon glyphicon-upload"></span> ' . Module::t('uploadmanager', 'Upload manager'),
                    $this->params['manager'] == 'uploadmanager' ? '#' : Module::UPLOAD_MANAGER_SRC,
                    [
                        'class' => $this->params['manager'] == 'uploadmanager' ? 'btn btn-default active' : 'btn btn-success',
                    ])
                ?>
            </div>
            <div class="right" role="popup">

            </div>
        </header>

        <?php echo $content; ?>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>