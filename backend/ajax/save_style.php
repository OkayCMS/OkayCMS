<?php

require_once 'configure.php';

if (!$managers->access('style_templates', $manager)) {
    exit();
}

// Проверка сессии для защиты от xss
if (!$request->checkSession()) {
    trigger_error('Session expired', E_USER_WARNING);
    exit();
}
$content = $request->post('content');
$style = $request->post('style');
$theme = $request->post('theme', 'string');

/*Сохранение стилей из админки*/
$file = $config->root_dir.'design/'.$theme.'/css/'.$style;

if (pathinfo($style, PATHINFO_EXTENSION) != 'css' || $file != realpath($file)) {
    exit();
}

if (is_file($file) && is_writable($file) && !is_file($config->root_dir.'design/'.$theme.'/locked')) {
    file_put_contents($file, $content);
}

$result = true;
$response->setContent(json_encode($result), RESPONSE_JSON);
$response->sendContent();
