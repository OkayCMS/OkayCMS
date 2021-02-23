<?php


namespace Okay\Core\QueryFactory;


use Aura\SqlQuery\Common\SelectInterface;
use Aura\SqlQuery\Common\SubselectInterface;
use Aura\SqlQuery\QueryInterface;
use Aura\SqlQuery\Common\Select as AuraSelect;

class Select extends AbstractQuery implements SelectInterface, SubselectInterface
{
    /**
     * @var QueryInterface|AuraSelect
     */
    protected $queryObject;

    public function setPaging($paging)
    {
        $this->queryObject->setPaging($paging);
        return $this;
    }

    public function getPaging()
    {
        return $this->queryObject->getPaging();
    }

    public function forUpdate($enable = true)
    {
        $this->queryObject->forUpdate($enable);
        return $this;
    }

    public function distinct($enable = true)
    {
        $this->queryObject->distinct($enable);
        return $this;
    }

    public function cols(array $cols)
    {
        $this->queryObject->cols($cols);
        return $this;
    }

    public function removeCol($alias)
    {
        $this->queryObject->removeCol($alias);
        return $this;
    }

    public function hasCols()
    {
        return $this->queryObject->hasCols();
    }

    public function getCols()
    {
        return $this->queryObject->getCols();
    }

    public function from($spec)
    {
        $this->queryObject->from($spec);
        return $this;
    }

    public function fromRaw($spec)
    {
        $this->queryObject->fromRaw($spec);
        return $this;
    }

    public function fromSubSelect($spec, $name)
    {
        $this->queryObject->fromSubSelect($spec, $name);
        return $this;
    }

    public function join($join, $spec, $cond = null, array $bind = [])
    {
        $this->queryObject->join($join, $spec, $cond, $bind);
        return $this;
    }

    public function innerJoin($spec, $cond = null, array $bind = [])
    {
        $this->queryObject->innerJoin($spec, $cond, $bind);
        return $this;
    }

    public function leftJoin($spec, $cond = null, array $bind = [])
    {
        $this->queryObject->leftJoin($spec, $cond, $bind);
        return $this;
    }

    public function joinSubSelect($join, $spec, $name, $cond = null, array $bind = [])
    {
        $this->queryObject->joinSubSelect($join, $spec, $name, $cond, $bind);
        return $this;
    }

    public function groupBy(array $spec)
    {
        $this->queryObject->groupBy($spec);
        return $this;
    }

    public function having($cond)
    {
        $this->queryObject->having($cond);
        return $this;
    }

    public function orHaving($cond)
    {
        $this->queryObject->orHaving($cond);
        return $this;
    }

    public function page($page)
    {
        $this->queryObject->page($page);
        return $this;
    }

    public function getPage()
    {
        return $this->queryObject->getPage();
    }

    public function union()
    {
        $this->queryObject->union();
        return $this;
    }

    public function unionAll()
    {
        $this->queryObject->unionAll();
        return $this;
    }

    public function getLimit()
    {
        return $this->queryObject->getLimit();
    }

    public function getOffset()
    {
        return $this->queryObject->getOffset();
    }

    public function resetCols()
    {
        $this->queryObject->resetCols();
        return $this;
    }

    public function resetTables()
    {
        $this->queryObject->resetTables();
        return $this;
    }

    public function resetWhere()
    {
        $this->queryObject->resetWhere();
        return $this;
    }

    public function resetGroupBy()
    {
        $this->queryObject->resetGroupBy();
        return $this;
    }

    public function resetHaving()
    {
        $this->queryObject->resetHaving();
        return $this;
    }

    public function resetOrderBy()
    {
        $this->queryObject->resetOrderBy();
        return $this;
    }

    public function resetUnions()
    {
        $this->queryObject->resetUnions();
        return $this;
    }

    public function where($cond, $param = null)
    {
        $this->queryObject->where($cond, $param);
        return $this;
    }

    public function orWhere($cond)
    {
        $this->queryObject->orWhere($cond);
        return $this;
    }

    public function limit($limit)
    {
        $this->queryObject->limit($limit);
        return $this;
    }

    public function offset($offset)
    {
        $this->queryObject->offset($offset);
        return $this;
    }

    public function orderBy(array $spec)
    {
        $this->queryObject->orderBy($spec);
        return $this;
    }

    public function calcFoundRows($enable = true)
    {
        if (method_exists($this->queryObject, 'calcFoundRows')) {
            $this->queryObject->calcFoundRows($enable);
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

    public function noCache($enable = true)
    {
        if (method_exists($this->queryObject, 'noCache')) {
            $this->queryObject->noCache($enable);
        }

        return $this;
    }

    public function straightJoin($enable = true)
    {
        if (method_exists($this->queryObject, 'straightJoin')) {
            $this->queryObject->straightJoin($enable);
        }

        return $this;
    }

    public function highPriority($enable = true)
    {
        if (method_exists($this->queryObject, 'highPriority')) {
            $this->queryObject->highPriority($enable);
        }

        return $this;
    }

    public function smallResult($enable = true)
    {
        if (method_exists($this->queryObject, 'smallResult')) {
            $this->queryObject->smallResult($enable);
        }

        return $this;
    }

    public function bigResult($enable = true)
    {
        if (method_exists($this->queryObject, 'smallResult')) {
            $this->queryObject->smallResult($enable);
        }

        return $this;
    }

    public function bufferResult($enable = true)
    {
        if (method_exists($this->queryObject, 'smallResult')) {
            $this->queryObject->smallResult($enable);
        }

        return $this;
    }

}