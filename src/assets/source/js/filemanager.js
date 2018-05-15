$(document).ready(function() {
    window.fileInfoContainer = $('[role="fileinfo"]');
    window.fileManagerContainer = $('[role="filemanager"]');
    window.fileManagerModalContainer = $(window.frameElement).parents('[role="filemanager-modal"]');

    /**
     * Get file information function.
     *
     * @param id
     * @param isAjaxLoader
     */
    function getFileInfo(id, isAjaxLoader) {

        var popupElement = $('[role="popup"]');
        var url = window.fileManagerContainer.attr("data-url-info");
        var params = {
            _csrf: window.yii.getCsrfToken(),
            id: id
        };

        AJAX(url, 'POST', params, false, function () {
            if (isAjaxLoader){
                setAjaxLoader(popupElement);
            }
        }, function(data) {
            window.fileInfoContainer.html(data);
            if (isAjaxLoader){
                clearContainer(popupElement);
            }

        }, function(data, xhr) {
            showPopup(popupElement, 'Server Error!', true, 4000);
        });
    }

    /**
     * Get file information by click on the media file item.
     */
    $('[role="item"]').on("click", function(e) {
        e.preventDefault();

        $("div.item").removeClass("active");
        $(this).addClass("active");

        var id = $(this).attr("data-key");

        getFileInfo(id, true);
    });

    /**
     * Update file information.
     */
    window.fileInfoContainer.on("click", '[role="update"]', function(e) {
        e.preventDefault();

        var fileInputs = $('[role="file-inputs"]'),
            url = fileInputs.attr("data-update-src"),
            baseUrl = fileInputs.attr("data-base-url"),
            popupElement = $('[role="popup"]'),
            subDir = window.fileManagerModalContainer.attr("data-sub-dir"),
            neededFileType = window.fileManagerModalContainer.attr("data-needed-file-type"),
            params = {
                _csrf: window.yii.getCsrfToken(),
                id: fileInputs.attr("data-file-id"),
                title: $('[role="file-title"]').val(),
                description: $('[role="file-description"]').val()
            };

        if (fileInputs.attr("data-is-image") == true){
            params.alt = $('[role="file-alt"]').val();
        }

        if (subDir && subDir != ''){
            params.subDir = subDir;
        }

        if (neededFileType){
            params.neededFileType = neededFileType;
        }

        var fileInputField = $('[role="file-new"]');
        if (fileInputField[0].files[0]) {
            var fileAttributeName = fileInputs.attr("data-file-attribute-name"),
                paramsFiles = {};
            paramsFiles[fileAttributeName] = fileInputField[0].files[0];
            params.files = paramsFiles;
        }

        AJAX(url, 'POST', params, true, function () {
            setAjaxLoader(popupElement);

        }, function(data) {

            if (data.meta.status == 'success'){
                showPopup(popupElement, data.meta.message, false);
                getFileInfo(params.id, false);

                if (data.data.files && data.data.files[0]){

                    var file = data.data.files[0],
                        fileType = file.type,
                        fileTypeShort = fileType.split('/')[0],
                        previewOptions = {fileType: fileType, baseUrl: baseUrl};

                    if (fileTypeShort == 'image'){
                        previewOptions.fileUrl = file.thumbnailUrl;
                    } else {
                        previewOptions.fileUrl = file.url;
                    }

                    if (fileTypeShort != 'image' && fileTypeShort != 'video' && fileTypeShort != 'audio'){
                        previewOptions.main = {width: 50};
                    }

                    var preview = getPreview(previewOptions) + '<span class="checked glyphicon glyphicon-ok"></span>',
                        itemSubclass = fileTypeShort == 'image' || fileTypeShort == 'video' ? 'floated' : 'cleared';

                    if (fileTypeShort != 'image' && fileTypeShort != 'video'){
                        preview += ' ' + params.title;
                    }

                    $('[data-key="' + params.id + '"]').html(preview).attr('class', 'item ' + itemSubclass + ' active');
                }
            } else {
                showPopup(popupElement, data.data.errors, true, 4000);
            }

        }, function(data, xhr) {
            showPopup(popupElement, data.message, true, 4000);
            getFileInfo(params.id);
        });
    });

    /**
     * Delete file.
     */
    window.fileInfoContainer.on("click", '[role="delete"]', function(e) {
        e.preventDefault();

        var fileInputs = $('[role="file-inputs"]'),
            url = fileInputs.attr("data-delete-src"),
            confirmMessage = fileInputs.attr("data-confirm-message"),
            popupElement = $('[role="popup"]'),
            params = {
                _csrf: window.yii.getCsrfToken(),
                id: fileInputs.attr("data-file-id")
            };

        if (confirm(confirmMessage)) {
            AJAX(url, 'POST', params, true, function () {
                setAjaxLoader(popupElement);

            }, function(data) {

                if (data.meta.status == 'success'){
                    $('[data-key="' + params.id + '"]').fadeOut();
                    clearContainer(window.fileInfoContainer);
                    showPopup(popupElement, data.meta.message, false);
                } else {
                    showPopup(popupElement, data.meta.message, true, 4000);
                }

            }, function(data, xhr) {
                showPopup(popupElement, data.message, true, 4000);
            });
        }
    });
});
