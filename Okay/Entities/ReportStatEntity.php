<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class ReportStatEntity extends Entity
{
    
    protected static $fields = [
        'product_id',
        'variant_id',
        'product_name',
        'variant_name',
        'sku',
    ];

    protected static $additionalFields = [
        'o.id',
        'SUM(p.price * p.amount) AS sum_price',
        'SUM(p.amount) as amount',
    ];

    protected static $defaultOrderFields = [
        'sum_price DESC',
    ];

    protected static $table = '__purchases';
    protected static $tableAlias = 'p';
    protected static $langTable;
    protected static $langObject;

    public function find(array $filter = [])
    {
        $this->select->join('LEFT', '__orders AS o', 'o.id = p.order_id');
        $this->select->groupBy(['p.variant_id', 'p.product_id', 'p.product_name', 'p.variant_name', 'p.sku']);
        $this->select->limit(null);

        return parent::find($filter);
    }
    
    public function count(array $filter = [])
    {
        $this->setUp();
        $this->select->join('LEFT', '__orders AS o', 'o.id = p.order_id');
        $this->buildFilter($filter);
        $this->select->cols(["COUNT( DISTINCT " . $this->getTableAlias() . ".variant_id) as count"]);

        // Уберем группировку и сортировку при подсчете по умолчанию
        $this->select->resetGroupBy();
        $this->select->resetOrderBy();

        $this->db->query($this->select);
        return ExtenderFacade::execute([static::class, __FUNCTION__], $this->getResult('count'), func_get_args());
    }
    
    public function countNullable(array $filter = [])
    {
        $this->setUp();
        $this->select->join('LEFT', '__orders AS o', 'o.id = p.order_id');
        $this->buildFilter($filter);
        $this->select->cols(["COUNT( " . $this->getTableAlias() . ".id) as count"]);

        $this->select->where('p.variant_id IS NULL');
        
        // Уберем группировку и сортировку при подсчете по умолчанию
        $this->select->resetGroupBy();
        $this->select->resetOrderBy();

        $this->db->query($this->select);
        return ExtenderFacade::execute([static::class, __FUNCTION__], $this->getResult('count'), func_get_args());
    }

    protected function filter__category_id($categoriesIds)
    {
        $this->select->join(
            'INNER',
            '__products AS pr',
            'pr.id = p.product_id AND pr.main_category_id IN(:category_ids)'
        );
        
        $this->select->where('p.product_id IS NOT NULL');
        $this->select->bindValue('category_ids', $categoriesIds);

        $this->select->groupBy(['p.id']);
    }
    
    protected function customOrder($order = null, array $orderFields = [], array $additionalData = [])
    {
        // Пример, как реализовать кастомную сортировку.
        switch ($order) {
            case 'price' :
                $orderFields = [
                    'sum_price DESC',
                ];
                break;
            case 'price_in' :
                $orderFields = [
                    'sum_price ASC',
                ];
                break;
            case 'amount' :
                $orderFields = [
                    'amount DESC',
                ];
                break;
            case 'amount_in' :
                $orderFields = [
                    'amount ASC',
                ];
                break;
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $orderFields, func_get_args());
    }
    
    protected function filter__status($statusId)
    {
        $this->select->where('o.status_id = :status_id')
            ->bindValue('status_id', (int)$statusId);
    }

    protected function filter__date_from($dateFrom)
    {
        $this->select->where('o.date >= :date_from')
            ->bindValue('date_from', $dateFrom);
    }
    
    protected function filter__date_to($dateTo)
    {
        $this->select->where('o.date <= :date_to')
            ->bindValue('date_to', $dateTo);
    }
    
    protected function filter__date_filter($dateFilter)
    {
        switch ($dateFilter) {
            case 'today': {
                $this->select->where('DATE(o.date) = DATE(NOW())');
                break;
            }
            case 'this_week': {
                $this->select->where('WEEK(o.date - INTERVAL 1 DAY) = WEEK(now()) /**/ AND YEAR(o.date - INTERVAL 1 DAY) = YEAR(now())');
                break;
            }
            case 'this_month': {
                $this->select->where('MONTH(o.date) = MONTH(now()) /**/ AND YEAR(o.date) = YEAR(now())');
                break;
            }
            case 'this_year': {
                $this->select->where('YEAR(o.date) = YEAR(now())');
                break;
            }
            case 'yesterday': {
                $this->select->where('DATE(o.date) = DATE(DATE_SUB(NOW(),INTERVAL 1 DAY))');
                break;
            }
            case 'last_week': {
                $this->select->where('WEEK(o.date - INTERVAL 1 DAY) = WEEK(DATE_SUB(NOW(),INTERVAL 1 WEEK)) /**/ AND YEAR(o.date - INTERVAL 1 DAY) = YEAR(DATE_SUB(NOW(),INTERVAL 1 WEEK))');
                break;
            }
            case 'last_month': {
                $this->select->where('MONTH(o.date) = MONTH(DATE_SUB(NOW(),INTERVAL 1 MONTH)) /**/ AND YEAR(o.date) = YEAR(DATE_SUB(NOW(),INTERVAL 1 MONTH))');
                break;
            }
            case 'last_year': {
                $this->select->where('YEAR(o.date) = YEAR(DATE_SUB(NOW(),INTERVAL 1 YEAR))');
                break;
            }
            case 'last_24hour': {
                $this->select->where('o.date >= DATE_SUB(NOW(),INTERVAL 24 HOUR)');
                break;
            }
            case 'last_7day': {
                $this->select->where('DATE(o.date) >= DATE(DATE_SUB(NOW(),INTERVAL 6 DAY))');
                break;
            }
            case 'last_30day': {
                $this->select->where('DATE(o.date) >= DATE(DATE_SUB(NOW(),INTERVAL 29 DAY))');
                break;
            }
        }
    }

    /*Выборка категоризации продаж*/
    public function getCategorizedStat($filter = [])
    {
        $select = $this->queryFactory->newSelect();
        $select->from('__purchases AS pp')
            ->cols([
                'pc.category_id',
                'SUM(pp.amount) as amount',
                'SUM(pp.amount * pp.price) as price',
            ])
            ->join('LEFT', '__products AS p', 'p.id=pp.product_id')
            ->join('LEFT', '__products_categories AS pc', '(pc.product_id = p.id AND pc.category_id=(SELECT category_id FROM __products_categories WHERE p.id=product_id ORDER BY position LIMIT 1))')
            ->groupBy(['pc.category_id']);
        
        
        if (!empty($filter['category_id'])) {
            $select->where('pc.category_id in (:category_id)')
                ->bindValue('category_id', (array)$filter['category_id']);
        }
        
        if (!empty($filter['brand_id'])) {
            $select->where('p.brand_id = :brand_id')
                ->bindValue('brand_id', (int)$filter['brand_id']);
        }
        
        if (isset($filter['date_from']) || isset($filter['date_to'])) {
            $select->join('LEFT', '__orders AS o', 'o.id=pp.order_id');
        }
        
        if (isset($filter['date_from']) && !isset($filter['date_to'])) {
            
            $select->where('o.date >= :date_from')
                ->bindValue('date_from', $filter['date_from']);
            
        } elseif (isset($filter['date_to']) && !isset($filter['date_from'])) {
            
            $select->where('o.date <= :date_to')
                ->bindValue('date_to', $filter['date_to']);
            
        } elseif (isset($filter['date_to']) && isset($filter['date_from'])) {

            $select->where('(o.date BETWEEN :date_from AND :date_to)')
                ->bindValues([
                    'date_from' => $filter['date_from'],
                    'date_to' => $filter['date_to'],
                ]);
            
        }
        
        $this->db->query($select);
        $result = [];
        foreach ($this->db->results() as $v) {
            $result[$v->category_id] = $v;
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $result, func_get_args());
    }
    
}
