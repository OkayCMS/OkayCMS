<?php


namespace Okay\Admin\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\PagesEntity;

class BackendPagesHelper
{
    /**
     * @var PagesEntity
     */
    private $pagesEntity;

    public function __construct(EntityFactory $entityFactory)
    {
        $this->pagesEntity = $entityFactory->get(PagesEntity::class);
    }

    public function prepareAdd($page)
    {
        return ExtenderFacade::execute(__METHOD__, $page, func_get_args());
    }

    public function add($page)
    {
        $insertId = $this->pagesEntity->add($page);
        return ExtenderFacade::execute(__METHOD__, $insertId, func_get_args());
    }

    public function prepareUpdate($page)
    {
        return ExtenderFacade::execute(__METHOD__, $page, func_get_args());
    }

    public function update($id, $page)
    {
        $result = $this->pagesEntity->update($id, $page);
        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    public function getPage($id)
    {
        $page = $this->pagesEntity->get($id);
        return ExtenderFacade::execute(__METHOD__, $page, func_get_args());
    }

    public function sortPositions($positions)
    {
        $ids = array_keys($positions);
        sort($positions);
        foreach ($positions as $i=>$position) {
            $this->pagesEntity->update($ids[$i], ['position'=>$position]);
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function disable($ids)
    {
        $this->pagesEntity->update($ids, ['visible'=>0]);
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function enable($ids)
    {
        $this->pagesEntity->update($ids, ['visible'=>1]);
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function delete($ids)
    {
        $result = $this->pagesEntity->delete($ids);
        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    public function findPages($filter = [])
    {
        $pages = $this->pagesEntity->find($filter);
        return ExtenderFacade::execute(__METHOD__, $pages, func_get_args());
    }

    public function duplicate($ids)
    {
        foreach($ids as $id) {
            $this->pagesEntity->duplicate((int)$id);
        }
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
}