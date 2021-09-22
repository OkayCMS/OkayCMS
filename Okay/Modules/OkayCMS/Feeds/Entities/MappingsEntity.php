<?php

namespace Okay\Modules\OkayCMS\Feeds\Entities;

use Okay\Core\Entity\Entity;
use Okay\Entities\LanguagesEntity;

class MappingsEntity extends Entity
{
    protected static $fields = [
        'id',
        'feed_id',
        'entity',
        'entity_id',
        'value',
        'to_feed'
    ];

    protected static $langFields = [
        'value'
    ];

    protected static $table = 'okay_cms__feeds__mappings';
    protected static $tableAlias = 'oc_fm';
    protected static $langTable = 'okay_cms__feeds__mappings';
    protected static $langObject = 'mapping';

    public function duplicate($mappingId, $newFeedId)
    {
        $mapping = $this->findOne(['id' => $mappingId]);

        $newMapping = new \stdClass();

        $fields = array_merge($this->getFields(), $this->getLangFields());

        foreach ($fields as $field) {
            if (property_exists($mapping, $field)) {
                $newMapping->$field = $mapping->$field;
            }
        }

        $newMapping->id = null;
        $newMapping->feed_id = $newFeedId;

        //Добавляем новую запись в бд
        $newMappingId = $this->add($newMapping);

        $this->multiDuplicatemapping($mappingId, $newMappingId);

        return $newMappingId;
    }

    private function multiDuplicatemapping($mappingId, $newMappingId): void
    {
        $langId = $this->lang->getLangId();
        if (!empty($langId)) {

            /** @var LanguagesEntity $langEntity */
            $langEntity = $this->entity->get(LanguagesEntity::class);

            $languages = $langEntity->find();
            $mappingLangFields = $this->getLangFields();

            foreach ($languages as $language) {
                if ($language->id != $langId) {
                    $this->lang->setLangId($language->id);

                    if (!empty($mappingLangFields)) {
                        $sourcemapping = $this->findOne(['id' => $mappingId]);
                        $destinationmapping = new \stdClass();
                        foreach($mappingLangFields as $field) {
                            $destinationmapping->{$field} = $sourcemapping->{$field};
                        }
                        $this->update($newMappingId, $destinationmapping);
                    }

                    $this->lang->setLangId($langId);
                }
            }
        }
    }
}