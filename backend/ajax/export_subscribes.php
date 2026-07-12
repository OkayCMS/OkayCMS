<?php

use Okay\Core\Database;
use Okay\Core\Export\CsvExportWriter;
use Okay\Core\Managers;
use Okay\Core\Response;
use Okay\Core\QueryFactory;
use Okay\Entities\UsersEntity;
use Okay\Entities\SubscribesEntity;

require_once 'configure.php';

$columnsNames = [
    'email' => 'Email'
];

$totalUsers      = 500;
$columnDelimiter = ';';
$subscribesCount = 5;
$exportFilesDir  = 'backend/files/export_users/';
$filename        = 'subscribes.csv';


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

/** @var SubscribesEntity $subscribesEntity */
$subscribesEntity     = $entityFactory->get(SubscribesEntity::class);

/** @var UsersEntity $usersEntity */
$usersEntity          = $entityFactory->get(UsersEntity::class);

if (!$managers->access('users', $managersEntity->get($_SESSION['admin']))) {
    exit();
}

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

if ($page == 1) {
    $csvExportWriter->writeRow($f, $columnsNames, $columnDelimiter, $format);
}

$filter = [];
$filter['page']  = $page;
$filter['limit'] = $totalUsers;
$filter['sort']  = $request->get('sort');

$users = [];
foreach ($subscribesEntity->find($filter) as $s) {
    $str = [];
    foreach ($columnsNames as $n => $c) {
        $str[] = $s->$n;
    }

    $csvExportWriter->writeRow($f, $str, $columnDelimiter, $format);
}

fclose($f);

$totalSubscribes = (int) $subscribesEntity->count();

if ($subscribesCount * $page < $totalSubscribes) {
    $data = ['end' => false, 'page' => $page, 'totalpages' => $totalSubscribes / $subscribesCount];
} else {
    $data = ['end' => true, 'page' => $page, 'totalpages' => $totalSubscribes / $subscribesCount];
}

if ($data) {
    $response->setContent(json_encode($data), RESPONSE_JSON)->sendContent();
}
