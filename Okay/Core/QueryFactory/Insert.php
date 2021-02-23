<?php


namespace Okay\Core\QueryFactory;


use Aura\SqlQuery\QueryInterface;
use Aura\SqlQuery\Common\Insert as AuraInsert;

class Insert extends AbstractQuery
{
    /**
     * @var QueryInterface|AuraInsert
     */
    protected $queryObject;

    public function execute()
    {
        return $this->db->query($this->queryObject);
    }

    public function into($into)
    {
        $this->queryObject->into($into);
        return $this;
    }

    public function ignore($ignore = true)
    {
        if (method_exists($this->queryObject, 'ignore')) {
            $this->queryObject->ignore($ignore);
        }

        return $this;
    }

    public function getLastInsertIdName($col)
    {
        return $this->queryObject->getLastInsertIdName($col);
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

    public function getBindValues()
    {
        return $this->queryObject->getBindValues();
    }

    public function addRows(array $rows)
    {
        $this->queryObject->addRows($rows);
        return $this;
    }

    public function addRow(array $cols = [])
    {
        $this->queryObject->addRow($cols);
        return $this;
    }

    public function highPriority($enable = true)
    {
        if (method_exists($this->queryObject, 'highPriority')) {
            $this->queryObject->highPriority($enable);
        }

        return $this;
    }

    public function lowPriority($enable = true)
    {
        if (method_exists($this->queryObject, 'lowPriority')) {
            $this->queryObject->lowPriority($enable);
        }

        return $this;
    }

    public function delayed($enable = true)
    {
        if (method_exists($this->queryObject, 'delayed')) {
            $this->queryObject->delayed($enable);
        }

        return $this;
    }

    public function onDuplicateKeyUpdateCol($col)
    {
        if (method_exists($this->queryObject, 'onDuplicateKeyUpdateCol')) {
            $this->queryObject->onDuplicateKeyUpdateCol($col);
        }

        return $this;
    }

    public function onDuplicateKeyUpdateCols(array $cols)
    {
        if (method_exists($this->queryObject, 'onDuplicateKeyUpdateCols')) {
            $this->queryObject->onDuplicateKeyUpdateCols($cols);
        }

        return $this;
    }

    public function onDuplicateKeyUpdate($col, $value)
    {
        if (method_exists($this->queryObject, 'onDuplicateKeyUpdate')) {
            $this->queryObject->onDuplicateKeyUpdate($col, $value);
        }

        return $this;
    }


}