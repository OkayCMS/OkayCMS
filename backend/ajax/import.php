<?php


use Okay\Entities\ManagersEntity;
use Okay\Core\QueryFactory;
use Okay\Core\Import;
use Okay\Admin\Helpers\BackendImportHelper;

require_once 'configure.php';

/** @var ManagersEntity $managersEntity */
$managersEntity = $entityFactory->get(ManagersEntity::class);

/** @var QueryFactory $queryBuilder */
$queryBuilder = $DI->get(QueryFactory::class);

/** @var Import $import */
$import = $DI->get(Import::class);

/** @var BackendImportHelper $importHelper */
$importHelper = $DI->get(BackendImportHelper::class);

$productsCount = 100;

if (!$managers->access('import', $managersEntity->get($_SESSION['admin']))) {
    exit;
}

$fields = $_SESSION['csv_fields'];
session_write_close();
unset($_SESSION['lang_id']);
unset($_SESSION['admin_lang_id']);

// Для корректной работы установим локаль UTF-8
setlocale(LC_ALL, $import->getLocale());

$result = new \stdClass();

// Определяем колонки из первой строки файла
$f = fopen($import->getImportFilesDir() . $import->getImportFile(), 'r');
$import->setColumns(fgetcsv($f, null, $import->getColumnDelimiter()));
$import->initInternalColumns($fields);

// Если нет названия товара - не будем импортировать
if (empty($fields) || !in_array('sku', $fields) && !in_array('name', $fields)) {
    exit;
}

$sql = $queryBuilder->newSqlQuery();
$sql->setStatement('START TRANSACTION');
$sql->execute();

// Переходим на заданную позицию, если импортируем не сначала
if($from = $request->get('from')) {
    fseek($f, $from);
} else {
    $sql = $queryBuilder->newSqlQuery()->setStatement("TRUNCATE __import_log")->execute();
}

// Массив импортированных товаров
$importedItems = [];

// Проходимся по строкам, пока не конец файла
// или пока не импортировано достаточно строк для одного запроса
for($k=0; !feof($f) && $k < $productsCount; $k++) {
    // Читаем строку
    $line = fgetcsv($f, 0, $import->getColumnDelimiter());

    $product = null;
    if(is_array($line) && !empty($line)) {
        $i = 0;
        // Проходимся по колонкам строки
        foreach ($fields as $csv=>$inner) {
            // Создаем массив item[название_колонки]=значение
            if (isset($line[$i]) && !empty($inner)) {
                $product[$inner] = $line[$i];
            }
            $i++;
        }
    }
    
    // Импортируем этот товар
    if($importedItem = $importHelper->importItem($product)) {
        $importedItems[] = $importedItem;
    }
}

// Запоминаем на каком месте закончили импорт
$from = ftell($f);
// И закончили ли полностью весь файл
$result->end = feof($f);

fclose($f);
$size = filesize($import->getImportFilesDir() . $import->getImportFile());

// Создаем объект результата
$result->from = $from;          // На каком месте остановились
$result->totalsize = $size;     // Размер всего файла

foreach ($importedItems as $item) {
    $productId    = (int)$item->product->id;
    $status       = (string)$item->status;
    $productName  = (string)$item->product->name;
    $variantName  = (string)$item->variant->name;

    $insert = $queryBuilder->newInsert();
    $insert->into('__import_log')
        ->cols([
            'product_id' => $productId,
            'status' => $status,
            'product_name' => $productName,
            'variant_name' => $variantName,
        ])
        ->execute();
}

$sql = $queryBuilder->newSqlQuery();
$sql->setStatement('COMMIT');
$sql->execute();

if ($result) {
    $response->setContent(json_encode($result), RESPONSE_JSON)->sendContent();
}
