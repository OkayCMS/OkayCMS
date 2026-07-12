<?php

use Okay\Entities\CurrenciesEntity;
use Okay\Entities\ManagersEntity;
use Okay\Entities\OrdersEntity;
use Okay\Core\Export\CsvExportWriter;
use Okay\Core\QueryFactory;
use Okay\Core\Managers;
use Okay\Core\Response;
use Okay\Core\Database;

require_once 'configure.php';

$columnsNames = [
    'id' =>           'Order ID',
    'date' =>         'Order date',
    'name' =>         'User name',
    'last_name' =>    'User last name',
    'phone' =>        'User phone',
    'email' =>        'User email',
    'comment' =>      'User comment',
    'total_price' =>  'Total price',
    'currency' =>     'Currency'
];

$columnDelimiter = ';';
$ordersCount = 100;
$exportFilesDir = 'backend/files/export/';
$filename = 'export_orders.csv';

/** @var Database $db */
$db = $DI->get(Database::class);

/** @var QueryFactory $queryFactory */
$queryFactory = $DI->get(QueryFactory::class);

/** @var Managers $managers */
$managers = $DI->get(Managers::class);

/** @var Response $response */
$response = $DI->get(Response::class);

/** @var CsvExportWriter $csvExportWriter */
$csvExportWriter = new CsvExportWriter();
$requestedFormat = $_GET['format'] ?? null;
$format = $csvExportWriter->normalizeFormat(is_string($requestedFormat) ? $requestedFormat : null);

/** @var OrdersEntity $ordersEntity */
$ordersEntity = $entityFactory->get(OrdersEntity::class);

/** @var ManagersEntity $managersEntity */
$managersEntity = $entityFactory->get(ManagersEntity::class);

/** @var CurrenciesEntity $currenciesEntity */
$currenciesEntity = $entityFactory->get(CurrenciesEntity::class);

if (!$managers->access('export', $managersEntity->get($_SESSION['admin']))) {
    exit();
}

session_write_close();
unset($_SESSION['lang_id']);
unset($_SESSION['admin_lang_id']);

$page = $request->get('page');
if (empty($page) || $page == 1) {
    $page = 1;
    if (is_writable($exportFilesDir . $filename)) {
        unlink($exportFilesDir . $filename);
    }
}

try {
    $f = $csvExportWriter->openAppendStream($exportFilesDir, $filename, $format);
} catch (\RuntimeException) {
    $response->setContent(json_encode(false), RESPONSE_JSON)->sendContent();
    exit;
}

$filter          = [];
$filter['page']  = $page;
$filter['limit'] = $ordersCount;

$statusId = $request->get('status', 'integer');
if (!empty($statusId)) {
    $filter['status_id'] = $statusId;
}

$labelId = $request->get('label', 'integer');
if (!empty($labelId)) {
    $filter['label'] = $labelId;
}

$fromDate = $request->get('from_date');
$toDate = $request->get('to_date');

if (!empty($fromDate)) {
    $filter['from_date'] = $fromDate;
}
if (!empty($toDate)) {
    $filter['to_date'] = $toDate;
}

if ($page == 1) {
    $csvExportWriter->writeRow($f, $columnsNames, $columnDelimiter, $format);
}

$mainCurrency =  $currenciesEntity->getMainCurrency();

$orders = $ordersEntity->find($filter);
if (!empty($orders)) {
    foreach ($orders as $o) {
        $str = array();
        $o->currency = $mainCurrency->code;
        foreach ($columnsNames as $n => $c) {
            $str[] = $o->$n;
        }
        $csvExportWriter->writeRow($f, $str, $columnDelimiter, $format);
    }
}

fclose($f);

$totalOrders = (int) $ordersEntity->count($filter);

if ($ordersCount * $page < $totalOrders) {
    $data = ['end' => false, 'page' => $page, 'totalpages' => $totalOrders / $ordersCount];
} else {
    $data = ['end' => true, 'page' => $page, 'totalpages' => $totalOrders / $ordersCount];
}

if ($data) {
    $response->setContent(json_encode($data), RESPONSE_JSON)->sendContent();
}
