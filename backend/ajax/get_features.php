<?php

use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesEntity;

require_once 'configure.php';

if (!$managers->access('products', $manager)) {
    exit();
}

/*Принимаем данные о товаре и категории*/
$categoryId = $request->get('category_id', 'integer');
$productId = $request->get('product_id', 'integer');

/** @var FeaturesEntity $featuresEntity */
$featuresEntity = $entityFactory->get(FeaturesEntity::class);

/** @var FeaturesValuesEntity $featuresValuesEntity */
$featuresValuesEntity = $entityFactory->get(FeaturesValuesEntity::class);

if (!empty($categoryId)) {
    $features = $featuresEntity->find(['category_id'=>$categoryId]);
} else {
    $features = $featuresEntity->find();
}

/*Выборка значений свойств*/
$featuresValues = [];
if (!empty($productId)) {
    foreach ($featuresValuesEntity->find(['product_id'=>$productId]) as $fv) {
        $featuresValues[$fv->feature_id][] = $fv;
    }
}

foreach ($features as $f) {
    if (isset($featuresValues[$f->id])) {
        $f->values = $featuresValues[$f->id];
    } else {
        $f->values = [
            ['value'=>'', 'id'=>'']
        ];
    }
}

$response->setContent(json_encode($features), RESPONSE_JSON);
$response->sendContent();
