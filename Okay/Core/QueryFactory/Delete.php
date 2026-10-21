<?php


namespace Okay\Core\QueryFactory;


use Aura\SqlQuery\QueryInterface;
use Aura\SqlQuery\Common\Delete as AuraDelete;

class Delete extends AbstractQuery
{
    use BindPlaceholdersTrait;

    /**
     * @var QueryInterface|AuraDelete
     */
    protected $queryObject;

    public function execute()
    {
        return $this->db->query($this->queryObject);
    }

    public function from($table)
    {
        $this->queryObject->from($table);
        return $this;
    }

    public function where($cond, $bind = [])
    {
        $binds = array_slice(func_get_args(), 1);

        list($cond, $named) = $this->bindPlaceholders($cond, $binds);
        $this->queryObject->where($cond, $named);
        return $this;
    }

    public function orWhere($cond, $bind = [])
    {
        $binds = array_slice(func_get_args(), 1);

        list($cond, $named) = $this->bindPlaceholders($cond, $binds);
        $this->queryObject->orWhere($cond, $named);
        return $this;
    }

    public function lowPriority($enable = true)
    {
        if (method_exists($this->queryObject, 'lowPriority')) {
            $this->queryObject->lowPriority($enable);
        }

        return $this;
    }

    public function ignore($enable = true)
    {
        if (method_exists($this->queryObject, 'ignore')) {
            $this->queryObject->ignore($enable);
        }

        return $this;
    }

    public function cache($enable = true)
    {
        if (method_exists($this->queryObject, 'cache')) {
            $this->queryObject->cache($enable);
        }

        return $this;
    }

    public function quick($enable = true)
    {
        if (method_exists($this->queryObject, 'quick')) {
            $this->queryObject->quick($enable);
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
