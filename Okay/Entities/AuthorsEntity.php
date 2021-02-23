<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Translit;
use Okay\Core\Image;

class AuthorsEntity extends Entity
{
    
    protected static $fields = [
        'id',
        'url',
        'image',
        'last_modify',
        'visible',
        'position',
        'socials',
    ];

    protected static $langFields = [
        'name',
        'position_name',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'description'
    ];

    protected static $searchFields = [
        'name',
        'meta_keywords',
    ];

    protected static $defaultOrderFields = [
        'position',
    ];

    protected static $table = 'authors';
    protected static $langObject = 'author';
    protected static $langTable = 'authors';
    protected static $tableAlias = 'a';
    protected static $alternativeIdField = 'url';
    
    public function find(array $filter = [])
    {
        $this->select->distinct(true);
        $this->select->join('left', '__blog AS b', 'b.author_id=a.id');
        return parent::find($filter);
    }
    
    public function count(array $filter = [])
    {
        $this->select->join('left', '__blog AS b', 'b.author_id=a.id');
        return parent::count($filter);
    }

    protected function filter__post_visible($postVisible)
    {
        $this->select->where('p.visible = ' . (int)$postVisible);
    }
    
    protected function filter__post_id($postsIds)
    {
        $this->select->where('b.id IN (:post_id)');
        $this->select->bindValue('post_id', (array)$postsIds);
    }

    public function add($author)
    {
        /** @var Translit $translit */
        $translit = $this->serviceLocator->getService(Translit::class);

        $author = (object)$author;
        if (empty($author->url)) {
            $author->url = $translit->translit($author->name);
            $author->url = str_replace('.', '', $author->url);
        }

        $author->url = preg_replace("/[\s]+/ui", '', $author->url);

        while ($this->findOne(['url' => $author->url])) {
            if(preg_match('/(.+)([0-9]+)$/', $author->url, $parts)) {
                $author->url = $parts[1].''.($parts[2]+1);
            } else {
                $author->url = $author->url.'2';
            }
        }

        return parent::add($author);
    }

    public function delete($ids)
    {
        $ids = (array)$ids;
        if (empty($ids)) {
            parent::delete($ids);
        }

        /** @var Image $imageCore */
        $imageCore = $this->serviceLocator->getService(Image::class);
        foreach ($ids as $id) {
            $imageCore->deleteImage(
                $id,
                'image',
                self::class,
                $this->config->original_authors_dir,
                $this->config->resized_authors_dir
            );
        }

        $update = $this->queryFactory->newUpdate();
        $update->table(BlogEntity::getTable())
            ->set('author_id', 0)
            ->where('author_id IN (:author_id)')
            ->bindValue('author_id', $ids);
        $this->db->query($update);

        parent::delete($ids);
    }

    public function duplicate($authorId)
    {
        $author = $this->findOne(['id' => $authorId]);

        //Запоминаем текущую позицию, на нее станет новая запись
        $position = $author->position;

        $newAuthor = new \stdClass();

        $fields = array_merge($this->getFields(), $this->getLangFields());

        foreach ($fields as $field) {
            if (property_exists($author, $field)) {
                $newAuthor->$field = $author->$field;
            }
        }

        $newAuthor->id = null;
        $newAuthor->url = '';

        //Добавляем новую запись в бд
        $newAuthorId = $this->add($newAuthor);

        // Сдвигаем страницы вперед и вставляем копию на соседнюю позицию
        $update = $this->queryFactory->newUpdate();
        $update->table('__authors')
            ->set('position', 'position+1')
            ->where('position>=:position')
            ->bindValue('position', $author->position);
        $this->db->query($update);

        $update = $this->queryFactory->newUpdate();
        $update->table('__authors')
            ->set('position', ':position')
            ->where('id=:id')
            ->bindValues([
                'position' => $position,
                'id' => $newAuthorId,
            ]);
        $this->db->query($update);

        $this->multiDuplicateAuthor($authorId, $newAuthorId);
        return $newAuthorId;
    }

    private function multiDuplicateAuthor($authorId, $newAuthorId) {
        $langId = $this->lang->getLangId();
        if (!empty($langId)) {

            /** @var LanguagesEntity $langEntity */
            $langEntity = $this->entity->get(LanguagesEntity::class);

            $languages = $langEntity->find();
            $authorLangFields = $this->getLangFields();

            foreach ($languages as $language) {
                if ($language->id != $langId) {
                    $this->lang->setLangId($language->id);

                    if (!empty($authorLangFields)) {
                        $sourceAuthor = $this->findOne(['id' => $authorId]);
                        $destinationAuthor = new \stdClass();
                        foreach($authorLangFields as $field) {
                            $destinationAuthor->{$field} = $sourceAuthor->{$field};
                        }
                        $this->update($newAuthorId, $destinationAuthor);
                    }

                    $this->lang->setLangId($langId);
                }
            }
        }
    }
    
}
