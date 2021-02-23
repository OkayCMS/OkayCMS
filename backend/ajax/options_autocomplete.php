<?php

use Okay\Entities\FeaturesValuesEntity;

require_once 'configure.php';

if (!$managers->access('products', $manager)) {
    exit();
}
$limit = 100;

/*Принимаем строку запроса*/
$keyword = $request->get('query', 'string');
$featureId = $request->get('feature_id', 'string');

/** @var FeaturesValuesEntity $featuresValuesEntity */
$featuresValuesEntity = $entityFactory->get(FeaturesValuesEntity::class);

$featuresValues = $featuresValuesEntity->find([
    'feature_id' => $featureId,
    'keyword'    => $keyword
]);

$suggestions = [];
foreach ($featuresValues as $fv) {
    $suggestion = new \stdClass();
    $suggestion->value = "{$fv->value}";
    $suggestion->data = $fv;
    $suggestions[] = $suggestion;
}

$result = new \stdClass;
$result->query = $keyword;
$result->suggestions = $suggestions;
$response->setContent(json_encode($result), RESPONSE_JSON);
$response->sendContent();
