<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Translit;

class PagesEntity extends Entity
{

    // Системные url
    private $systemPages = [
        '',
        'catalog',
        'products',
        'all-products',
        'discounted',
        'bestsellers',
        'brands',
        'wishlist',
        'comparison',
        'cart',
        'order',
        'contact',
        'user',
        '404',
        'authors',
    ];

    protected static $fields = [
        'id',
        'url',
        'visible',
        'position',
        'last_modify',
    ];

    protected static $langFields = [
        'name',
        'name_h1',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'description'
    ];

    protected static $defaultOrderFields = [
        'position ASC',
    ];

    protected static $table = '__pages';
    protected static $langObject = 'page';
    protected static $langTable = 'pages';
    protected static $tableAlias = 'p';
    protected static $alternativeIdField = 'url';
    
    public function getSystemPages()
    {
        return $this->systemPages;
    }

    public function get($id)
    {
        $this->setUp();

        if (!is_int($id) && $this->getAlternativeIdField()) {
            $filter[$this->getAlternativeIdField()] = $id;
        } else {
            $filter['id'] = $id;
        }

        $this->buildFilter($filter);
        $this->select->cols($this->getAllFields());

        $this->db->query($this->select);

        return ExtenderFacade::execute([static::class, __FUNCTION__], $this->getResult(), func_get_args());
    }
    
    public function delete($ids)
    {
        $ids = (array)$ids;

        if (empty($ids)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
        }

        $result = true;
        foreach ($ids as $id) {
            // Запретим удаление системных ссылок
            $page = $this->get(intval($id));
            if (!in_array($page->url, $this->systemPages)) {
                parent::delete($id);
            } else {
                $result = false;
            }
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $result, func_get_args());
    }

    private function formattedUrl($url)
    {
        $url = trim($url);

        if (empty($url)) {
            return $url;
        }

        $url = explode('?', $url)[0];

        if ($url[0] === '/') {
            $url = substr($url, 1);
        }

        $lastSymbolNumber = strlen($url) - 1;
        if (isset($url[$lastSymbolNumber]) && $url[$lastSymbolNumber] == '/') {
            $url = substr($url, 0, -1);
        }

        return $url;
    }

    public function add($page)
    {
        /** @var Translit $translit */
        $translit = $this->serviceLocator->getService(Translit::class);

        $page = (object)$page;
        if (empty($page->url)) {
            $page->url = $translit->translit($page->name);
            $page->url = str_replace('.', '', $page->url);
        }

        $page->url = preg_replace("/[\s]+/ui", '', $page->url);

        while ($this->get((string)$page->url)) {
            if(preg_match('/(.+)([0-9]+)$/', $page->url, $parts)) {
                $page->url = $parts[1].''.($parts[2]+1);
            } else {
                $page->url = $page->url.'2';
            }
        }

        return parent::add($page);
    }

    public function duplicate($pageId)
    {
        $page = $this->findOne(['id' => $pageId]);

        //Запоминаем текущую позицию, на нее станет новая запись
        $position = $page->position;

        $newPage = new \stdClass();

        $fields = array_merge($this->getFields(), $this->getLangFields());

        foreach ($fields as $field) {
            if (property_exists($page, $field)) {
                $newPage->$field = $page->$field;
            }
        }

        $newPage->id = null;
        $newPage->url = '';

        //Добавляем новую запись в бд
        $newPageId = $this->add($newPage);

        // Сдвигаем страницы вперед и вставляем копию на соседнюю позицию
        $update = $this->queryFactory->newUpdate();
        $update->table('__pages')
            ->set('position', 'position+1')
            ->where('position>=:position')
            ->bindValue('position', $page->position);
        $this->db->query($update);

        $update = $this->queryFactory->newUpdate();
        $update->table('__pages')
            ->set('position', ':position')
            ->where('id=:id')
            ->bindValues([
                'position' => $position,
                'id' => $newPageId,
            ]);
        $this->db->query($update);

        $this->multiDuplicatePage($pageId, $newPageId);
        return $newPageId;
    }

    private function multiDuplicatePage($pageId, $newPageId) {
        $langId = $this->lang->getLangId();
        if (!empty($langId)) {

            /** @var LanguagesEntity $langEntity */
            $langEntity = $this->entity->get(LanguagesEntity::class);

            $languages = $langEntity->find();
            $pageLangFields = $this->getLangFields();

            foreach ($languages as $language) {
                if ($language->id != $langId) {
                    $this->lang->setLangId($language->id);

                    if (!empty($pageLangFields)) {
                        $sourcePage = $this->findOne(['id' => $pageId]);
                        $destinationPage = new \stdClass();
                        foreach($pageLangFields as $field) {
                            $destinationPage->{$field} = $sourcePage->{$field};
                        }
                        $this->update($newPageId, $destinationPage);
                    }

                    $this->lang->setLangId($langId);
                }
            }
        }
    }
}
