<?php


use Okay\Core\Database;
use Okay\Core\Managers;
use Okay\Core\Response;
use Okay\Core\QueryFactory;
use Okay\Entities\BrandsEntity;
use Okay\Entities\ManagersEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\ReportStatEntity;

require_once 'configure.php';

$totalPrice  = 0;
$totalAmount = 0;

$columnsNames = [
    'name'   => 'Имя',
    'amount' => 'Количество',
    'price'  => 'Цена',
];

$columnDelimiter = ';';
$exportFilesDir  = 'backend/files/export/';
$filename        = 'export_stat.csv';

/** @var Database $db */
$db = $DI->get(Database::class);

/** @var QueryFactory $queryFactory */
$queryFactory = $DI->get(QueryFactory::class);

/** @var Managers $managers */
$managers = $DI->get(Managers::class);

/** @var Response $response */
$response = $DI->get(Response::class);

/** @var BrandsEntity $brandsEntity */
$brandsEntity         = $entityFactory->get(BrandsEntity::class);

/** @var ManagersEntity $managersEntity */
$managersEntity       = $entityFactory->get(ManagersEntity::class);

/** @var ReportStatEntity $reportStatEntity */
$reportStatEntity     = $entityFactory->get(ReportStatEntity::class);

/** @var CategoriesEntity $categoriesEntity */
$categoriesEntity     = $entityFactory->get(CategoriesEntity::class);


if (!$managers->access('category_stats', $managersEntity->get($_SESSION['admin']))) {
    exit();
}

// Страница, которую экспортируем
$page = $request->get('page');
if (empty($page) || $page==1) {
    $page = 1;
    // Если начали сначала - удалим старый файл экспорта
    if (is_writable($exportFilesDir.$filename)) {
        unlink($exportFilesDir.$filename);
    }
}

// Открываем файл экспорта на добавление
$f = fopen($exportFilesDir.$filename, 'ab');

// Если начали сначала - добавим в первую строку названия колонок
if ($page == 1) {
    fputcsv($f, $columnsNames, $columnDelimiter);
}

$filter = [];
$filter['page'] = $page;
$totalPrice = 0;
$totalAmount = 0;
$category_id = $request->get('category','integer');
if (!empty($category_id)) {
    $category = $categoriesEntity->get(intval($category_id));
    $this->design->assign('category',$category);
    $filter['category_id'] = $category->children;
}

$brand_id = $request->get('brand','integer');
if (!empty($brand_id)) {
    $filter['brand_id'] = $brand_id;
    $brand = $brandsEntity->get(intval($brand_id));
    $this->design->assign('brand',$brand);
}

$dateFrom = $request->get('date_from');
$dateTo = $request->get('date_to');

if (!empty($dateFrom) || !empty($dateTo)) {
    if (!empty($dateFrom)) {
        $filter['date_from'] = date("Y-m-d 00:00:01", strtotime($dateFrom));
    }
    if (!empty($dateTo)) {
        $filter['date_to'] = date("Y-m-d 23:59:00", strtotime($dateTo));
    }
}

$categories = $categoriesEntity->getCategoriesTree();
$purchases = $reportStatEntity->getCategorizedStat($filter);

if (!empty($category)) {
    $categories_list = cat_tree([$category], $purchases);
} else {
    $categories_list = cat_tree($categories, $purchases);
}
foreach ($categories_list as $c) {
    fputcsv($f, $c, $columnDelimiter);
}

$total = [
    'name' => 'Имя',
    'amount' => $totalAmount,
    'price'=>$totalPrice
];

fputcsv($f, $total, $columnDelimiter);
fclose($f);

file_put_contents(
    $exportFilesDir.$filename,
    iconv( "utf-8", "windows-1251//IGNORE", file_get_contents($exportFilesDir.$filename))
);

$data = true;

if ($data) {
    $response->setContent(json_encode($data), RESPONSE_JSON)->sendContent();
}


function cat_tree($categories, $purchases = [], &$result = []) {
    global $totalPrice, $totalAmount, $subcategoryDelimiter;

    foreach ($categories as $k=>$v) {
        $category = [];
        $path = [];

        foreach ($v->path as $p) {
            $path[] = str_replace($subcategoryDelimiter, '\\'.$subcategoryDelimiter, $p->name);
        }

        if (isset($purchases[$v->id])) {
            $price = floatval($purchases[$v->id]->price);
            $amount = intval($purchases[$v->id]->amount);
        } else {
            $price = 0;
            $amount = 0;
        }

        $category['name']   = implode('/', $path);
        $category['price']  = $price;
        $category['amount'] = $amount;
        $result[] = $category;
        $totalPrice += $price;
        $totalAmount += $amount;
        if (isset($v->subcategories)) {
            array_merge($result, cat_tree($v->subcategories,$purchases,$result));
        }
    }
    return $result;
}