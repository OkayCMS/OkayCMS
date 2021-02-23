<?php


namespace Okay\Core;


use Aura\SqlQuery\QueryFactory as AuraQueryFactory;
use Okay\Core\QueryFactory\SqlQuery;
use Okay\Core\QueryFactory\Select;
use Okay\Core\QueryFactory\Update;
use Okay\Core\QueryFactory\Delete;
use Okay\Core\QueryFactory\Insert;

class QueryFactory
{
    private $auraQueryFactory;

    public function __construct(AuraQueryFactory $auraQueryFactory)
    {
        $this->auraQueryFactory = $auraQueryFactory;
    }

    public function newSelect()
    {
        return new Select($this->auraQueryFactory->newSelect());
    }

    public function newUpdate()
    {
        return new Update($this->auraQueryFactory->newUpdate());
    }

    public function newInsert()
    {
        return new Insert($this->auraQueryFactory->newInsert());
    }

    public function newDelete()
    {
        return new Delete($this->auraQueryFactory->newDelete());
    }

    public function newSqlQuery()
    {
        return new SqlQuery();
    }
}