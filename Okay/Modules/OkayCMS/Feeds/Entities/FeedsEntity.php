<?php

namespace Okay\Modules\OkayCMS\Feeds\Entities;

use Okay\Core\Entity\Entity;
use Okay\Core\Translit;
use Okay\Entities\LanguagesEntity;

/**
 * @phpstan-type FeedRow \stdClass&object{
 *     id: int|string|null,
 *     name: string,
 *     url: string,
 *     position: int|string,
 *     categories_settings: mixed,
 *     features_settings: mixed,
 *     settings: mixed
 * }
 * @phpstan-type LanguageRow object{id: int|string}
 */
class FeedsEntity extends Entity
{
    protected static $fields = [
        'id',
        'name',
        'url',
        'enabled',
        'preset',
        'settings',
        'features_settings',
        'categories_settings',
        'position'
    ];

    protected static $defaultOrderFields = [
        'position',
    ];

    protected static $table = 'okay_cms__feeds__feeds';
    protected static $tableAlias = 'oc_ff';

    public function find(array $filter = [])
    {
        $resultFields = $this->getAllFieldsWithoutAlias();

        $results = parent::find($filter);

        $field = null;

        if (count($resultFields) == 1) {
            $field = reset($resultFields);
        }

        if (!$field) {
            if (in_array('categories_settings', $resultFields)) {
                foreach ($results as $result) {
                    /** @var FeedRow $result */
                    $result->categories_settings = unserialize($result->categories_settings);
                }
            }

            if (in_array('features_settings', $resultFields)) {
                foreach ($results as $result) {
                    /** @var FeedRow $result */
                    $result->features_settings = unserialize($result->features_settings);
                }
            }

            if (in_array('features_settings', $resultFields)) {
                foreach ($results as $result) {
                    /** @var FeedRow $result */
                    $result->settings = unserialize($result->settings);
                }
            }
        } elseif ($field === 'categories_settings' || $field === 'features_settings' || $field === 'settings') {
            foreach ($results as &$result) {
                $result = unserialize($result);
            }
        }

        return $results;
    }

    public function add($feed)
    {
        /** @var Translit $translit */
        $translit = $this->serviceLocator->getService(Translit::class);

        $feed = (object)$feed;
        /** @var FeedRow $feed */
        if (empty($feed->url)) {
            $feed->url = $translit->translit($feed->name);
            $feed->url = str_replace('.', '', $feed->url);
        }

        $feed->url = preg_replace("/[\s]+/ui", '', $feed->url);

        while ($this->findOne(['url' => $feed->url])) {
            if (preg_match('/(.+)([0-9]+)$/', $feed->url, $parts)) {
                $feed->url = $parts[1] . '' . ($parts[2] + 1);
            } else {
                $feed->url = $feed->url . '2';
            }
        }

        if (!isset($feed->categories_settings)) {
            $feed->categories_settings = [];
        }

        if (!isset($feed->features_settings)) {
            $feed->features_settings = [];
        }

        if (!isset($feed->settings)) {
            $feed->settings = [];
        }

        $feed->categories_settings = serialize((array) $feed->categories_settings);
        $feed->features_settings = serialize((array) $feed->features_settings);
        $feed->settings = serialize((array) $feed->settings);

        return parent::add($feed);
    }

    public function update($ids, $object)
    {
        $object = (object)$object;

        if (isset($object->categories_settings)) {
            $object->categories_settings = serialize((array) $object->categories_settings);
        }

        if (isset($object->features_settings)) {
            $object->features_settings = serialize((array) $object->features_settings);
        }

        if (isset($object->settings)) {
            $object->settings = serialize((array) $object->settings);
        }

        return parent::update($ids, $object);
    }

    public function delete($ids): bool
    {
        if ($result = parent::delete($ids)) {
            /** @var ConditionsEntity $conditionsEntity */
            $conditionsEntity = $this->entity->get(ConditionsEntity::class);
            $conditionsEntity->deleteByDiscountId($ids);
        }

        return $result;
    }

    public function duplicate($feedId)
    {
        /** @var FeedRow|null $feed */
        $feed = $this->findOne(['id' => $feedId]);
        if (!$feed) {
            return false;
        }

        //Запоминаем текущую позицию, на нее станет новая запись
        $position = $feed->position;

        $newFeed = new \stdClass();

        $fields = array_merge($this->getFields(), $this->getLangFields());

        foreach ($fields as $field) {
            if (!empty($field) && property_exists($feed, $field)) {
                $newFeed->$field = $feed->$field;
            }
        }

        $newFeed->id = null;
        $newFeed->url = '';

        //Добавляем новую запись в бд
        $newFeedId = $this->add($newFeed);

        // Сдвигаем страницы вперед и вставляем копию на соседнюю позицию
        $update = $this->queryFactory->newUpdate();
        $update->table(self::getTable())
            ->set('position', 'position+1')
            ->where('position>=:position')
            ->bindValue('position', $feed->position);
        $this->db->query($update);

        $update = $this->queryFactory->newUpdate();
        $update->table(self::getTable())
            ->set('position', ':position')
            ->where('id=:id')
            ->bindValues([
                'position' => $position,
                'id' => $newFeedId,
            ]);
        $this->db->query($update);

        $this->multiDuplicateFeed($feedId, $newFeedId);
        $this->duplicateConditions($feedId, $newFeedId);

        return $newFeedId;
    }

    private function multiDuplicateFeed($feedId, $newFeedId): void
    {
        $langId = $this->lang->getLangId();
        if (!empty($langId)) {

            /** @var LanguagesEntity $langEntity */
            $langEntity = $this->entity->get(LanguagesEntity::class);

            /** @var array<int|string, LanguageRow> $languages */
            $languages = $langEntity->find();
            $feedLangFields = $this->getLangFields();

            foreach ($languages as $language) {
                if ($language->id != $langId) {
                    $this->lang->setLangId($language->id);

                    if (!empty($feedLangFields)) {
                        /** @var object|null $sourceFeed */
                        $sourceFeed = $this->findOne(['id' => $feedId]);
                        if (!$sourceFeed) {
                            continue;
                        }
                        $destinationFeed = new \stdClass();
                        foreach ($feedLangFields as $field) {
                            $destinationFeed->{$field} = $sourceFeed->{$field};
                        }
                        $this->update($newFeedId, $destinationFeed);
                    }

                    $this->lang->setLangId($langId);
                }
            }
        }
    }

    private function duplicateConditions($feedId, $newFeedId): void
    {
        /** @var ConditionsEntity $bBlocksEntity */
        $conditionsEntity = $this->entity->get(ConditionsEntity::class);
        foreach ($conditionsEntity->find(['feed_id' => $feedId]) as $condition) {
            $conditionsEntity->duplicate($condition->id, $newFeedId);
        }
    }
}
