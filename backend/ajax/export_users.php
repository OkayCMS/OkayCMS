<?php


use Okay\Entities\UsersEntity;
use Okay\Core\QueryFactory;
use Okay\Core\Managers;
use Okay\Core\Database;
use Okay\Core\Response;

require_once 'configure.php';

$columnDelimiter = ';';
$usersCount      = 100;
$exportFilesDir  = 'backend/files/export_users/';
$filename        = 'users.csv';

$columnsNames = [
    'name'       => '���',
    'email'      => 'Email',
    'phone'      => '�������',
    'address'    => '�����',
    'group_name' => '������',
    'discount'   => '������',
    'created'    => '����',
    'last_ip'    => '��������� IP'
];

/** @var Database $db */
$db = $DI->get(Database::class);

/** @var QueryFactory $queryFactory */
$queryFactory = $DI->get(QueryFactory::class);

/** @var Managers $managers */
$managers = $DI->get(Managers::class);

/** @var Response $response */
$response = $DI->get(Response::class);

/** @var UsersEntity $usersEntity */
$usersEntity         = $entityFactory->get(UsersEntity::class);

if (!$managers->access('users', $managersEntity->get($_SESSION['admin']))) {
    exit();
}

$page = $request->get('page');
if(empty($page) || $page==1) {
    $page = 1;
    if(is_writable($exportFilesDir.$filename)) {
        unlink($exportFilesDir.$filename);
    }
}

$f = fopen($exportFilesDir.$filename, 'ab');
if($page == 1) {
    fputcsv($f, $columnsNames, $columnDelimiter);
}

$filter = [];
$filter['page'] = $page;
$filter['limit'] = $usersCount;
if($request->get('group_id')) {
    $filter['group_id'] = intval($request->get('group_id'));
}
$filter['sort'] = $request->get('sort');

$users = [];
foreach($usersEntity->find($filter) as $u) {
    $str = array();
    foreach($columnsNames as $n=>$c) {
        $str[] = $u->$n;
    }
    fputcsv($f, $str, $columnDelimiter);
}

$totalUsers = $usersEntity->count($filter);

if($usersCount*$page < $totalUsers) {
    $data = ['end'=>false, 'page'=>$page, 'totalpages'=>$totalUsers/$usersCount];
} else {
    $data = ['end'=>true, 'page'=>$page, 'totalpages'=>$totalUsers/$usersCount];
}

fclose($f);

file_put_contents(
    $exportFilesDir.$filename,
    iconv( "utf-8", "windows-1251//IGNORE", file_get_contents($exportFilesDir.$filename))
);

if ($data) {
    $response->setContent(json_encode($data), RESPONSE_JSON)->sendContent();
}
