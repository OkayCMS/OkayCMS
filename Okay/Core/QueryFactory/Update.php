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

    /**
     * @param array<string, mixed> $bind
     * @return $this
     */
    public function where($cond, array $bind = [])
    {
        $this->queryObject->where($cond, $bind);
        return $this;
    }

    /**
     * @param array<string, mixed> $bind
     * @return $this
     */
    public function orWhere($cond, array $bind = [])
    {
        $this->queryObject->orWhere($cond, $bind);
        return $this;
    }

    /**
     * @param list<string|int|float|bool|null> $binds
     * @return $this
     */
    public function col($col, array $binds = [])
    {
        $this->queryObject->col($col, $binds);
        return $this;
    }

    /**
     * @param array<int|string, mixed> $cols
     * @return $this
     */
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

    /**
     * @param list<string> $spec
     * @return $this
     */
    public function orderBy(array $spec)
    {
        if (method_exists($this->queryObject, 'orderBy')) {
            return $this->queryObject->orderBy($spec);
        }

        return $this;
    }
}
