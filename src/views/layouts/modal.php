<?php

/** @var string $srcToFiles Src to get files by filemanager. */
/** @var int $btnId */
/** @var int $inputId */
/** @var string $mediafileContainer In this container will be inserted selected mediafile. */
/** @var string $titleContainer In this container will be inserted title of selected mediafile. */
/** @var string $descriptionContainer In this container will be inserted description of selected mediafile. */
/** @var string $insertedData This data will be inserted in to the input field. */
/** @var string $owner Owner name (post, article, page e.t.c.). */
/** @var int $ownerId Owner id. */
/** @var string $ownerAttribute Owner attribute (thumbnail, image e.t.c.). */
/** @var string $neededFileType Needed file type for validation (thumbnail, image e.t.c.). */
/** @var string $subDir Subdirectory to upload files. */
?>

<div role="filemanager-modal" class="modal" tabindex="-1"
     data-src-to-files="<?php echo $srcToFiles ?>"
     data-btn-id="<?php echo $btnId ?>"
     data-input-id="<?php echo $inputId ?>"
     data-mediafile-container="<?php echo isset($mediafileContainer) ? $mediafileContainer : '' ?>"
     data-title-container="<?php echo isset($titleContainer) ? $titleContainer : '' ?>"
     data-description-container="<?php echo isset($descriptionContainer) ? $descriptionContainer : '' ?>"
     data-inserted-data="<?php echo isset($insertedData) ? $insertedData : '' ?>"
     data-owner="<?php echo $owner ?>"
     data-owner-id="<?php echo $ownerId ?>"
     data-owner-attribute="<?php echo $ownerAttribute ?>"
     data-needed-file-type="<?php echo $neededFileType ?>"
     data-sub-dir="<?php echo $subDir ?>"
>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body"></div>
        </div>
    </div>
</div>