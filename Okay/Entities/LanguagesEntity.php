<?php


namespace Okay\Entities;


use Okay\Core\Languages;
use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class LanguagesEntity extends Entity
{
    protected static $fields = [
        'id',
        'label',
        'href_lang',
        'enabled',
        'position',
    ];

    protected static $langFields = [
        'name',
    ];

    protected static $defaultOrderFields = [
        'position ASC',
    ];

    protected static $table = '__languages';
    protected static $langObject = 'language';
    protected static $langTable = 'languages';
    protected static $tableAlias = 'le';
    
    private $allLanguages = [];
    private $mainLanguage;
    
    
    public function __construct()
    {
        parent::__construct();
        $this->initLanguages();
    }
    
    private function initLanguages()
    {
        $this->mappedBy('id');
        $this->allLanguages = [];
        $this->allLanguages = parent::find();
        
        $this->mainLanguage = reset($this->allLanguages);
    }

    public function get($id)
    {
        if (empty($this->allLanguages)) {
            $this->initLanguages();
        }
        
        if (empty($id)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
        }

        if (is_int($id) && isset($this->allLanguages[$id])) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], $this->allLanguages[$id], func_get_args());
        }

        if (is_string($id)) {
            foreach ($this->allLanguages as $language) {
                if ($language->label == $id) {
                    return ExtenderFacade::execute([static::class, __FUNCTION__], $language, func_get_args());
                }
            }
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
    }

    /**
     * @param $langId
     * @return object|null
     * @throws \Exception
     * Метод возвращает язык, с массивом его переводов на другие языки
     */
    public function getMultiLanguage($langId)
    {
        $this->initLanguages();
        
        $currentLangId = $this->lang->getLangId();
        $result = parent::get($langId);
        
        foreach ($this->allLanguages as $l) {
            $this->lang->setLangId($l->id);
            $this->mappedBy('id');
            $lang = parent::get((int)$langId);
            $result->names[$l->id] = $lang->name;
            $results[$result->id] = $result;
        }

        $this->lang->setLangId($currentLangId);

        return ExtenderFacade::execute([static::class, __FUNCTION__], $result, func_get_args());
    }
    
    // Метод по сути ничего не фильтрует, только возвращает все языки
    public function find(array $filter = [])
    {
        if (empty($this->allLanguages)) {
            $this->initLanguages();
        }
        
        $result = $this->allLanguages;
        if (!empty($filter['label'])) {
            $result = [];
            foreach ($this->allLanguages as $l) {
                if ($l->label == $filter['label']) {
                    $result[$l->id] = $l;
                }
            }
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $result, func_get_args());
    }
    
    /*Выборка первого языка сайта*/
    public function getMainLanguage()
    {
        return ExtenderFacade::execute([static::class, __FUNCTION__], $this->mainLanguage, func_get_args());
    }

    public function update($ids, $language)
    {
        parent::update($ids, $language);
        
        $this->initLanguages();
        return true;
    }

    /*Добавление языка*/
    public function add($language)
    {
        
        $language = (object)$language;
        $langId = parent::add($language);
        
        /** @var Languages $languagesCore */
        $languagesCore = $this->serviceLocator->getService(Languages::class);

        /** @var TranslationsEntity $translations */
        $translations = $this->entity->get(TranslationsEntity::class);
        
        if (isset($langId)) {

            $translations->copyTranslations($this->mainLanguage->label, $language->label);
            
            if ($entitiesLangInfo = $languagesCore->getEntitiesLangInfo()) {
                foreach ($entitiesLangInfo as $entityLangInfo) {
                    $sql = $this->queryFactory->newSqlQuery();
                    $sql->setStatement('INSERT INTO ' . $entityLangInfo->langTable . ' (' . implode(',', $entityLangInfo->fields) . ', ' . $entityLangInfo->object . '_id, lang_id)
                                    SELECT ' . implode(',', $entityLangInfo->fields) . ', id, ' . $langId . '
                                    FROM ' . $entityLangInfo->table);
                    $this->db->query($sql);
                }
            }
            
            if (isset($this->mainLanguage) && !empty($this->mainLanguage)) {
                $settings = $this->settings->getSettings($this->mainLanguage->id);
                if (!empty($settings)) {
                    foreach ($settings as $s) {
                        $sql = $this->queryFactory->newSqlQuery();
                        $sql->setStatement("REPLACE INTO `__settings_lang` SET 
                                                    `lang_id`=:lang_id,
                                                    `param`=:param,
                                                    `value`=:value
                                                    ");
                        $sql->bindValue('land_id', $this->db->escape($langId));
                        $sql->bindValue('param', $this->db->escape($s->param));
                        $sql->bindValue('value', $this->db->escape($s->value));
                        $this->db->query($sql);
                    }
                }
            } else {
                $sql = $this->queryFactory->newSqlQuery();
                $sql->setStatement("UPDATE `__settings_lang` SET `lang_id`=:lang_id");
                $sql->bindValue('lang_id', $this->db->escape($langId));
                $this->db->query($sql);
            }
        }
        $this->initLanguages();

        return ExtenderFacade::execute([static::class, __FUNCTION__], $langId, func_get_args());
    }

    /*Удаление языка*/
    public function delete($ids)
    {
        /** @var TranslationsEntity $translationsEntity */
        $translationsEntity = $this->entity->get(TranslationsEntity::class);
        
        $ids = (array)$ids;
        $languages = $this->find();
        if (count($languages) == count($ids)) {
            $first = $this->getMainLanguage();
        }
        
        foreach ($ids as $id) {

            // Удалим переводы фронта
            $lang = $this->get((int)$id);
            $translationsEntity->deleteLang($lang->label);
            
            $saveMain = (isset($first) && $id == $first->id);
            if (empty($id)) {
                continue;
            }
            
            $id = (int)$id;
            parent::delete($id);
            
            $tables = $this->getLangTables();
            
            foreach ($tables as $table) {
                $delete = $this->queryFactory->newDelete();
                $delete->from($table)->where("lang_id={$id}");
                $this->db->query($delete);
            }

            if (!$saveMain) {
                $delete = $this->queryFactory->newDelete();
                $delete->from('__settings_lang')->where("lang_id={$id}");
                $this->db->query($delete);
            } else {
                $update = $this->queryFactory->newUpdate();
                $update->table('__settings_lang')
                    ->set('lang_id', 0)
                    ->where("lang_id={$id}");
                $this->db->query($update);
            }
        }
        $this->initLanguages();
        return true;
    }
    
    private function getLangTables()
    {
        $sql = $this->queryFactory->newSqlQuery();
        $sql->setStatement("SHOW TABLES LIKE '%__lang\_%'");
        $this->db->query($sql);

        $tables = [];
        while ($table = $this->db->result()) {
            $tables[] = reset($table);
        }
        return $tables;
    }

}
