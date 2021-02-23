<?php


namespace Okay\Admin\Controllers;


use Okay\Core\Request;
use Okay\Core\QueryFactory;
use Okay\Core\EntityFactory;
use Okay\Entities\ImagesEntity;
use Okay\Entities\ProductsEntity;

class ImportLogAdmin extends IndexAdmin
{

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * @var ImagesEntity
     */
    protected $imagesEntity;

    /**
     * @var ProductsEntity
     */
    protected $productsEntity;


    public function fetch(
        Request $request,
        QueryFactory $queryFactory,
        EntityFactory $entityFactory
    ) {
        $this->request        = $request;
        $this->queryFactory  = $queryFactory;
        $this->entityFactory  = $entityFactory;
        $this->imagesEntity   = $entityFactory->get(ImagesEntity::class);
        $this->productsEntity = $entityFactory->get(ProductsEntity::class);

        $filter = array();
        $filter['page'] = max(1, $this->request->get('page', 'integer'));

        if ($filter['limit'] = $this->request->get('limit', 'integer')) {
            $filter['limit'] = max(25, $filter['limit']);
            $filter['limit'] = min(500, $filter['limit']);
            $_SESSION['import_log_num'] = $filter['limit'];
        } elseif (!empty($_SESSION['import_log_num'])) {
            $filter['limit'] = $_SESSION['import_log_num'];
        } else {
            $filter['limit'] = 100;
        }
        $this->design->assign('current_limit', $filter['limit']);

        // Текущий фильтр
        if($f = $this->request->get('filter', 'string')) {
            $filter['status'] = $f;
            $this->design->assign('filter', $f);
        }

        // Поиск
        $keyword = $this->request->get('keyword');
        if(!empty($keyword)) {
            $filter['keyword'] = $keyword;
            $this->design->assign('keyword', $keyword);
        }

        $logs_count = $this->getLogs($filter);
        // Показать все страницы сразу
        if($this->request->get('page') == 'all') {
            $filter['limit'] = $logs_count;
        }

        if($filter['limit']>0) {
            $pages_count = ceil($logs_count/$filter['limit']);
        } else {
            $pages_count = 0;
        }
        $filter['page'] = min($filter['page'], $pages_count);
        $this->design->assign('logs_count', $logs_count);
        $this->design->assign('pages_count', $pages_count);
        $this->design->assign('current_page', $filter['page']);
        $logs = $this->getLogs($filter, false);
        if(!empty($logs)) {
            $products_ids = array();
            foreach($logs as $l) {
                $products_ids[] = $l->product_id;
            }

            $products = array();
            $images_ids = array();
            foreach($this->productsEntity->find(['id'=>array_unique($products_ids)]) as $p) {
                $products[$p->id] = $p;
                $images_ids[] = $p->main_image_id;
            }

            if (!empty($images_ids)) {
                $images = $this->imagesEntity->find(['id'=>$images_ids]);
                foreach ($images as $image) {
                    if (isset($products[$image->product_id])) {
                        $products[$image->product_id]->image = $image;
                    }
                }
            }

            foreach ($logs as $l) {
                if (isset($products[$l->product_id])) {
                    $l->product = $products[$l->product_id];
                }
            }
        }
        $this->design->assign('logs', $logs);

        $this->response->setContent($this->design->fetch('import_log.tpl'));
    }

    /*Выборка лога последнего успешного импорта*/
    private function getLogs($filter = array(), $is_count = true) {
        $keyword_filter = '';
        $status_filter = '';
        $sql_limit = '';

        if (isset($filter['keyword'])) {
            $keywords = explode(' ', $filter['keyword']);
            foreach ($keywords as $keyword) {
                $kw = trim($keyword);
                if ($kw !== '') {
                    $keyword_filter .= "AND (
                        il.product_name LIKE '%$kw%'
                        OR il.variant_name LIKE '%$kw%'
                    ) ";
                }
            }
        }
        if (isset($filter['status'])) {
            $filterStatusParam = $filter['status'];
            $status_filter = "AND il.status='$filterStatusParam'";
        }

        if ($is_count) {
            $select = 'count(il.id) as cnt';
        } else {
            $select = 'il.*';
            $limit = 100;
            if(isset($filter['limit'])) {
                $limit = max(1, intval($filter['limit']));
            }
            if(isset($filter['page'])) {
                $page = max(1, intval($filter['page']));
            }

            $firstParam  = ($page-1)*$limit;
            $secondParam = $limit;
            $sql_limit = " LIMIT $firstParam, $secondParam ";
        }

        $query = "SELECT $select
            FROM __import_log AS il
            WHERE
                1
                $keyword_filter
                $status_filter
            ORDER BY id DESC
            $sql_limit
        ";

        $sql = $this->queryFactory->newSqlQuery();
        $sql->setStatement($query);
        $this->db->query($sql);

        if ($is_count) {
            return $this->db->result('cnt');
        } else {
            return $this->db->results();
        }
    }

}
