<?php


namespace Okay\Core\QueryFactory;


use Aura\SqlQuery\QueryInterface;
use Aura\SqlQuery\Common\Update as AuraUpdate;

class Update extends AbstractQuery implements QueryInterface
{
    /**
     * @var QueryInterface|AuraUpdate
     */
    protected $queryObject;

    public function execute()
    {
        return $this->db->query($this->queryObject);
    }

    public function table($table)
    {
        $this->queryObject->table($table);
        return $this;
    }

    public function where($cond)
    {
        $this->queryObject->where($cond);
        return $this;
    }

    public function orWhere($cond)
    {
        $this->queryObject->orWhere($cond);
        return $this;
    }

    public function col($col)
    {
        $this->queryObject->col($col);
        return $this;
    }

    public function cols(array $cols)
    {
        $this->queryObject->cols($cols);
        return $this;
    }

    public function set($col, $value)
    {
        $this->queryObject->set($col, $value);
        return $this;
    }

    public function lowPriority($enable)
    {
        if (method_exists($this->queryObject, 'lowPriority')) {
            $this->queryObject->lowPriority($enable);
        }

        return $this;
    }

    public function ignore($enable)
    {
        if (method_exists($this->queryObject, 'ignore')) {
            $this->queryObject->ignore($enable);
        }

        return $this;
    }

    public function limit($limit)
    {
        if (method_exists($this->queryObject, 'limit')) {
            $this->queryObject->limit($limit);
        }

        return $this;
    }

    public function getLimit()
    {
        if (method_exists($this->queryObject, 'getLimit')) {
            return $this->queryObject->getLimit();
        }

        return null;
    }

    public function orderBy(array $spec)
    {
        if (method_exists($this->queryObject, 'orderBy')) {
            return $this->queryObject->orderBy($spec);
        }

        return $this;
    }
}