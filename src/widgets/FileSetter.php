<?php
namespace Itstructure\MFUploader\widgets;

use Yii;
use yii\helpers\{Html, Url};
use yii\widgets\InputWidget;
use Itstructure\MFUploader\Module;
use Itstructure\MFUploader\assets\FileSetterAsset;

/**
 * Class FileSetter
 *
 * Example 1 (for thumbnail):
 *
 * Container to display selected thumbnail.
 * <div id="thumbnail-container">
 *  <?php if (isset($thumbnailModel) && $thumbnailModel instanceof Mediafile): ?>
 *      <img src="<?php echo $thumbnailModel->getThumbUrl(Module::DEFAULT_THUMB_ALIAS) ?>">
 *  <?php endif; ?>
 * </div>
 *
 * <?php echo FileSetter::widget([
 *    'model' => $model,
 *    'attribute' => UploadModelInterface::FILE_TYPE_THUMB,
 *    'neededFileType' => UploadModelInterface::FILE_TYPE_THUMB,
 *    'buttonName' => Module::t('main', 'Set thumbnail'),
 *    'mediafileContainer' => '#thumbnail-container',
 *    'owner' => 'post',
 *    'ownerId' => {current owner id, post id, page id e.t.c.},
 *    'ownerAttribute' => UploadModelInterface::FILE_TYPE_THUMB,
 *    'subDir' => 'post'
 * ]); ?>
 *
 *
 * Example 2 (for image):
 *
 * $number - number of the file (Can be rendered by cycle)
 *
 * Container to display selected image.
 * <div class="media">
 *      <div id="mediafile-container-new<?php if (isset($number)): ?>-<?php echo $number; ?><?php endif; ?>">
 *      </div>
 *      <div class="media-body">
 *          <h4 id="title-container-new<?php if (isset($number)): ?>-<?php echo $number; ?><?php endif; ?>" class="media-heading"></h4>
 *          <div id="description-container-new<?php if (isset($number)): ?>-<?php echo $number; ?><?php endif; ?>"></div>
 *      </div>
 * </div>
 *
 * <?php echo FileSetter::widget([
 *    'model' => $model,
 *    'attribute' => UploadModelInterface::FILE_TYPE_IMAGE,
 *    'neededFileType' => UploadModelInterface::FILE_TYPE_IMAGE,
 *    'buttonName' => Module::t('main', 'Set image'),
 *    'options' => [
 *       'id' => Html::getInputId($model, UploadModelInterface::FILE_TYPE_IMAGE) . (isset($number) ? '-new-' . $number : '')
 *    ],
 *    'mediafileContainer' => '#mediafile-container-new' . (isset($number) ? '-' . $number : ''),
 *    'titleContainer' => '#title-container-new' . (isset($number) ? '-' . $number : ''),
 *    'descriptionContainer' => '#description-container-new' . (isset($number) ? '-' . $number : ''),
 *    'owner' => 'post',
 *    'ownerId' => {current owner id, post id, page id e.t.c.},
 *    'ownerAttribute' => UploadModelInterface::FILE_TYPE_IMAGE,
 *    'subDir' => 'post'
 * ]); ?>
 *
 * @property string|null $owner Owner name (post, article, page e.t.c.).
 * @property int|null $ownerId Owner id.
 * @property string|null $ownerAttribute Owner attribute (thumbnail, image e.t.c.).
 * @property string|null $neededFileType Needed file type for validation (thumbnail, image e.t.c.).
 * @property string $subDir Subdirectory to upload files.
 * @property string $template Template to display widget elements.
 * @property string $buttonHtmlTag Button html tag.
 * @property string $buttonName Button name.
 * @property array $buttonOptions Button html options.
 * @property string $resetButtonHtmlTag Reset button html tag.
 * @property string $resetButtonName Reset button name.
 * @property array $resetButtonOptions Reset button html options.
 * @property string $deleteBoxName Delete box name (text).
 * @property string $deleteBoxAttribute Delete box attribute.
 * @property array $deleteBoxOptions Delete box html options.
 * @property bool $deleteBoxDisplay Display or not delete box.
 * @property string $mediafileContainer In this container will be inserted selected mediafile.
 * @property string $titleContainer In this container will be inserted title of selected mediafile.
 * @property string $descriptionContainer In this container will be inserted description of selected mediafile.
 * @property string $callbackBeforeInsert JS function. That will be called before insert file data in to the input.
 * @property string $insertedData This data will be inserted in to the input field.
 * @property string $srcToFiles Src to get files by filemanager.
 *
 * @package Itstructure\MFUploader\widgets
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class FileSetter extends InputWidget
{
    /**
     * Owner name (post, article, page e.t.c.).
     * @var string|null
     */
    public $owner = null;

    /**
     * Owner id.
     * @var int|null
     */
    public $ownerId = null;

    /**
     * Owner attribute (thumbnail, image e.t.c.).
     * @var string|null
     */
    public $ownerAttribute = null;

    /**
     * Needed file type for validation (thumbnail, image e.t.c.).
     * @var string|null
     */
    public $neededFileType = null;

    /**
     * Subdirectory to upload files.
     * @var string
     */
    public $subDir = '';

    /**
     * Template to display widget elements.
     * @var string
     */
    public $template = '<div class="input-group">{input}<span class="input-group-btn">{button}{reset-button}</span><span class="delete-box">{delete-box}</span></div>';

    /**
     * Button html tag.
     * @var string
     */
    public $buttonHtmlTag = 'button';

    /**
     * Button name.
     * @var string
     */
    public $buttonName = 'Browse';

    /**
     * Button html options.
     * @var array
     */
    public $buttonOptions = [];

    /**
     * Reset button html tag.
     * @var string
     */
    public $resetButtonHtmlTag = 'button';

    /**
     * Reset button name.
     * @var string
     */
    public $resetButtonName = 'Clear';

    /**
     * Reset button html options.
     * @var array
     */
    public $resetButtonOptions = [];

    /**
     * Delete box name (text).
     * @var string
     */
    public $deleteBoxName = 'Delete';

    /**
     * Delete box attribute.
     * @var string
     */
    public $deleteBoxAttribute = 'delete[]';

    /**
     * Delete box html options.
     * @var array
     */
    public $deleteBoxOptions = [];

    /**
     * Display or not delete box.
     * @var bool
     */
    public $deleteBoxDisplay = false;

    /**
     * Optional, if set, in container will be inserted selected mediafile.
     * @var string|null
     */
    public $mediafileContainer = null;

    /**
     * Optional, if set, in container will be inserted title of selected mediafile.
     * @var string|null
     */
    public $titleContainer = null;

    /**
     * Optional, if set, in container will be inserted description of selected mediafile.
     * @var string|null
     */
    public $descriptionContainer = null;

    /**
     * JS function. That will be called before insert file data in to the input.
     * @var string
     */
    public $callbackBeforeInsert = '';

    /**
     * This data will be inserted in to the input field.
     * @var string
     */
    public $insertedData = self::INSERTED_DATA_ID;
    
    /**
     * Src to get files by filemanager.
     * @var string
     */
    public $srcToFiles  = Module::FILE_MANAGER_SRC;

    /**
     * Data, which will be inserted in to the file input.
     */
    const INSERTED_DATA_ID = 'id';
    const INSERTED_DATA_URL = 'url';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->options['class'])) {
            $this->options['class'] = 'form-control';
        }

        if (empty($this->buttonOptions['id'])) {
            $this->buttonOptions['id'] = $this->options['id'] . '-btn';
        }

        if (empty($this->buttonOptions['class'])) {
            $this->buttonOptions['class'] = 'btn btn-default';
        }

        if (empty($this->resetButtonOptions['class'])) {
            $this->resetButtonOptions['class'] = 'btn btn-default';
        }

        $this->buttonOptions['role'] = 'filemanager-load';
        $this->resetButtonOptions['role'] = 'clear-input';
        $this->resetButtonOptions['data-clear-element-id'] = $this->options['id'];
        $this->resetButtonOptions['data-mediafile-container'] = $this->mediafileContainer;
    }

    /**
     * Run widget.
     */
    public function run()
    {
        $replace = [];

        if ($this->hasModel()) {
            $replace['{input}'] = Html::activeHiddenInput($this->model, $this->attribute, $this->options);
        } else {
            $replace['{input}'] = Html::hiddenInput($this->name, $this->value, $this->options);
        }

        $replace['{button}'] = Html::tag($this->buttonHtmlTag, $this->buttonName, $this->buttonOptions);
        $replace['{reset-button}'] = Html::tag($this->resetButtonHtmlTag, $this->resetButtonName, $this->resetButtonOptions);
        $replace['{delete-box}'] = $this->deleteBoxDisplay ? Html::checkbox($this->deleteBoxAttribute, false, $this->deleteBoxOptions).' '.$this->deleteBoxName : '';

        FileSetterAsset::register($this->view);

        if (!empty($this->callbackBeforeInsert)) {
            $this->view->registerJs('
                $("#' . $this->options['id'] . '").on("fileInsert", ' . $this->callbackBeforeInsert . ');'
            );
        }

        $modal = $this->renderFile('@mfuploader/views/layouts/modal.php', [
            'inputId' => $this->options['id'],
            'btnId' => $this->buttonOptions['id'],
            'srcToFiles' => Url::to([$this->srcToFiles]),
            'mediafileContainer' => $this->mediafileContainer,
            'titleContainer' => $this->titleContainer,
            'descriptionContainer' => $this->descriptionContainer,
            'insertedData' => $this->insertedData,
            'owner' => $this->owner,
            'ownerId' => $this->ownerId,
            'ownerAttribute' => $this->ownerAttribute,
            'neededFileType' => $this->neededFileType,
            'subDir' => $this->subDir,
        ]);

        return strtr($this->template, $replace) . $modal;
    }

    /**
     * Give ability of configure view to the module class.
     * @return \yii\base\View|\yii\web\View
     */
    public function getView()
    {
        $module = Yii::$app->controller->module;

        if (method_exists($module, 'getView')) {
            return $module->getView();
        }

        return parent::getView();
    }
}