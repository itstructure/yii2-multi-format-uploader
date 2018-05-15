$(document).ready(function() {

    /**
     * Handler to catch press on insert button.
     *
     * @param e
     */
    function frameInsertHandler(e) {

        var modal = $(this).parents('[role="filemanager-modal"]');

        $(this).contents().find(".redactor").on('click', '[role="insert"]', function(e) {
            e.preventDefault();

            var fileInputs = $(this).parents('[role="file-inputs"]'),
                mediafileContainer = $(modal.attr("data-mediafile-container")),
                titleContainer = $(modal.attr("data-title-container")),
                descriptionContainer = $(modal.attr("data-description-container")),
                insertedData = modal.attr("data-inserted-data"),
                mainInput = $("#" + modal.attr("data-input-id"));

            mainInput.trigger("fileInsert", [insertedData]);

            if (mediafileContainer) {
                var fileType = fileInputs.attr("data-file-type"),
                    fileTypeShort = fileType.split('/')[0],
                    fileUrl = fileInputs.attr("data-file-url"),
                    baseUrl = fileInputs.attr("data-base-url"),
                    previewOptions = {
                        fileType: fileType,
                        fileUrl: fileUrl,
                        baseUrl: baseUrl
                    };

                if (fileTypeShort === 'image' || fileTypeShort === 'video' || fileTypeShort === 'audio'){
                    previewOptions.main = {width: fileInputs.attr("data-original-preview-width")};
                }

                var preview = getPreview(previewOptions);
                mediafileContainer.html(preview);

                /* Set title */
                if (titleContainer){
                    var titleValue = $(fileInputs.contents().find('[role="file-title"]')).val();
                    titleContainer.html(titleValue);
                }

                /* Set description */
                if (descriptionContainer){
                    var descriptionValue = $(fileInputs.contents().find('[role="file-description"]')).val();
                    descriptionContainer.html(descriptionValue);
                }
            }

            mainInput.val(fileInputs.attr("data-file-" + insertedData));
            modal.modal("hide");
        });
    }

    /**
     * Load file manager.
     */
    $('[role="filemanager-load"]').on("click", function(e) {
        e.preventDefault();

        var modal = $('[role="filemanager-modal"][data-btn-id="'+$(this).attr('id')+'"]'),
            srcToFiles = modal.attr("data-src-to-files"),
            owner = modal.attr("data-owner"),
            ownerId = modal.attr("data-owner-id"),
            ownerAttribute = modal.attr("data-owner-attribute");

        var paramsArray = [];
        var paramsQuery = '';

        if (owner){
            paramsArray.owner = owner;
        }

        if (ownerId){
            paramsArray.ownerId = ownerId;
        }

        if (ownerAttribute){
            paramsArray.ownerAttribute = ownerAttribute;
        }

        for (var index in paramsArray){
            var paramString = index + '=' + paramsArray[index];
            paramsQuery += paramsQuery == '' ? paramString : '&' + paramString;
        }

        if (paramsQuery != ''){
            srcToFiles += '?' + paramsQuery;
        }

        var iframe = $('<iframe src="' + srcToFiles + '" frameborder="0" role="filemanager-frame"></iframe>');

        iframe.on("load", frameInsertHandler);
        modal.find(".modal-body").html(iframe);
        modal.modal("show");
    });

    /**
     * Clear value in main input.
     */
    $('[role="clear-input"]').on("click", function(e) {
        e.preventDefault();

        $("#" + $(this).attr("data-clear-element-id")).val("");
        $($(this).attr("data-mediafile-container")).empty();
    });
});
