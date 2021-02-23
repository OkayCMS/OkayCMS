<?php


use Okay\Entities\CurrenciesEntity;
use Okay\Entities\ManagersEntity;
use Okay\Entities\OrdersEntity;
use Okay\Core\QueryFactory;
use Okay\Core\Managers;
use Okay\Core\Response;
use Okay\Core\Database;

require_once 'configure.php';

$columnsNames = [
    'id'=>           'Order ID',
    'date'=>         'Order date',
    'name'=>         'User name',
    'phone'=>        'User phone',
    'email'=>        'User email',
    'address'=>      'User address',
    'comment'=>      'User comment',
    'total_price'=>  'Total price',
    'currency'=>     'Currency'
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
if(empty($page) || $page==1) {
    $page = 1;
    if(is_writable($exportFilesDir.$filename)) {
        unlink($exportFilesDir.$filename);
    }
}

$f = fopen($exportFilesDir.$filename, 'ab');

$filter          = [];
$filter['page']  = $page;
$filter['limit'] = $ordersCount;

$statusId = $request->get('status', 'integer');
if (!empty($statusId)) {
    $filter['status'] = $statusId;
}

$labelId = $request->get('label', 'integer');
if(!empty($labelId)) {
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

if($page == 1) {
    fputcsv($f, $columnsNames, $columnDelimiter);
}

$mainCurrency =  $currenciesEntity->getMainCurrency();

$orders = $ordersEntity->find($filter);
if (!empty($orders)) {
    foreach($orders as $o) {
        $str = array();
        $o->currency = $mainCurrency->code;
        foreach($columnsNames as $n=>$c) {
            $str[] = $o->$n;
        }
        fputcsv($f, $str, $columnDelimiter);
    }
}
$totalOrders = $ordersEntity->count($filter);

if($ordersCount*$page < $totalOrders) {
    $data = ['end'=>false, 'page'=>$page, 'totalpages'=>$totalOrders/$ordersCount];
} else {
    $data = ['end'=>true, 'page'=>$page, 'totalpages'=>$totalOrders/$ordersCount];
}

fclose($f);

file_put_contents(
    $exportFilesDir.$filename,
    iconv( "utf-8", "windows-1251//IGNORE", file_get_contents($exportFilesDir.$filename))
);

if($data) {
    $response->setContent(json_encode($data), RESPONSE_JSON)->sendContent();
}