<?php

use Okay\Entities\ProductsEntity;
use Okay\Entities\ImagesEntity;
use Okay\Core\Image;

require_once 'configure.php';

if (!$managers->access('products', $manager)) {
    exit();
}

/*Поиск товаров*/
$keyword = $request->post('query', 'string');
$filter = $request->post('filter');

/** @var ProductsEntity $productsEntity */
$productsEntity = $entityFactory->get(ProductsEntity::class);

/** @var ProductsEntity $imagesEntity */
$imagesEntity = $entityFactory->get(ImagesEntity::class);

/** @var Image $imagesCore */
$imagesCore = $DI->get(Image::class);

$productFields = [
    'id',
    'name',
    'main_image_id',
];
$imagesIds = [];
$products = [];
if (empty($filter)) {
    $filter = [];
}
if (!isset($filter['limit'])) {
    $filter['limit'] = 10;
}
foreach ($productsEntity->cols($productFields)->find(['keyword' => $keyword] + $filter) as $product) {
    $products[$product->id] = $product;
    $imagesIds[] = $product->main_image_id;
}

foreach ($imagesEntity->find(['id' => $imagesIds]) as $image) {
    if (isset($products[$image->product_id])) {
        $products[$image->product_id]->image = $image->filename;
    }
}

$suggestions = [];
foreach ($products as $product) {
    if (!empty($product->image)) {
        $product->image = $imagesCore->getResizeModifier($product->image, 35, 35);
    }

    $suggestion = new stdClass();
    $suggestion->value = $product->name;
    $suggestion->data = $product;
    $suggestions[] = $suggestion;
}

$result = new stdClass;
$result->query = $keyword;
$result->suggestions = $suggestions;
$response->setContent(json_encode($result), RESPONSE_JSON);
$response->sendContent();

