/** global: ActiveXObject */

/**
 * Serialize object to string.
 *
 * @param   {object} obj    - Object incoming
 * @returns {string}        - Object as Get params string
 */
function serializeParams(obj) {
    return Object.keys(obj).reduce(function(a,k) {a.push(k+'='+encodeURIComponent(obj[k]));return a},[]).join('&');
}

/**
 * AJAX function.
 *
 * @param {string} url           - Request URL
 * @param {string} method        - Request type ('post' || 'get')
 * @param {object} params        - Object with params (for files { name: 'sasha' (sended to $_POST[]), files: { custom_filename: element.files[0] } (sended to $_FILES[]))
 * @param {bool}   response_json - Type of response (JSON or not)
 * @param {func}   func_waiting  - Function while waiting
 * @param {func}   func_callback - Function on success result
 * @param {func}   func_error    - Function on error result
 * @param {func}   func_progress - Function on uploading progress
 */
function AJAX(url, method, params, response_json, func_waiting, func_callback, func_error, func_progress) {
    var xhr = null;

    try { // For: chrome, firefox, safari, opera, yandex, ...
        xhr = new XMLHttpRequest();
    } catch(e) {
        try { // For: IE6+
            xhr = new ActiveXObject("Microsoft.XMLHTTP");
        } catch(e1) { // if JS not supported or disabled
            console.log("Browser Not supported!");
            return;
        }
    }

    xhr.onreadystatechange = function() {

        // ready states:
        // 0: uninitialized
        // 1: loading
        // 2: loaded
        // 3: interactive
        // 4: complete

        if (xhr.readyState == 4) { // when result is ready

            var response_text = xhr.responseText;

            if (response_json) {
                try {
                    response_text = JSON.parse(response_text);
                } catch (e) { }
            }

            if (xhr.status === 200) { // on success
                if (typeof func_callback == 'function') {
                    func_callback(response_text);
                }
            } else { // on error
                if (typeof func_error == 'function') {
                    func_error(response_text, xhr);
                    console.log(xhr.status + ': ' + xhr.statusText);
                }
            }
        } else { // waiting for result
            if (typeof func_waiting == 'function') {
                func_waiting();
            }
        }
    }

    var data = null;

    if (params.files) {
        method = 'POST';

        data = new FormData();
        for (var index_param in params) {
            if (typeof params[index_param] == 'object') {
                for (var index_file in params[index_param]) {
                    data.append(index_file, params[index_param][index_file]);
                }
            } else {
                data.append(index_param, params[index_param]);
            }
        }

        if (typeof func_progress == 'function') {
            xhr.upload.onprogress = function(event) {
                // 'progress: ' + event.loaded + ' / ' + event.total;
                func_progress(event);
            }
        }
    } else {
        data = serializeParams(params);
    }

    method = method.toUpperCase();

    if (method == 'GET' && data) {
        url += '?' + data;
    }

    xhr.open(method, url, true);

    if ( ! params.files) {
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    }

    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xhr.send(data);
}

/**
 * Show status messages by popup.
 *
 * @param {element} container - Block container in which the text message will be inserted.
 * @param {string|array} data - Data with text message.
 * @param {bool} error        - Status of message (true - error, false - success).
 * @param {int} time (ms)     - Time delay before close popup (ms).
 */
function showPopup(container, data, error, time)
{
    var classPopup;

    if (error) {
        classPopup = 'popup error';
    } else {
        classPopup = 'popup success';
    }

    var content = '';

    if (jQuery.type(data) === 'string') {
        content = data;
    }

    if (jQuery.type(data) === 'object') {
        for (var key in data) {
            if (jQuery.type(data[key]) == 'array') {
                for (var index in data[key]) {
                    content += data[key][index] + '</br>';
                }
            } else {
                content += data[key] + '</br>';
            }
        }
    }

    container.html(
        '<div class="' + classPopup + '">' + content + '</div>'
    );

    if (!time && time != 0) {
        time = 2000;
    }

    if (time && time != 0) {
        setTimeout(function () {
            clearContainer(container);
        }, time);
    }
}

/**
 * Clear popup message.
 *
 * @param {element} container
 */
function clearContainer(container)
{
    container.html('');
}

/**
 * Ajax loader using progress-bar style.
 *
 * @param {element} container
 * @param {integer} delayTime
 */
function setAjaxLoader(container, delayTime)
{
    var initValue = 100;

    if (delayTime) {
        initValue = 0;
    }

    container.html(
        '<div class="progress">' +
            '<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="' + initValue + '" aria-valuemin="0" aria-valuemax="100" style="width: ' + initValue + '%;">' +
                '<span class="sr-only">100% Complete</span>' +
            '</div>' +
        '</div>');

    if (delayTime) {
        var progressBar = container.find('[role="progressbar"]');
        progressBar.on('load', progressBar.animate({width:100+'%'}).delay(delayTime));
    }
}

/**
 * Get preview according with the file type.
 *
 * @param {object} params.
 * Example {fileType: 'image/jpeg', fileUrl: '\uploads\images\post\...url to file.jpeg', baseUrl: '/assets/fc0cd220'}
 *
 * @returns {string}
 */
