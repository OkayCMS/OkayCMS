<?php


namespace Okay\Helpers;


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\JsSocial;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\AuthorsEntity;

class AuthorsHelper implements GetListInterface
{
    
    private $entityFactory;
    
    public function __construct(EntityFactory $entityFactory)
    {
        $this->entityFactory = $entityFactory;
    }

    /**
     * @inheritDoc
     */
    public function getList($filter = [], $sortName = null, $excludedFields = null)
    {
        if ($excludedFields === null) {
            $excludedFields = $this->getExcludeFields();
        }
        
        /** @var AuthorsEntity $authorsEntity */
        $authorsEntity = $this->entityFactory->get(AuthorsEntity::class);

        // Исключаем колонки, которые нам не нужны
        if (is_array($excludedFields) && !empty($excludedFields)) {
            $authorsEntity->cols(AuthorsEntity::getDifferentFields($excludedFields));
        }
        
        if ($sortName !== null) {
            $authorsEntity->order($sortName, $this->getOrderAuthorsAdditionalData());
        }

        $posts = $authorsEntity->mappedBy('id')->find($filter);
        
        return ExtenderFacade::execute(__METHOD__, $posts, func_get_args());
    }

    public function getSocials($author)
    {
        if (empty($author->socials)) {
            return ExtenderFacade::execute(__METHOD__, [1], func_get_args());
        }
        
        if (is_array($author->socials)) {
            return ExtenderFacade::execute(__METHOD__, $author->socials, func_get_args());
        } elseif ($socials = json_decode($author->socials, true)) {
            foreach ($socials as $k=>$social) {
                $socials[$k]['domain'] = JsSocial::getSocialDomain($social['url']);
            }
            return ExtenderFacade::execute(__METHOD__, $socials, func_get_args());
        }

        return ExtenderFacade::execute(__METHOD__, false, func_get_args());
    }
    
    public function getExcludeFields()
    {
        return ExtenderFacade::execute(__METHOD__, [], func_get_args());
    }

    public function getCurrentSort()
    {
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
    
    public function getAuthorsFilter()
    {
        // Выбираем только активных авторов
        $filter['visible'] = 1;
        
        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }
    
    public function paginate($itemsPerPage, $currentPage, array &$filter, Design $design)
    {
        /** @var AuthorsEntity $authorsEntity */
        $authorsEntity = $this->entityFactory->get(AuthorsEntity::class);

        // Вычисляем количество страниц
        $authorsCount = $authorsEntity->count($filter);

        // Показать все страницы сразу
        $allPages = false;
        if ($currentPage == 'all') {
            $allPages = true;
            $itemsPerPage = $authorsCount;
        }

        // Если не задана, то равна 1
        $currentPage = max(1, (int)$currentPage);
        $design->assign('current_page_num', $currentPage);
        $design->assign('is_all_pages', $allPages);

        $pagesNum = !empty($itemsPerPage) ? ceil($authorsCount/$itemsPerPage) : 0;
        $design->assign('total_pages_num', $pagesNum);
        $design->assign('total_products_num', $authorsCount);

        $filter['page'] = $currentPage;
        $filter['limit'] = $itemsPerPage;

        $result = true;
        if ($allPages === false && $currentPage > 1 && $currentPage > $pagesNum) {
            $result = false;
        }

        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    private function getOrderAuthorsAdditionalData()
    {
        $orderAdditionalData = [];
        return ExtenderFacade::execute(__METHOD__, $orderAdditionalData, func_get_args());
    }
    
}