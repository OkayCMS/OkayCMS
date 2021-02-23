<?php

require_once 'configure.php';

if (!$managers->access('file_templates', $manager)) {
    exit();
}

// Проверка сессии для защиты от xss
if (!$request->checkSession()) {
    trigger_error('Session expired', E_USER_WARNING);
    exit();
}
$content = $request->post('content');
$template = $request->post('template');
$theme = $request->post('theme', 'string');

if ($request->post("email")) {
    $template = "email/{$template}";
}

/*Сохранение файлов шаблона из админки*/
$file = $config->root_dir.'design/'.$theme.'/html/'.$template;

if (pathinfo($template, PATHINFO_EXTENSION) != 'tpl' || $file != realpath($file)) {
    exit();
}

if (is_file($file) && is_writable($file) && !is_file($config->root_dir.'design/'.$theme.'/locked')) {
    file_put_contents($file, $content);
}

$result = true;
$response->setContent(json_encode($result), RESPONSE_JSON);
$response->sendContent();
