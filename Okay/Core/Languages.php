<?php

namespace Okay\Core;


use HaydenPierce\ClassFinder\ClassFinder;
use Okay\Core\Entity\Entity;
use Okay\Entities\LanguagesEntity;

class Languages
{
    
    private $languagesList = [];
    private $mainLanguage;
    private $langId;
    private $availableLanguages;

    /**
     * @var Database 
     */
    private $db;

    /**
     * @var Request 
     */
    private $request;

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    public function __construct(Database $db, Request $request, QueryFactory $queryFactory)
    {
        $this->db = $db;
        $this->request = $request;
        $this->queryFactory = $queryFactory;
        $this->configureLanguages();
    }

    private function configureLanguages()
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])
            ->from(LanguagesEntity::getTable())
            ->orderBy(['position ASC']);
        
        $this->db->query($select);
        $this->languagesList = $this->db->results(null, 'id');
        
        $this->mainLanguage = reset($this->languagesList);
    }
    
    /*Выборка списка языков сайта*/
    public function getLangList()
    {
        if (!isset($this->availableLanguages)) {
            include_once("backend/lang/languages_list.php");
            $this->availableLanguages = isset($langs) ? $langs : [];
        }
        return $this->availableLanguages;
    }
    
    public function getAllLanguages()
    {
        return $this->languagesList;
    }
    
    public function getMainLanguage()
    {
        return $this->mainLanguage;
    }
    
    /*Выборка ID текущего языка*/
    public function getLangId()
    {
        if (empty($this->languagesList)) {
            return null;
        }
        
        if (!empty($this->langId)) {
            return $this->langId;
        }
        
        if (empty($this->langId) && !empty($_SESSION['lang_id']) && !empty($this->languagesList[$_SESSION['lang_id']])) {
            $this->langId  = intval($_SESSION['lang_id']);
        }

        if (empty($this->langId)) {
            $this->langId = (int)$this->mainLanguage->id;
        }
        return $this->langId;
    }

    /*Установка ID языка*/
    public function setLangId($id)
    {
        $id = (int)$id;
        if (!isset($this->languagesList[$id])) {
            $id = (int)$this->mainLanguage->id;
        }
        
        $this->langId = $_SESSION['lang_id'] = $id;
    }

    public function getLangLabel($langId = null)
    {
        if ($langId === null) {
            $langId = $this->getLangId();
        }

        if (!isset($this->languagesList[$langId])) {
            return false;
        }

        $currentLanguage = $this->languagesList[$langId];
        return $currentLanguage->label;
    }
    
    public function getLangLink($langId = null)
    {
        $langLink = '';
        if ($langId === null) {
            $langId = $this->getLangId();
        }
        
        if (!isset($this->languagesList[$langId])) {
            return false;
        }
        
        $currentLanguage = $this->languagesList[$langId];
        
        if (!empty($this->mainLanguage) && !empty($currentLanguage) && $currentLanguage->id !== $this->mainLanguage->id) {
            $langLink = $currentLanguage->label . '/';
        }
        return $langLink;
    }

    /*Выборка мультиязычных данных*/
    public function getDescription($data, $fields, $clear = true)
    {
        if (empty($this->languagesList)) {
            return false;
        }
        
        $intersect = array_intersect($fields, array_keys((array)$data));
        if (!empty($intersect)) {
            $description = new \stdClass;
            foreach ($fields as $f) {
                if (isset($data->$f)) {
                    $description->$f = $data->$f;
                }
                if ($clear === true && $this->mainLanguage->id != $this->getLangId()) {
                    unset($data->$f);
                }
            }
            $result = new \stdClass();
            $result->description = $description;
            return $result;
        }
        return false;
    }

    /*Выборка мультиязычных данных и их дальнейшая обработка*/
    public function actionDescription($objectIds, $description, $fields, $langObject, $langTable, $updateLangId = null)
    {
        $objectIds = (array)$objectIds;
        if (!empty($this->languagesList) && !empty($fields)) {
            if (!empty($updateLangId)) {
                $upd_languages[] = $this->languagesList[$updateLangId];
            } else {
                $upd_languages = $this->languagesList;
            }
            
            foreach ($upd_languages as $lang) {
                $description->lang_id = $lang->id;
                foreach ($objectIds as $objectId) {
                    $this->actionData($objectId, $description, $langObject, $langTable);
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function getQuery($tableAlias, $langTable, $langObject, $params = [])
    {
        // Если не передали языковую таблицу, значит языков для данной сущности нет
        if (empty($langTable)) {
            return null;
        }
        
        $langAlias = $this->getLangAlias($tableAlias, $params);

        $lang = (isset($params['lang']) && $params['lang'] ? $params['lang'] : $this->getLangId());

        if (!empty($params['px'])) {
            $px = $params['px'];
        } else {
            $px = $tableAlias;
        }

        if (!empty($lang) && $this->getLangId() !== null) {
            $langJoin = $langTable . ' AS ' . $langAlias;
            $cond = $langAlias . '.' . $langObject . '_id = ' . $px . '.id AND ' . $langAlias.'.lang_id = '.(int)$lang;
        } else {
            $langJoin = '';
            $cond = '';
        }

        $result['join'] = $langJoin;
        $result['cond'] = $cond;

        return $result;
    }

    public function getLangAlias($tableAlias, $params = [])
    {
        $lang = (isset($params['lang']) && $params['lang'] ? $params['lang'] : $this->getLangId());

        if (!empty($params['px'])) {
            $px = $params['px'];
        } else {
            $px = $tableAlias;
        }

        if (!empty($lang) && $this->getLangId() !== null) {
            $langAlias = (isset($params['px_lang']) && $params['px_lang'] ? $params['px_lang'] : 'l');
        } else {
            $langAlias = $px;
        }
        
        return $langAlias;
    }

    public function getEntitiesLangInfo()
    {
        $results = [];
        $namespace = 'Okay\Entities';
        if ($classes = ClassFinder::getClassesInNamespace($namespace)) {
            /** @var Entity $class */
            foreach ($classes as $class) {
                if ($class::getLangTable()) {
                    $result = new \stdClass();
                    $result->langTable = $class::getLangTable();
                    $result->table = $class::getTable();
                    $result->object = $class::getLangObject();
                    $result->fields = $class::getLangFields();
                    $results[] = $result;
                }
            }
        }
        
        return $results;
    }
    
    /*Действия над мультиязычным контентом*/
    private function actionData($objectId, $data, $langObject, $langTable)
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['count(*)' => 'count'])
            ->from($langTable)
            ->where('lang_id = :action_object_lang_id')
            ->where($langObject . '_id = :action_object_id');
        
        $select->bindValues([
            'action_object_lang_id' => $data->lang_id,
            'action_object_id' => $objectId
        ]);
        
        $this->db->query($select);
        
        $dataLang = $this->db->result('count');
        
        if ($dataLang == 0) {
            $insert = $this->queryFactory->newInsert();
            $objectField   = $langObject . '_id';
            $data->$objectField = $objectId;
            
            $insert->into($langTable)
                ->cols((array)$data);
            
            $this->db->query($insert);
        } elseif ($dataLang == 1) {
            $update = $this->queryFactory->newUpdate();
            $update->table($langTable)
                ->cols((array)$data)
                ->where('lang_id = :action_object_lang_id')
                ->where($langObject . '_id = :action_object_id');

            $update->bindValues([
                'action_object_lang_id' => $data->lang_id,
                'action_object_id' => $objectId
            ]);
            
            $this->db->query($update);
        }
    }
    
}
