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
    'name'       => 'Name',
    'last_name'  => 'Last name',
    'email'      => 'Email',
    'phone'      => 'Phone',
    'address'    => 'Address',
    'group_name' => 'Group name',
    'discount'   => 'Discount',
    'created'    => 'Created',
    'last_ip'    => 'Last IP'
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

fclose($f);

$totalUsers = $usersEntity->count($filter);

if($usersCount*$page < $totalUsers) {
    $data = ['end'=>false, 'page'=>$page, 'totalpages'=>$totalUsers/$usersCount];
} else {
    $data = ['end'=>true, 'page'=>$page, 'totalpages'=>$totalUsers/$usersCount];

    mb_substitute_character('');
    file_put_contents(
        $exportFilesDir.$filename,
        mb_convert_encoding(file_get_contents($exportFilesDir.$filename), 'Windows-1251')
    );
}

if ($data) {
    $response->setContent(json_encode($data), RESPONSE_JSON)->sendContent();
}
