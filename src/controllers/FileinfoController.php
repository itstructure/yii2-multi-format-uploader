<?php

namespace Itstructure\MFUploader\controllers;

use yii\web\{Controller, BadRequestHttpException};
use yii\filters\{VerbFilter, AccessControl};
use Itstructure\MFUploader\Module;
use Itstructure\MFUploader\models\Mediafile;
use Itstructure\MFUploader\traits\ResponseTrait;

/**
 * Class FileinfoController
 * Controller class to get file information using view template.
 *
 * @property Module $module
 *
 * @package Itstructure\MFUploader\controllers
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class FileinfoController extends Controller
{
    use ResponseTrait;

    /**
     * Initialize.
     */
    public function init()
    {
        $this->enableCsrfValidation = $this->module->enableCsrfValidation;

        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => $this->module->accessRoles,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Get file info.
     *
     * @throws BadRequestHttpException
     *
     * @return string
     */
    public function actionIndex()
    {
        $id = \Yii::$app->request->post('id');

        if (empty($id) || !is_numeric($id)) {
            throw new BadRequestHttpException(Module::t('actions', 'Parameter id must be sent.'));
        }

        /** @var Mediafile $model */
        $model = Mediafile::findOne($id);

        // Set url to set file by Java script.
        if ($model->isImage()) {
            $urlToSetFile = $model->getThumbUrl(Module::THUMB_ALIAS_MEDIUM);
            if (empty($urlToSetFile)) {
                $urlToSetFile = $model->url;
            }

        } else {
            $urlToSetFile = $model->url;
        }

        // Set width to set file by Java script.
        if ($model->isImage()) {
            $widthToSetFile = isset($this->module->thumbsConfig[Module::THUMB_ALIAS_MEDIUM]) ?
                $this->module->thumbsConfig[Module::THUMB_ALIAS_MEDIUM]['size'][0] : Module::ORIGINAL_PREVIEW_WIDTH;

        } elseif ($model->isAudio() || $model->isVideo()) {
            $widthToSetFile = Module::ORIGINAL_PREVIEW_WIDTH;

        } else {
            $widthToSetFile = null;
        }

        return $this->renderAjax('index', [
            'model' => $model,
            'urlToSetFile' => $urlToSetFile,
            'widthToSetFile' => $widthToSetFile,
            'fileAttributeName' => $this->module->fileAttributeName,
            'updateUrl' => Module::getUpdateUrl($this->module->defaultStorageType),
            'deleteUrl' => Module::getDeleteUrl($this->module->defaultStorageType),
        ]);
    }
}
