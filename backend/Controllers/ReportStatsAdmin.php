<?php


namespace Okay\Admin\Controllers;


use Okay\Entities\CategoriesEntity;
use Okay\Entities\OrderStatusEntity;
use Okay\Entities\ReportStatEntity;

class ReportStatsAdmin extends IndexAdmin
{
    
    public function fetch(ReportStatEntity $reportStatEntity, CategoriesEntity $categoriesEntity, OrderStatusEntity $orderStatusEntity) {
        $filter = [];
        $date_filter = $this->request->get('date_filter');
        if (!empty($date_filter)) {
            $filter['date_filter'] = $date_filter;
            $this->design->assign('date_filter', $date_filter);
        }

        /*Фильтр по датам*/
        $date_from = $this->request->get('date_from');
        $date_to = $this->request->get('date_to');
        $filter_check = $this->request->get('filter_check');
        
        if (!empty($date_from)) {
            $filter['date_from'] = date("Y-m-d 00:00:00", strtotime($date_from));
            $this->design->assign('date_from', $date_from);
        }
        
        if (!empty($date_to)) {
            $filter['date_to'] = date("Y-m-d 23:59:59", strtotime($date_to));
            $this->design->assign('date_to', $date_to);
        }
        $this->design->assign('filter_check', $filter_check);
        
        $status = $this->request->get('status', 'integer');
        if (!empty($status)) {
            $filter['status'] = $status;
            $this->design->assign('status', $status);
        }
        
        $sort_prod = $this->request->get('sort_prod');
        if (!empty($sort_prod)) {
            $filter['sort_prod'] = $sort_prod;
            $this->design->assign('sort_prod',$sort_prod);
        } else {
            $sort_prod = 'price';
            $this->design->assign('sort_prod',$sort_prod);
        }
        
        $filter['page'] = max(1, $this->request->get('page', 'integer'));
        $filter['limit'] = 40;
        $catFilter = $this->request->get('category_id','integer');
        $this->design->assign('category',$catFilter );

        if ($cat = $categoriesEntity->get($catFilter)) {
            $filter['category_id'] = $cat->children;
        }

        $statCount = $reportStatEntity->count($filter);
        $statCount += $reportStatEntity->countNullable($filter);
        
        $this->design->assign('posts_count', $statCount );
        
        if ($this->request->get('page') == 'all') {
            $filter['limit'] = $statCount;
        }
        
        $this->design->assign('pages_count', ceil($statCount/$filter['limit']));
        $this->design->assign('current_page', $filter['page']);

        /*Выборка товаров для статистики*/
        $report_stat_purchases = $reportStatEntity->find($filter);
        
        foreach ($report_stat_purchases as $id=>$r) {
            if (!empty($r->product_id)) {
                $tmp_cat = $categoriesEntity->findOne(array('product_id' => $r->product_id));
                $report_stat_purchases[$id]->category = $tmp_cat;
            }
        }

        $all_status = $orderStatusEntity->find();
        $this->design->assign("all_status", $all_status);
        $this->design->assign('report_stat_purchases', $report_stat_purchases);
        $this->design->assign('categories', $categoriesEntity->getCategoriesTree());

        $this->response->setContent($this->design->fetch('reportstats.tpl'));
    }

    public function export(
        ReportStatEntity $reportStatEntity,
        CategoriesEntity $categoriesEntity
    ) {

        $columnsNames = [
            'category_name' => 'Категория',
            'product_name'  => 'Название товара',
            'sum_price'     => 'Сумма',
            'amount'        => 'Количество',
        ];

        $columnDelimiter = ';';
        $statCount       = 100;
        $exportFilesDir  = 'backend/files/export/';
        $filename        = 'export_stat_products.csv';
        
        $page = $this->request->get('page');
        if (empty($page) || $page==1) {
            $page = 1;
            if (is_writable($exportFilesDir.$filename)) {
                unlink($exportFilesDir.$filename);
            }
        }

        $f = fopen($exportFilesDir.$filename, 'ab');
        if ($page == 1) {
            fputcsv($f, $columnsNames, $columnDelimiter);
        }

        $filter = [];
        $dateFilter = $this->request->get('date_filter');
        if (!empty($dateFilter)) {
            $filter['date_filter'] = $dateFilter;
        }

        if ($this->request->get('date_from') || $this->request->get('date_to')) {
            $dateFrom = $this->request->get('date_from');
            $dateTo = $this->request->get('date_to');
        }

        $catFilter = $this->request->get('category_id', 'int');
        if ($cat = $categoriesEntity->get($catFilter)) {
            $filter['category_id'] = $cat->children;
        }

        if (!empty($dateFrom)) {
            $filter['date_from'] = date("Y-m-d 00:00:00", strtotime($dateFrom));
        }
        if (!empty($dateTo)) {
            $filter['date_to'] = date("Y-m-d 23:59:59", strtotime($dateTo));
        }
        $status = $this->request->get('status', 'integer');
        if (!empty($status)) {
            $filter['status'] = $status;
        }

        $sortProd = $this->request->get('sort_prod');
        if (!empty($sortProd)) {
            $filter['sort_prod'] = $sortProd;
        } else {
            $filter['sort_prod'] = 'price';
        }
        
        $filter['page'] = max(1, $this->request->get('page', 'integer'));
        $filter['limit'] = 40;

        if ($this->request->get('page') == 'all') {
            $filter['limit'] = $statCount;
        }
        
        $totalCount = $reportStatEntity->count($filter);
        $totalCount += $reportStatEntity->countNullable($filter);

        $totalSum = 0;
        $totalAmount = 0;
        $reportStatPurchases = $reportStatEntity->find($filter);
        
        foreach ($reportStatPurchases as $id=>$r) {
            if (!empty($r->product_id)) {
                $tmpCat = $categoriesEntity->findOne(['product_id' => $r->product_id]);
                $reportStatPurchases[$id]->category_name = $tmpCat->name;
            } else {
                $reportStatPurchases[$id]->category_name = '';
            }
        }

        foreach ($reportStatPurchases as $u) {
            $totalSum += $u->sum_price;
            $totalAmount += $u->amount;
            $str = [];
            foreach($columnsNames as $n=>$c) {
                $str[] = $u->$n;
            }
            fputcsv($f, $str, $columnDelimiter);
        }

        $total = [
            'category_name' => 'Итого',
            'product_name'  => ' ',
            'price'         => $totalSum,
            'amount'        => $totalAmount
        ];

        fputcsv($f, $total, $columnDelimiter);
        fclose($f);

        file_put_contents(
            $exportFilesDir.$filename,
            iconv( "utf-8", "windows-1251//IGNORE", file_get_contents($exportFilesDir.$filename))
        );

        if ($statCount*$page < $totalCount) {
            $data = ['end'=>false, 'page'=>$page, 'totalpages'=>$totalCount/$statCount];
        } else {
            $data = ['end'=>true, 'page'=>$page, 'totalpages'=>$totalCount/$statCount];
        }

        if ($data) {
            $this->response->setContent(json_encode($data), RESPONSE_JSON);
        }
    }
    
}