function getPreview(params) {

    if (jQuery.type(params) !== 'object') {
        var errorOptions = "Parameter 'options' must be an object!";
        console.log(errorOptions);
        alert(errorOptions);
        return '';
    }

    if (!params.fileType) {
        var errorFileType = "fileType is not defined!";
        console.log(errorFileType);
        alert(errorFileType);
        return '';
    }

    var fileType = params.fileType,
        fileUrl = '',
        baseUrl = '';

    if (params.fileUrl) {
        fileUrl = params.fileUrl;
    }

    if (params.baseUrl) {
        baseUrl = params.baseUrl;
    }

    /* Additional options */
    var customMainOptions = {},
        customSourceOptions = {},
        customTrackOptions = {},
        initSourceOptions = {},
        initTrackOptions = {};

    if (params.main && jQuery.type(params.main) === 'object') {
        customMainOptions = params.main;
    }

    if (params.source && jQuery.type(params.source) === 'object') {
        customSourceOptions = params.source;
    }

    if (params.track && jQuery.type(params.track) === 'object') {
        customTrackOptions = params.track;
    }

    switch (fileType.split('/')[0]) {
        case 'image':
            return '<img src="' + fileUrl + '" ' + objectParamsToHtml(customMainOptions) + '>';
            break;

        case 'audio':
            var initAudioOptions = {controls: 'controls'};
                initSourceOptions = {preload: 'auto'};
                initTrackOptions = {kind: 'subtitles'};
            return  '<audio controls ' + objectParamsToHtml(jsonMergeRecursive(customMainOptions, initAudioOptions)) + '>' +
                        '<source src="' + fileUrl + '" type="' + fileType + '" ' + objectParamsToHtml(jsonMergeRecursive(customSourceOptions, initSourceOptions)) + ' >' +
                        '<track ' + objectParamsToHtml(jsonMergeRecursive(customTrackOptions, initTrackOptions)) + '>' +
                    '</audio>';
            break;

        case 'video':
            var initVideoOptions = {controls: 'controls', width: 300, height: 240};
                initSourceOptions = {preload: 'auto'};
                initTrackOptions = {kind: 'subtitles'};
            return  '<video controls ' + objectParamsToHtml(jsonMergeRecursive(customMainOptions, initVideoOptions)) + '>' +
                        '<source src="' + fileUrl + '" type="' + fileType + '" ' + objectParamsToHtml(jsonMergeRecursive(customSourceOptions, initSourceOptions)) + ' >' +
                        '<track ' + objectParamsToHtml(jsonMergeRecursive(customTrackOptions, initTrackOptions)) + '>' +
                    '</video>';
            break;

        case 'text':
            return '<img src="' + baseUrl + '/images/text.png' + '" ' + objectParamsToHtml(customMainOptions) + '>';
            break;

        case 'application':
            if (strpos({str: fileType.split('/')[1], find: 'word', index: 1})) {
                return '<img src="' + baseUrl + '/images/word.png' + '" ' + objectParamsToHtml(customMainOptions) + '>';

            } else if (strpos({str: fileType.split('/')[1], find: 'excel', index: 1})) {
                return '<img src="' + baseUrl + '/images/excel.png' + '" ' + objectParamsToHtml(customMainOptions) + '>';

            } else if (fileType.split('/')[1] == 'pdf') {
                return '<img src="' + baseUrl + '/images/pdf.png' + '" ' + objectParamsToHtml(customMainOptions) + '>';

            } else {
                return '<img src="' + baseUrl + '/images/app.png' + '" ' + objectParamsToHtml(customMainOptions) + '>';
            }
            break;

        default:
            return '<img src="' + baseUrl + '/images/other.png' + '" ' + objectParamsToHtml(customMainOptions) + '>';
    }
}

/**
 * Analog for "strpos" php function.
 *
 * @param data
 * @returns {null}
 */
function strpos(data) {
    // Created by Mark Tali [webcodes.ru]
    // Example. Return 8, but if index > 2, then return null
    // strpos({str: 'Bla-bla-bla...', find: 'bla', index: 2});
    var haystack = data.str, needle = data.find, offset = 0;
    for (var i = 0; i < haystack.split(needle).length; i++) {
        var index = haystack.indexOf(needle, offset + (data.index != 1 ? 1 : 0));
        if (i == data.index - 1) return (index != -1 ? index : null); else offset = index;
    }
}

/**
 * Merge two json objects.
 *
 * @param json1
 * @param json2
 * @returns {{}}
 */
function jsonMergeRecursive(json1, json2) {
    var out = {};
    for(var k1 in json1) {
        if (json1.hasOwnProperty(k1)) out[k1] = json1[k1];
    }
    for(var k2 in json2) {
        if (json2.hasOwnProperty(k2)) {
            if(!out.hasOwnProperty(k2)) out[k2] = json2[k2];
            else if(
                (typeof out[k2] === 'object') && (out[k2].constructor === Object) &&
                (typeof json2[k2] === 'object') && (json2[k2].constructor === Object)
            ) out[k2] = json_merge_recursive(out[k2], json2[k2]);
        }
    }
    return out;
}

/**
 * Parse object params to the html string.
 *
 * @param objectParams
 * @returns {string}
 */
function objectParamsToHtml(objectParams)
{
    var options = '';
    for (var index in objectParams) {
        options += ' ' + index + '="'+ objectParams[index] + '"';
    }
    return options;
}
