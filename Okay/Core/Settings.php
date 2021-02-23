<?php


namespace Okay\Core;


/**
 * Управление настройками магазина, хранящимися в базе данных
 * В отличие от класса Config оперирует настройками доступными админу и хранящимися в базе данных.
 */
class Settings
{
    
    private $vars;
    private $vars_lang;
    
    /**
     * @var Database
     */
    private $db;

    /**
     * @var Languages
     */
    private $languages;

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    public function __construct(Database $db, Languages $languages, QueryFactory $queryFactory)
    {
        $this->db = $db;
        $this->languages = $languages;
        $this->queryFactory = $queryFactory;
        $this->initSettings();
    }
    
    public function get($param)
    {
        if (isset($this->vars_lang[$param])) {
            return $this->vars_lang[$param];
        } elseif (isset($this->vars[$param])) {
            return $this->vars[$param];
        } else {
            return null;
        }
    }
    
    public function has($param)
    {
        return isset($this->vars_lang[$param]) || isset($this->vars[$param]);
    }
    
    public function set($param, $value)
    {
        if (isset($this->vars_lang[$param])) {
            return;
        }
        
        if(is_array($value)) {
            $valuePrepared = serialize($value);
        } else {
            $valuePrepared = (string) $value;
        }
        
        if(!isset($this->vars[$param])) {
            $insert = $this->queryFactory->newInsert();
            $insert->into('__settings')
                ->cols([
                    'value' => $valuePrepared,
                    'param' => $param,
                ]);

            $this->db->query($insert);
        } elseif ($this->vars[$param] != $value) {
            $update = $this->queryFactory->newUpdate();
            $update->table('__settings')
                ->cols(['value' => $valuePrepared])
                ->where('param = :param');
            $update->bindValue('param', $param);

            $this->db->query($update);
        }

        $this->vars[$param] = $value;
    }
    
    public function __get($param)
    {
        return $this->get($param);
    }

    /*Запись данных в общие настройки*/
    public function __set($param, $value)
    {
        $this->set($param, $value);
    }

    /*Выборка всех данных с таблиц настроек*/
    public function initSettings()
    {
        // Выбираем из базы ОБЩИЕ настройки и записываем их в переменную
        $this->vars = [];
        
        $select = $this->queryFactory->newSelect();
        
        $this->db->query($select->cols(['param', 'value'])->from('__settings'));
        foreach($this->db->results() as $result) {
            if($this->isSerializable($result->value)) {
                $this->vars[$result->param] = unserialize($result->value);;
            } else {
                $this->vars[$result->param] = $result->value;
            }
        }
    
        // Выбираем из базы настройки с переводами к текущему языку
        $this->vars_lang = [];
        $multi = $this->getSettings();
        if (is_array($multi)) {
            foreach ($multi as $s) {
                if($this->isSerializable($s->value)) {
                    $this->vars_lang[$s->param] = unserialize($s->value);
                } else {
                    $this->vars_lang[$s->param] = $s->value;
                }
            }
        }
    }

    private function isSerializable($value)
    {
        $unserializedValue = @unserialize($value);
        return (is_array($unserializedValue) || is_object($unserializedValue));
    }
    
    /**
     * Adding a new setting for all languages
     * @param string $param
     * @param string $value
     * @return bool
     * @throws \Exception
     */
    private function add($param, $value)
    {
        $select = $this->queryFactory->newSelect();
        $select->from(\Okay\Entities\LanguagesEntity::getTable())
            ->cols(['id']);
        $this->db->query($select);
        $languagesIds = $this->db->results('id');
        
        if (!empty($languagesIds)) {
            foreach ($languagesIds as $lId) {

                $delete = $this->queryFactory->newDelete();
                $delete->from('__settings_lang')
                    ->where('param =:param')
                    ->where('lang_id =:lang_id')
                    ->bindValue('param', $param)
                    ->bindValue('lang_id', $lId);

                $this->db->query($delete);
                
                $insert = $this->queryFactory->newInsert();
                $insert->into('__settings_lang')
                    ->cols([
                        'param' => $param,
                        'value' => $value,
                        'lang_id' => $lId,
                    ]);

                $this->db->query($insert);
                
            }
        } else {
            $delete = $this->queryFactory->newDelete();
            $delete->from('__settings_lang')
                ->where('param =:param')
                ->bindValue('param', $param);

            $this->db->query($delete);

            $insert = $this->queryFactory->newInsert();
            $insert->into('__settings_lang')
                ->cols([
                    'param' => $param,
                    'value' => $value,
                ]);

            if (!$this->db->query($insert)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Updating by $param(current language), or adding;
     * if a setting with specified $param is exist - it will be updated,
     * otherwise it will be added(called add() function).
     * @param string $param
     * @param string $value
     * @return bool
     * @throws \Exception
     */
    public function update($param, $value)
    {
        if (empty($param)) {
            return false;
        }
        $this->vars_lang[$param] = $value;
        $value = is_array($value) ? serialize($value) : (string) $value;

        $select = $this->queryFactory->newSelect();
        $select->from('__settings_lang')
            ->cols(['1'])
            ->where('param = :param')
            ->bindValue('param', $param)
            ->limit(1);
        
        $this->db->query($select);
        if (!$this->db->result()) {
            return $this->add($param, $value);
        } else {
            $delete = $this->queryFactory->newDelete();
            $delete->from('__settings_lang')
                ->where('param =:param')
                ->bindValue('param', $param);
                
            $insert = $this->queryFactory->newInsert();
            $insert->into('__settings_lang')
                ->cols([
                    'param' => $param,
                    'value' => $value,
                ]);

            if ($langId = $this->languages->getLangId()) {
                $delete->where('lang_id =:lang_id')
                    ->bindValue('lang_id', $langId);
                $insert->cols(['lang_id' => $langId]);
            }

            $this->db->query($delete);
            $this->db->query($insert);
            
            return true;
        }
    }

    /**
     * Getting settings.
     * if $langId is not specified, a current language will be returned.
     * $langId = 0 is wrong, will be returned false.
     * @param int $langId
     * @return array|bool
     * @throws \Exception
     */
    public function getSettings($langId = null)
    {
        $select = $this->queryFactory->newSelect();
        $select->from(\Okay\Entities\LanguagesEntity::getTable())
            ->cols(['id'])
            ->where("id=" . (int)$langId)
            ->limit(1);
        $this->db->query($select);
        
        if (!is_null($langId) && !$this->db->results('id')) {
            return false;
        }

        $select = $this->queryFactory->newSelect();
        $select->from('__settings_lang')
            ->cols(['*']);
        
        $langId  = !is_null($langId) ? $langId : $this->languages->getLangId();
        if($langId) {
            $select->where('lang_id=:action_object_lang_id');
            $select->bindValues(['action_object_lang_id'=>$langId]);
        }
        $this->db->query($select);
        return $this->db->results();
    }

}
