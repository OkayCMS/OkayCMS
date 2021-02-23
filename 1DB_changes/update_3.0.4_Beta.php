<?php


/**
 * Данный скрипт изменит кодировку всех таблиц на utf8mb4_unicode_ci
 * По идее всё должно быть безопасно, но настоятельно рекомендуется сделать бекап базы
 */

chdir('..');

use Okay\Core\Config;
use Okay\Core\Database;
use Okay\Core\QueryFactory;
use Okay\Core\Response;

ini_set('display_errors', 'on');
error_reporting(E_ALL & ~E_DEPRECATED);

$time_start = microtime(true);
if (!empty($_SERVER['HTTP_USER_AGENT'])) {
    session_name(md5($_SERVER['HTTP_USER_AGENT']));
}
session_start();

if (empty($_SESSION['admin'])) {
    die('You must be login in to admin panel');
}

require_once('vendor/autoload.php');

$DI = include 'Okay/Core/config/container.php';

/** @var Database $db */
$db = $DI->get(Database::class);

/** @var Config $config */
$config = $DI->get(Config::class);

/** @var Response $response */
$response = $DI->get(Response::class);

/** @var QueryFactory $queryFactory */
$queryFactory = $DI->get(QueryFactory::class);

$dbName = $config->db_name;
$sql = $queryFactory->newSqlQuery();
$sql->setStatement("SELECT CONCAT('ALTER TABLE `', t.`TABLE_SCHEMA`, '`.`', t.`TABLE_NAME`, '` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;') as sqlcode
  FROM `information_schema`.`TABLES` t
WHERE t.`TABLE_SCHEMA` = '{$config->db_name}'
ORDER BY 1");

$db->query($sql);
$resultQueries = $db->results('sqlcode');

foreach ($resultQueries as $resultQuery) {
    $sql = $queryFactory->newSqlQuery();
    $sql->setStatement($resultQuery);
    $db->query($sql);
}

$response->setContent('Обновление кодировки базы данных выполено, удалите этот скрипт', RESPONSE_TEXT)->sendContent();


