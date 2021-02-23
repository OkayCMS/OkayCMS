<?php


namespace Okay\Admin\Helpers;


use Okay\Core\Config;
use Okay\Core\Database;
use Okay\Core\EntityFactory;
use Okay\Core\Image;
use Okay\Core\QueryFactory;
use Okay\Core\Request;
use Okay\Entities\AuthorsEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendAuthorsHelper
{
    /**
     * @var AuthorsEntity
     */
    private $authorsEntity;

    /**
     * @var Image
     */
    private $imageCore;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var Database
     */
    private $db;

    /**
     * @var Request
     */
    private $request;

    public function __construct(
        EntityFactory $entityFactory,
        Config        $config,
        Image         $imageCore,
        QueryFactory  $queryFactory,
        Database      $db,
        Request       $request
    ) {
        $this->authorsEntity = $entityFactory->get(AuthorsEntity::class);
        $this->config       = $config;
        $this->imageCore    = $imageCore;
        $this->queryFactory = $queryFactory;
        $this->db           = $db;
        $this->request      = $request;
    }

    public function findAuthors($filter)
    {
        $authors = $this->authorsEntity->mappedBy('id')->find($filter);
        return ExtenderFacade::execute(__METHOD__, $authors, func_get_args());
    }

    public function findAllAuthors()
    {
        $authorsCount = $this->authorsEntity->count();
        $allAuthors = $this->authorsEntity->mappedBy('id')->find(['limit' => $authorsCount]);
        return ExtenderFacade::execute(__METHOD__, $allAuthors, func_get_args());
    }

    public function prepareAdd($author)
    {
        return ExtenderFacade::execute(__METHOD__, $author, func_get_args());
    }

    public function add($author)
    {
        $insertId = $this->authorsEntity->add($author);
        return ExtenderFacade::execute(__METHOD__, $insertId, func_get_args());
    }

    public function prepareUpdate($author)
    {
        return ExtenderFacade::execute(__METHOD__, $author, func_get_args());
    }

    public function update($id, $author)
    {
        $this->authorsEntity->update($id, $author);
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getAuthor($id)
    {
        $author = $this->authorsEntity->get($id);
        return ExtenderFacade::execute(__METHOD__, $author, func_get_args());
    }

    public function deleteImage($author)
    {
        $this->imageCore->deleteImage(
            $author->id,
            'image',
            AuthorsEntity::class,
            $this->config->get('original_authors_dir'),
            $this->config->get('resized_authors_dir')
        );

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function uploadImage($image, $author)
    {
        if (!empty($image['name']) && ($filename = $this->imageCore->uploadImage($image['tmp_name'], $image['name'], $this->config->get('original_authors_dir')))) {
            $this->imageCore->deleteImage(
                $author->id,
                'image',
                AuthorsEntity::class,
                $this->config->get('original_authors_dir'),
                $this->config->get('resized_authors_dir')
            );

            $this->authorsEntity->update($author->id, ['image'=>$filename]);
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function enable($ids)
    {
        $this->authorsEntity->update($ids, ['visible' => 1]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function disable($ids)
    {
        $this->authorsEntity->update($ids, ['visible' => 0]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function delete($ids)
    {
        $this->authorsEntity->delete($ids);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function sortPositions($positions)
    {
        $ids       = array_keys($positions);
        sort($positions);

        foreach ($positions as $i=>$position) {
            $this->authorsEntity->update($ids[$i], ['position'=>$position]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function buildFilter()
    {
        $filter = [];
        $filter['page'] = max(1, $this->request->get('page', 'integer'));

        if ($filter['limit'] = $this->request->get('limit', 'integer')) {
            $filter['limit'] = max(5, $filter['limit']);
            $filter['limit'] = min(100, $filter['limit']);
            $_SESSION['authors_num_admin'] = $filter['limit'];
        } elseif (!empty($_SESSION['authors_num_admin'])) {
            $filter['limit'] = $_SESSION['authors_num_admin'];
        } else {
            $filter['limit'] = 25;
        }

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function countAuthors($filter)
    {
        $authorsCount = $this->authorsEntity->count($filter);
        return ExtenderFacade::execute(__METHOD__, $authorsCount, func_get_args());
    }

    public function makePagination($authorsCount, $filter)
    {
        if ($this->request->get('page') == 'all') {
            $filter['limit'] = $authorsCount;
        }

        if ($filter['limit']>0) {
            $pagesCount = ceil($authorsCount/$filter['limit']);
        } else {
            $pagesCount = 0;
        }

        $filter['page'] = min($filter['page'], $pagesCount);

        return [$filter, $pagesCount];
    }

    public function duplicate($ids)
    {
        foreach($ids as $id) {
            $this->authorsEntity->duplicate((int)$id);
        }
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
}