<?php
use yii\helpers\Html;
use Itstructure\MFUploader\Module;
?>

<tr role="file-block">
    <td>
        <table width="100%">
            <tbody>
            <tr>
                <td width="128">
                    {preview}
                </td>
                <td width="10">
                </td>
                <td>
                    <div>
                        {size}
                    </div>
                    <div>
                        {title}
                    </div>
                    <div role="progress-block">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                                <span class="sr-only">100% Complete</span>
                            </div>
                        </div>
                    </div>
                </td>
                <td width="10">
                </td>
                <td width="200">
                    <div role="button-block-upload">
                        <?php echo Html::button('<span class="glyphicon glyphicon-upload"></span> ' . Module::t('uploadmanager', 'Upload'), [
                            'class' => 'btn btn-primary btn-sm',
                            'id' => 'upload-button-{fileNumber}',
                            'role' => 'upload-file',
                            'data-file-number' => '{fileNumber}'
                        ]) ?>
                        <?php echo Html::button('<span class="glyphicon glyphicon-ban-circle"></span> ' . Module::t('uploadmanager', 'Cancel'), [
                            'class' => 'btn btn-info btn-sm',
                            'id' => 'cancel-button-{fileNumber}',
                            'role' => 'cancel-upload',
                            'data-file-number' => '{fileNumber}'
                        ]) ?>
                    </div>
                    <div role="button-block-delete">
                        <?php echo Html::button('<span class="glyphicon glyphicon-trash"></span> ' . Module::t('uploadmanager', 'Delete'), [
                            'class' => 'btn btn-danger btn-sm',
                            'id' => 'delete-button-{fileNumber}',
                            'role' => 'delete-file-button',
                            'data-file-number' => '{fileNumber}',
                            'data-file-id' => ''
                        ]) ?>
                        <?php echo Html::checkbox(Module::t('uploadmanager', 'Delete'), false, [
                            'id' => 'delete-checkbox-{fileNumber}',
                            'role' => 'delete-file-checkbox',
                        ]) ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    <div class="input-group input-group-sm {altVisibility}">
                        <span class="input-group-addon" id="file-alt"><?php echo Module::t('filemanager', 'Alt') ?></span>
                        <input type="text" class="form-control" placeholder="<?php echo Module::t('filemanager', 'Alt') ?>"
                               aria-describedby="file-alt" name="alt" role="file-alt">
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon" id="file-title"><?php echo Module::t('filemanager', 'Title') ?></span>
                        <input type="text" class="form-control" placeholder="<?php echo Module::t('filemanager', 'Title') ?>"
                               aria-describedby="file-title" name="title" role="file-title">
                    </div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon" id="file-description"><?php echo Module::t('filemanager', 'Description') ?></span>
                        <textarea class="form-control" placeholder="<?php echo Module::t('filemanager', 'Description') ?>"
                                  aria-describedby="file-description" name="description" role="file-description"></textarea>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
