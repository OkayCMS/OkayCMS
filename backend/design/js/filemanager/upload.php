<?php

try {
    if (!isset($config)) {
        $config = include 'config/config.php';
    }

    include 'include/utils.php';
    require_once 'include/okay_access.php';

    if (!function_exists('sendUploadErrorResponse')) {
        function sendUploadErrorResponse(string $message, int $statusCode = 200): void
        {
            $files = array();
            $names = $_FILES['files']['name'] ?? array();

            if (is_array($names)) {
                foreach ($names as $i => $name) {
                    $files[] = array(
                        'name' => $name,
                        'error' => $message,
                        'size' => $_FILES['files']['size'][$i] ?? 0,
                        'type' => $_FILES['files']['type'][$i] ?? '',
                    );
                }
            } elseif (is_string($names) && $names !== '') {
                $files[] = array(
                    'name' => $names,
                    'error' => $message,
                    'size' => $_FILES['files']['size'] ?? 0,
                    'type' => $_FILES['files']['type'] ?? '',
                );
            }

            if ($files === array()) {
                $files[] = array(
                    'name' => '',
                    'error' => $message,
                    'size' => 0,
                    'type' => '',
                );
            }

            $payload = json_encode(array('files' => $files));
            response(is_string($payload) ? $payload : '{"files":[]}', $statusCode, array(
                'Content-Type' => 'application/json',
            ))->send();
            exit;
        }
    }

    if ($_SESSION['RF']["verify"] != "RESPONSIVEfilemanager") {
        sendUploadErrorResponse(trans('forbidden') . AddErrorLocation(), 403);
    }

    include 'include/mime_type_lib.php';

    $ftp = ftp_con($config);

    if ($ftp) {
        $source_base = $config['ftp_base_folder'] . $config['upload_dir'];
        $thumb_base = $config['ftp_base_folder'] . $config['ftp_thumbs_dir'];
    } else {
        $source_base = $config['current_path'];
        $thumb_base = $config['thumbs_base_path'];
    }

    if (isset($_POST["fldr"])) {
        $_POST['fldr'] = str_replace('undefined', '', $_POST['fldr']);
    } else {
        return;
    }

    $fldr = normalizeFilemanagerUploadFolder($_POST['fldr']);

    if ($fldr === null) {
        sendUploadErrorResponse(trans('wrong path') . AddErrorLocation());
    }

    $storeFolder = $source_base . $fldr;
    $storeFolderThumb = $thumb_base . $fldr;

    $path = $storeFolder;
    $cycle = true;
    $max_cycles = 50;
    $i = 0;
    //GET config
    while ($cycle && $i < $max_cycles) {
        $i++;
        if ($path == $config['current_path']) {
            $cycle = false;
        }
        if (file_exists($path . "config.php")) {
            $configTemp = include $path . 'config.php';
            $config = array_merge($config, $configTemp);
            //TODO switch to array
            $cycle = false;
        }
        $path = fix_dirname($path) . '/';
    }

    require('UploadHandler.php');
    $messages = null;
    if (trans("Upload_error_messages") !== "Upload_error_messages") {
        $messages = trans("Upload_error_messages");
    }

    if (isset($_POST['url'])) {
        http_response_code(403);
        sendUploadErrorResponse(trans('forbidden') . AddErrorLocation(), 403);
    }

    if (empty($_FILES['files']['name'][0])) {
        sendUploadErrorResponse('No file was uploaded', 400);
    }


    if ($config['mime_extension_rename']) {
        $info = pathinfo($_FILES['files']['name'][0]);
        $mime_type = $_FILES['files']['type'][0];
        if (function_exists('mime_content_type')) {
            $mime_type = mime_content_type($_FILES['files']['tmp_name'][0]);
        } elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = $finfo === false ? false : finfo_file($finfo, $_FILES['files']['tmp_name'][0]);
        } else {
            $mime_type = get_file_mime_type($_FILES['files']['tmp_name'][0]);
        }
        if ($mime_type === false) {
            $mime_type = '';
        }
        $extension = get_extension_from_mime($mime_type);

        if ($extension == 'so' || $extension == '' || $mime_type == "text/troff") {
            $extension = $info['extension'] ?? '';
        }
        $filename = $info['filename'] . "." . $extension;
    } else {
        $filename = $_FILES['files']['name'][0];
    }
    $_FILES['files']['name'][0] = fix_filename($filename, $config);
    $extension = fix_strtolower(pathinfo($_FILES['files']['name'][0], PATHINFO_EXTENSION));
    if (!check_extension($extension, $config)) {
        sendUploadErrorResponse(trans('wrong extension') . AddErrorLocation());
    }
    if ($extension === 'svg') {
        $uploadedFile = $_FILES['files']['tmp_name'][0] ?? '';
        if (!is_string($uploadedFile) || !sanitizeFilemanagerSvg($uploadedFile)) {
            sendUploadErrorResponse(trans('wrong extension') . AddErrorLocation());
        }
    }

    if (!$_FILES['files']['type'][0]) {
        $_FILES['files']['type'][0] = $mime_type;
    }
    // LowerCase
    if ($config['lower_case']) {
        $_FILES['files']['name'][0] = fix_strtolower($_FILES['files']['name'][0]);
    }
    if (!checkresultingsize($_FILES['files']['size'][0])) {
        sendUploadErrorResponse(sprintf(trans('max_size_reached'), $config['MaxSizeTotal']) . AddErrorLocation());
    }

    $uploadConfig = array(
        'config' => $config,
        'storeFolder' => $storeFolder,
        'storeFolderThumb' => $storeFolderThumb,
        'ftp' => $ftp,
        'upload_dir' => dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $storeFolder,
        'upload_url' => $config['base_url'] . $config['upload_dir'] . $fldr,
        'mkdir_mode' => $config['folderPermission'],
        'max_file_size' => $config['MaxSizeUpload'] * 1024 * 1024,
        'correct_image_extensions' => true,
        'print_response' => false
    );

    if (!$config['ext_blacklist']) {
        $uploadConfig['accept_file_types'] = '/\.(' . implode('|', $config['ext']) . ')$/i';

        if ($config['files_without_extension']) {
            $uploadConfig['accept_file_types'] = '/((\.(' . implode('|', $config['ext']) . ')$)|(^[^.]+$))$/i';
        }
    } else {
        $uploadConfig['accept_file_types'] = '/\.(?!' . implode('|', $config['ext_blacklist']) . '$)/i';

        if ($config['files_without_extension']) {
            $uploadConfig['accept_file_types'] = '/((\.(?!' . implode('|', $config['ext_blacklist']) . '$))|(^[^.]+$))/i';
        }
    }

    if ($ftp) {
        if (!is_dir($config['ftp_temp_folder'])) {
            mkdir($config['ftp_temp_folder'], $config['folderPermission'], true);
        }

        if (!is_dir($config['ftp_temp_folder'] . "thumbs")) {
            mkdir($config['ftp_temp_folder'] . "thumbs", $config['folderPermission'], true);
        }

        $uploadConfig['upload_dir'] = $config['ftp_temp_folder'];
    }

    $upload_handler = new UploadHandler($uploadConfig, true, $messages);
} catch (Exception $e) {
    $return = array();

    if ($_FILES['files']) {
        foreach ($_FILES['files']['name'] as $i => $name) {
            $return[] = array(
                'name' => $name,
                'error' => $e->getMessage(),
                'size' => $_FILES['files']['size'][$i],
                'type' => $_FILES['files']['type'][$i]
            );
        }

        echo json_encode(array("files" => $return));
        return;
    }

    echo json_encode(array("error" => $e->getMessage()));
}
