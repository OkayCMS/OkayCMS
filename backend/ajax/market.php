<?php

use Okay\Entities\CategoriesEntity;

require_once 'configure.php';

// Проверка сессии для защиты от xss
if (!$request->checkSession()) {
    trigger_error('Session expired', E_USER_WARNING);
    exit();
}

if (!$managers->access('categories',  $manager)) {
    exit();
}

/** @var CategoriesEntity $categoriesEntity */
$categoriesEntity = $entityFactory->get(CategoriesEntity::class);

/*Выборка категорий для Я.Маркета из файла*/
$result = new \stdClass();
$module = $request->post('module');
$module = (!$module ? $request->get('module') : $module);
switch ($module) {
    case 'search_market': {
        $keyword = $request->get('query');
        $keywords = explode(' ', $keyword);
        $categories = $categoriesEntity->getMarket($keyword);

        $suggestions = array();
        foreach ($categories as $cats) {
            $suggestion = new \stdClass();
            $suggestion->data = $cats;
            $suggestion->value = $cats;
            $suggestions[] = $suggestion;
        }
        $result->query = $keyword;
        $result->suggestions = $suggestions;
        break;
    }
}

$response->setContent(json_encode($result), RESPONSE_JSON);
$response->sendContent();



