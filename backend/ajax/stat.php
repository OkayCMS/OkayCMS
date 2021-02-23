<?php

use Okay\Core\QueryFactory;
use Okay\Core\Database;

require_once 'configure.php';

if (!$managers->access('sales_chart', $manager)) {
    exit();
}

/** @var QueryFactory $queryFactory */
$queryFactory = $DI->get(QueryFactory::class);

/** @var Database $db */
$db = $DI->get(Database::class);

$select = $queryFactory->newSelect();
$select->cols([
    'SUM( o.total_price ) AS total_price',
    'MAX(DAY(date)) AS day',
    'MAX(MONTH(date)) as month',
    'MAX(YEAR(date)) as year',
])->from('__orders o')
    ->where('o.closed ')
    ->groupBy([
        'YEAR(o.date)',
        'MONTH(o.date)',
        'DATE(o.date)',
    ]);

$db->query($select);
$data = $db->results();

$results = [];
foreach($data as $d) {
    $result['day'] = $d->day;
    $result['month'] = $d->month;
    $result['year'] = $d->year;
    $result['y'] = $d->total_price;
    $results[] = $result;
}

$response->setContent(json_encode($results), RESPONSE_JSON);
$response->sendContent();
