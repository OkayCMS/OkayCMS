<?php


namespace Okay\Core\QueryFactory;


use Aura\SqlQuery\QueryInterface;
use Okay\Core\ServiceLocator;
use Okay\Core\Database;

abstract class AbstractQuery implements QueryInterface
{
    /**
     * @var Database
     */
    protected $db;

    /**
     * @var QueryInterface
     */
    protected $queryObject;

    /**
     * @var bool
     */
    protected $executed;

    public function __construct($queryObject)
    {
        $SL                = ServiceLocator::getInstance();
        $this->db          = $SL->getService(Database::class);
        $this->queryObject = $queryObject;
    }

    public function debug()
    {
        if (!$query = $this->db->prepare($this, $this->getBindValues())) {
            return false;
        }
        
        return $this->db->debug($query, $this->getBindValues());
    }

    public function debugPrint()
    {
        if ($queryString = $this->debug()) {
            print "<pre>" . $queryString . "</pre>" . PHP_EOL . PHP_EOL;
        } else {
            print 'Error in query' . PHP_EOL . PHP_EOL;
        }
    }
    
    public function result($column = null)
    {
        if ($this->executed) {
            return $this->db->result($column);
        }

        $this->db->query($this);
        $this->executed = true;
        return $this->db->result($column);
    }

    public function results($column = null, $mapped = null)
    {
        if ($this->executed) {
            return $this->db->results($column, $mapped);
        }

        $this->db->query($this);
        $this->executed = true;
        return $this->db->results($column, $mapped);
    }

    public function execute()
    {
        $this->db->query($this);
        $this->executed = true;
        return $this;
    }

    public function __toString()
    {
        return $this->queryObject->getStatement();
    }

    public function getQuoteNamePrefix()
    {
        return $this->queryObject->getQuoteNamePrefix();
    }

    public function getQuoteNameSuffix()
    {
        return $this->queryObject->getQuoteNameSuffix();
    }

    public function bindValues(array $bind_values)
    {
        $this->queryObject->bindValues($bind_values);
        return $this;
    }

    public function bindValue($name, $value)
    {
        $this->queryObject->bindValue($name, $value);
        return $this;
    }

    public function getBindValues()
    {
        return $this->queryObject->getBindValues();
    }

    public function getStatement()
    {
        return $this->queryObject->getStatement();
    }
}