<?php

$config = include 'config/config.php';

include 'include/utils.php';
require_once 'include/okay_access.php';
include 'include/mime_type_lib.php';

if ($_SESSION['RF']["verify"] != "RESPONSIVEfilemanager") {
    response(trans('forbidden') . AddErrorLocation(), 403)->send();
    exit;
}

$relativePath = normalizeFilemanagerRelativePath($_POST['path']);
if ($relativePath === null) {
    response(trans('wrong path') . AddErrorLocation(), 400)->send();
    exit;
}

$name = fix_filename($_POST['name'], $config);
if ($name === '' || !checkRelativePath($name) || strpos($name, '/') !== false || strpos($name, '\\') !== false) {
    response(trans('wrong path') . AddErrorLocation(), 400)->send();
    exit;
}

$ftp = ftp_con($config);

if ($ftp) {
    $path = $config['ftp_base_url'] . $config['upload_dir'] . $relativePath;
} else {
    $resolvedPath = resolveFilemanagerPath($config['current_path'], $relativePath);
    if ($resolvedPath === null) {
        http_response_code(400);
        exit(trans('wrong path') . AddErrorLocation());
    }
    $path = rtrim($resolvedPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
}

$info = pathinfo($name);
$extension = $info['extension'] ?? '';

if (!check_extension($extension, $config)) {
    response(trans('wrong extension') . AddErrorLocation(), 400)->send();
    exit;
}

$file_name = $info['basename'];
$file_ext = $extension;
$file_path = $path . $name;


// make sure the file exists
if ($ftp) {
    header('Content-Type: application/octet-stream');
    header("Content-Transfer-Encoding: Binary");
    header("Content-disposition: attachment; filename=\"" . $file_name . "\"");
    readfile($file_path);
} elseif (is_file($file_path) && is_readable($file_path)) {
    if (!file_exists($path . $name)) {
        response(trans('File_Not_Found') . AddErrorLocation(), 404)->send();
        exit;
    }

    $size = filesize($file_path);
    $file_name = rawurldecode($file_name);


    if (function_exists('mime_content_type')) {
        $mime_type = mime_content_type($file_path);
    } elseif (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = $finfo === false ? false : finfo_file($finfo, $file_path);
    } else {
        $mime_type = get_file_mime_type($file_path);
    }
    if ($mime_type === false) {
        $mime_type = 'application/octet-stream';
    }


    @ob_end_clean();
    if (ini_get('zlib.output_compression')) {
        ini_set('zlib.output_compression', 'Off');
    }
    header('Content-Type: ' . $mime_type);
    header('Content-Disposition: attachment; filename="' . $file_name . '"');
    header("Content-Transfer-Encoding: binary");
    header('Accept-Ranges: bytes');

    if (isset($_SERVER['HTTP_RANGE'])) {
        list($a, $range) = explode("=", $_SERVER['HTTP_RANGE'], 2);
        list($range) = explode(",", $range, 2);
        list($range, $range_end) = explode("-", $range);
        $range = intval($range);
        if (!$range_end) {
            $range_end = $size - 1;
        } else {
            $range_end = intval($range_end);
        }

        $new_length = $range_end - $range + 1;
        header("HTTP/1.1 206 Partial Content");
        header("Content-Length: $new_length");
        header("Content-Range: bytes $range-$range_end/$size");
    } else {
        $new_length = $size;
        header("Content-Length: " . $size);
    }

    $chunksize = 1 * (1024 * 1024);
    $bytes_send = 0;

    if ($file = fopen($file_path, 'r')) {
        if (isset($_SERVER['HTTP_RANGE'])) {
            fseek($file, $range);
        }

        while (
            !feof($file) &&
            (!connection_aborted()) &&
            ($bytes_send < $new_length)
        ) {
            $buffer = fread($file, $chunksize);
            if ($buffer === false) {
                break;
            }
            echo $buffer;
            flush();
            $bytes_send += strlen($buffer);
        }
        fclose($file);
    } else {
        die('Error - can not open file.');
    }

    die();
} else {
    // file does not exist
    header("HTTP/1.0 404 Not Found");
}

exit;
