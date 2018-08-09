<?php

namespace Itstructure\MFUploader\controllers\upload;

use Yii;
use yii\filters\{AccessControl, ContentNegotiator, VerbFilter};
use yii\base\{InvalidConfigException, UnknownMethodException, InvalidArgumentException};
use yii\web\{Controller, Request, Response, UploadedFile, BadRequestHttpException, NotFoundHttpException, ForbiddenHttpException};
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;
use Itstructure\MFUploader\Module;
use Itstructure\MFUploader\assets\UploadmanagerAsset;
use Itstructure\MFUploader\models\Mediafile;
use Itstructure\MFUploader\traits\{ResponseTrait, MediaFilesTrait};
use Itstructure\MFUploader\interfaces\{UploadComponentInterface, UploadModelInterface};

/**
 * Class CommonUploadController
 * Common upload controller class to upload files.
 *
 * @property UploadComponentInterface $uploadComponent
 * @property UploadModelInterface $uploadModel
 * @property Module $module
 *
 * @package Itstructure\MFUploader\controllers\upload
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
abstract class CommonUploadController extends Controller
{
    use ResponseTrait, MediaFilesTrait;

    /**
     * @var string|array the configuration for creating the serializer that formats the response data.
     */
    public $serializer = 'yii\rest\Serializer';

    /**
     * @var UploadModelInterface
     */
    private $uploadModel;

    /**
     * @return UploadComponentInterface
     */
    abstract protected function getUploadComponent(): UploadComponentInterface;

    /**
     * Initialize.
     */
    public function init()
    {
        $this->enableCsrfValidation = $this->module->enableCsrfValidation;
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
                'denyCallback' => function () {
                    \Yii::$app->response->format = Response::FORMAT_JSON;
                    throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
                }
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => $this->verbs(),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        return $this->serializeData($result);
    }

    /**
     * @return array
     */
    public function verbs()
    {
        return [
            'save' => ['POST'],
            'delete' => ['POST'],
        ];
    }

    /**
     * Set upload model.
     *
     * @param UploadModelInterface $model
     *
     * @return void
     */
    public function setUploadModel(UploadModelInterface $model): void
    {
        $this->uploadModel = $model;
    }

    /**
     * Returns upload model.
     *
     * @return UploadModelInterface
     */
    public function getUploadModel(): UploadModelInterface
    {
        return $this->uploadModel;
    }

    /**
     * Send new file to upload it.
     *
     * @throws BadRequestHttpException
     *
     * @return array
     */
    public function actionSend()
    {
        try {
            $this->uploadModel = $this->getUploadComponent()->setModelForSave($this->setMediafileModel());

            return $this->actionSave(Yii::$app->request);

        } catch (InvalidConfigException|UnknownMethodException|NotFoundHttpException|InvalidArgumentException|
            AwsException|S3Exception|\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update existing file.
     *
     * @throws BadRequestHttpException
     *
     * @return array
     */
    public function actionUpdate()
    {
        try {
            $request = Yii::$app->request;

            if (empty($request->post('id'))) {
                return $this->getFailResponse(Module::t('actions', 'Error to save file.'), [
                    'errors' => Module::t('actions', 'Parameter id must be sent.')
                ]);
            }

            $this->uploadModel = $this->getUploadComponent()->setModelForSave(
                $this->setMediafileModel($request->post('id'))
            );

            return $this->actionSave($request);

        } catch (InvalidConfigException|UnknownMethodException|NotFoundHttpException|InvalidArgumentException|
            AwsException|S3Exception|\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Delete the media model entry with files.
     *
     * @throws BadRequestHttpException
     *
     * @return array
     */
    public function actionDelete()
    {
        try {
            $deleted = $this->deleteMediafileEntry(Yii::$app->request->post('id'), $this->module);

            if (false === $deleted) {
                return $this->getFailResponse(
                    Module::t('actions', 'Error to delete file.')
                );
            }

            return $this->getSuccessResponse(
                Module::t('actions', 'Deleted {0} files.', [$deleted])
            );

        } catch (InvalidConfigException|\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Provides upload or update file.
     *
     * @throws InvalidConfigException
     * @throws BadRequestHttpException
     *
     * @param $request
     *
     * @return array
     */
    private function actionSave($request)
    {
        if (!($request instanceof Request)) {
            throw new InvalidArgumentException('Param $request must be instanceof yii\web\Request.');
        }

        if (null === $this->uploadModel) {
            throw new InvalidConfigException('Upload model is not defined.');
        }

        $this->uploadModel->setAttributes($request->post(), false);
        $this->uploadModel->setFile(UploadedFile::getInstanceByName($this->module->fileAttributeName));

        if (!$this->uploadModel->save()) {
            return $this->getFailResponse(Module::t('actions', 'Error to save file.'), [
                'errors' => $this->uploadModel->getErrors()
            ]);
        }

        if ($this->uploadModel->getMediafileModel()->isImage()) {
            $this->uploadModel->createThumbs();
        }

        $response = [];
        $response['files'][] = $this->getUploadResponse();

        return $this->getSuccessResponse(Module::t('actions', 'File saved.'), $response);
    }

    /**
     * Serializes the specified data.
     * The default implementation will create a serializer based on the configuration given by [[serializer]].
     * It then uses the serializer to serialize the given data.
     *
     * @param mixed $data the data to be serialized
     *
     * @return mixed the serialized data.
     */
    private function serializeData($data)
    {
        return Yii::createObject($this->serializer)->serialize($data);
    }

    /**
     * Response with uploaded file data.
     *
     * @return array
     */
    private function getUploadResponse(): array
    {
        $mediafileModel = $this->uploadModel->getMediafileModel();

        return [
            'id'            => $this->uploadModel->getId(),
            'url'           => $mediafileModel->url,
            'thumbnailUrl'  => $mediafileModel->getDefaultThumbUrl(UploadmanagerAsset::register($this->view)->baseUrl),
            'name'          => $mediafileModel->filename,
            'type'          => $mediafileModel->type,
            'size'          => $mediafileModel->size,
        ];
    }

    /**
     * Returns an intermediate model for working with the main.
     *
     * @param int|null $id
     *
     * @throws UnknownMethodException
     * @throws NotFoundHttpException
     *
     * @return Mediafile
     */
    private function setMediafileModel(int $id = null): Mediafile
    {
        if (null === $id) {
            return new Mediafile();
        } else {
            return $this->findMediafileModel($id);
        }
    }
}
