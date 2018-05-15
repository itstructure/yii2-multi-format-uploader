<?php
namespace Itstructure\MFUploader\traits;

use Yii;
use yii\web\NotFoundHttpException;
use yii\base\{InvalidConfigException, UnknownMethodException};
use Itstructure\MFUploader\Module;
use Itstructure\MFUploader\models\{Mediafile, OwnersMediafiles};
use Itstructure\MFUploader\models\upload\BaseUpload;
use Itstructure\MFUploader\components\{LocalUploadComponent, S3UploadComponent};
use Itstructure\MFUploader\interfaces\{UploadComponentInterface, UploadModelInterface};

/**
 * Trait DeleteFilesTrait
 *
 * @property UploadComponentInterface[]|LocalUploadComponent[]|S3UploadComponent[] $tmpUploadComponents Upload components to delete files according with their different types.
 *
 * @package Itstructure\MFUploader\traits
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
trait MediaFilesTrait
{
    /**
     * Upload components to delete files according with their different types.
     *
     * @var UploadComponentInterface[]|LocalUploadComponent[]|S3UploadComponent[]
     */
    protected $tmpUploadComponents = [];

    /**
     * Delete mediafiles from owner.
     * @param string $owner
     * @param int $ownerId
     * @param Module $module
     * @return void
     */
    protected function deleteMediafiles(string $owner, int $ownerId, Module $module): void
    {
        $mediafileIds = OwnersMediafiles::getMediaFilesQuery([
            'owner' => $owner,
            'ownerId' => $ownerId,
        ])
        ->select('id')
        ->all();

        $mediafileIds = array_map(function ($data) {return $data->id;}, $mediafileIds);

        $this->deleteMediafileEntry($mediafileIds, $module);
    }

    /**
     * Find the media model entry.
     * @param int $id
     * @throws UnknownMethodException
     * @throws NotFoundHttpException
     * @return Mediafile
     */
    protected function findMediafileModel(int $id): Mediafile
    {
        $modelObject = new Mediafile();

        if (!method_exists($modelObject, 'findOne')){
            $class = (new\ReflectionClass($modelObject));
            throw new UnknownMethodException('Method findOne does not exists in ' . $class->getNamespaceName() . '\\' . $class->getShortName().' class.');
        }

        $result = call_user_func([
            $modelObject,
            'findOne',
        ], $id);

        if ($result !== null) {
            return $result;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Delete mediafile record with files.
     * @param array|int|string $id
     * @param Module $module
     * @throws InvalidConfigException
     * @return bool|int
     */
    protected function deleteMediafileEntry($id, Module $module)
    {
        if (is_array($id)){
            $i = 0;
            foreach ($id as $item) {
                if (!$this->deleteMediafileEntry((int)$item, $module)){
                    return false;
                }
                $i += 1;
            }
            return $i;

        } else {
            $mediafileModel = $this->findMediafileModel((int)$id);

            switch ($mediafileModel->storage) {
                case Module::STORAGE_TYPE_LOCAL:
                    $this->setComponentIfNotIsset($mediafileModel->storage, 'local-upload-component', $module);
                    break;

                case Module::STORAGE_TYPE_S3:
                    $this->setComponentIfNotIsset($mediafileModel->storage, 's3-upload-component', $module);
                    break;

                default:
                    throw new InvalidConfigException('Unknown type of the file storage');
            }

            /** @var UploadModelInterface|BaseUpload $deleteModel */
            $deleteModel = $this->tmpUploadComponents[$mediafileModel->storage]->setModelForDelete($mediafileModel);

            if ($deleteModel->delete() === 0){
                return false;
            }

            return 1;
        }
    }

    /**
     * Set tmp upload component if not isset.
     * @param string $storage
     * @param string $componentName
     * @param Module $module
     * @return void
     */
    private function setComponentIfNotIsset(string $storage, string $componentName, Module $module): void
    {
        if (!isset($this->tmpUploadComponents[$storage])){
            $this->tmpUploadComponents[$storage] = $module->get($componentName);
        }
    }
}
