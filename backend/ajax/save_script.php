<?php

require_once 'configure.php';

if (!$managers->access('scripts', $manager)) {
    exit();
}

// Проверка сессии для защиты от xss
if (!$request->checkSession()) {
    trigger_error('Session expired', E_USER_WARNING);
    exit();
}
$content = $request->post('content');
$script = $request->post('script');
$theme = $request->post('theme', 'string');

/*Сохранение скриптов из админки*/
$file = $config->root_dir.'design/'.$theme.'/js/'.$script;

if (pathinfo($script, PATHINFO_EXTENSION) != 'js' || $file != realpath($file)) {
    exit();
}

if(is_file($file) && is_writable($file) && !is_file($config->root_dir.'design/'.$theme.'/locked')) {
    file_put_contents($file, $content);
}

$result = true;
$response->setContent(json_encode($result), RESPONSE_JSON);
$response->sendContent();
