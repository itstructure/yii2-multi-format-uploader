<?php

namespace Itstructure\MFUploader\controllers;

use yii\helpers\BaseUrl;
use yii\data\{ActiveDataProvider, Pagination};
use yii\base\InvalidArgumentException;
use yii\filters\{VerbFilter, AccessControl};
use yii\web\{Controller, BadRequestHttpException};
use Itstructure\MFUploader\Module;
use Itstructure\MFUploader\models\{OwnerMediafile, Mediafile};

/**
 * Class ManagerController
 * Manager controller class to display the next managers:
 * 1. To view and select available files.
 * 2. To upload files.
 *
 * @property Module $module
 *
 * @package Itstructure\MFUploader\controllers
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class ManagerController extends Controller
{
    /**
     * Initialize.
     */
    public function init()
    {
        $this->layout = '@'.Module::MODULE_NAME.'/views/layouts/main';

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
                    'filemanager' => ['GET'],
                    'uploadmanager' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * Get filemanager with uploaded files.
     *
     * @throws BadRequestHttpException
     *
     * @return string
     */
    public function actionFilemanager()
    {
        try {
            $request = \Yii::$app->request;

            $requestParams = [];

            if ((null !== $request->get('owner') && null !== $request->get('ownerId'))) {
                $requestParams['owner'] = $request->get('owner');
                $requestParams['ownerId'] = $request->get('ownerId');
            }

            if ((null !== $request->get('ownerAttribute') && null !== $request->get('ownerAttribute'))) {
                $requestParams['ownerAttribute'] = $request->get('ownerAttribute');
            }

            if (count($requestParams) > 0) {
                $query = OwnerMediafile::getMediaFilesQuery($requestParams)->orWhere([
                    'not in', 'id', OwnerMediafile::find()->select('mediafileId')->asArray()
                ]);
            } else {
                $query = Mediafile::find()->where([
                    'not in', 'id', OwnerMediafile::find()->select('mediafileId')->asArray()
                ])->orderBy('id DESC');
            }

            $pagination = new Pagination([
                'defaultPageSize' => 12,
                'totalCount' => $query->count(),
            ]);

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => $pagination
            ]);

            BaseUrl::remember($request->getAbsoluteUrl(), Module::BACK_URL_PARAM);

            return $this->render('filemanager', [
                'dataProvider' => $dataProvider,
                'pagination' => $pagination,
                'manager' => 'filemanager',
            ]);
        } catch (\Exception|InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Get uploadmanager for uploading files.
     *
     * @throws BadRequestHttpException
     *
     * @return string
     */
    public function actionUploadmanager()
    {
        try {
            return $this->render('uploadmanager', [
                'manager' => 'uploadmanager',
                'fileAttributeName' => $this->module->fileAttributeName,
                'sendUrl' => Module::getSendUrl($this->module->defaultStorageType),
                'deleteUrl' => Module::getDeleteUrl($this->module->defaultStorageType),
            ]);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e->getCode());
        }
    }
}
