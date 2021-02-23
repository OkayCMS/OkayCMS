<?php


use Okay\Core\Managers;
use Okay\Core\Response;
use Okay\Entities\ManagersEntity;
use Okay\Admin\Helpers\BackendExportHelper;

require_once 'configure.php';

/** @var Managers $managers */
$managers = $DI->get(Managers::class);

/** @var Response $response */
$response = $DI->get(Response::class);

/** @var BackendExportHelper $backendExportHelper */
$backendExportHelper  = $DI->get(BackendExportHelper::class);

/** @var ManagersEntity $managersEntity */
$managersEntity       = $entityFactory->get(ManagersEntity::class);

$configParams         = $backendExportHelper->getConfigParams();

$columnDelimiter      = $configParams->column_delimiter;
$valuesDelimiter      = $configParams->values_delimiter;
$subcategoryDelimiter = $configParams->subcategory_delimiter;
$productsCount        = $configParams->products_count;
$exportFilesDir       = $configParams->export_files_dir;
$filename             = $configParams->filename;

$columnsNames = $backendExportHelper->getColumnsNames();
if (!$managers->access('export', $managersEntity->get($_SESSION['admin']))) {
    exit();
}

list($filter, $page) = $backendExportHelper->setUp($exportFilesDir, $filename,$columnsNames, $columnDelimiter, $productsCount);
$products = $backendExportHelper->fetchProducts($filter);
$products = $backendExportHelper->attachFeatures($products, $valuesDelimiter);
$products = $backendExportHelper->attachCategories($products, $subcategoryDelimiter);
$products = $backendExportHelper->attachImages($products);

$variants = $backendExportHelper->fetchVariants($products);
foreach($variants as $variant) {
    if(isset($products[$variant->product_id])) {
        $variantData = $backendExportHelper->prepareVariantsData($variant);
        $products[$variant->product_id]['variants'][] = $variantData;
    }
}

$products = $backendExportHelper->attachBrands($products);

$data = $backendExportHelper->exportRun($exportFilesDir, $filename,  $products, $filter, $columnsNames, $columnDelimiter, $productsCount, $page);
if($data) {
    $response->setContent(json_encode($data), RESPONSE_JSON)->sendContent();
}