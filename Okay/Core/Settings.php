<?php


namespace Okay\Core;


use Okay\Entities\LanguagesEntity;

/**
 * Управление настройками магазина, хранящимися в базе данных
 * В отличие от класса Config оперирует настройками доступными админу и хранящимися в базе данных.
 */
class Settings
{
    private $vars;
    private $vars_lang;
    
    /** @var Database */
    private $db;
    
    /** @var Languages */
    private $languages;
    
    /** @var QueryFactory */
    private $queryFactory;
    
    public function __construct(Database $db, Languages $languages, QueryFactory $queryFactory)
    {
        $this->db = $db;
        $this->languages = $languages;
        $this->queryFactory = $queryFactory;
        $this->initSettings();
    }
    
    public function has(string $param): bool
    {
        return array_key_exists($param, $this->vars_lang) || array_key_exists($param, $this->vars);
    }
    
    public function get(string $param)
    {
        return $this->vars_lang[$param] ?? $this->vars[$param] ?? null;
    }
    
    /** Запись данных в общие настройки */
    public function set(string $param, $value): void
    {
        if (array_key_exists($param, $this->vars_lang)) {
            return;
        }
        
        if (is_array($value)) {
            $valuePrepared = serialize($value);
        } else {
            $valuePrepared = (string)$value;
        }
        
        if (!array_key_exists($param, $this->vars)) {
            $this->queryFactory->newInsert()
                ->into('__settings')
                ->cols([
                    'value' => $valuePrepared,
                    'param' => $param,
                ])->execute();
        } elseif ($this->vars[$param] != $value) {
            $this->queryFactory->newUpdate()
                ->table('__settings')
                ->cols(['value' => $valuePrepared])
                ->where('param = ?', $param)
                ->execute();
        }
        
        $this->vars[$param] = $value;
    }
    
    public function __get($param)
    {
        return $this->get($param);
    }
    
    public function __set($param, $value): void
    {
        $this->set($param, $value);
    }
    
    /** Выборка всех данных из таблиц настроек */
    public function initSettings(): void
    {
        // Выбираем из базы ОБЩИЕ настройки и записываем их в переменную
        $this->vars = [];
        
        $settings = $this->queryFactory->newSelect()
            ->cols(['param', 'value'])
            ->from('__settings')
            ->results();
        foreach ($settings as $s) {
            $this->vars[$s->param] = $this->unserialize($s->value, $s->value);
        }
        
        // Выбираем из базы настройки с переводами к текущему языку
        $this->vars_lang = [];
        $multi = $this->getSettings();
        if (is_array($multi)) {
            foreach ($multi as $s) {
                $this->vars_lang[$s->param] = $this->unserialize($s->value, $s->value);
            }
        }
    }
    
    /**
     * If $data is unserializeable, returns the value, otherwise, returns $default
     * @param string $data
     * @param mixed $default
     * @return mixed
     */
    private function unserialize($in, $default)
    {
        if ($in === '') return $default;
        set_error_handler(function () use (&$out, $default) {
            $out = $default;
        });
        $out = unserialize($in);
        restore_error_handler();
        return $out;
    }
    
    /**
     * Adding a new setting for all languages
     * @param string $param
     * @param string $value
     * @return bool
     * @throws \Exception
     */
    private function add(string $param, string $value): bool
    {
        $languagesIds = $this->queryFactory->newSelect()
            ->cols(['id'])
            ->from(LanguagesEntity::getTable())
            ->results('id');
        
        if ($languagesIds) {
            foreach ($languagesIds as $langId) {
                
                $this->queryFactory->newDelete()
                    ->from('__settings_lang')
                    ->where('param = ?', $param)
                    ->where('lang_id = ?', $langId)
                    ->execute();
                
                $this->queryFactory->newInsert()
                    ->into('__settings_lang')
                    ->cols([
                        'param' => $param,
                        'value' => $value,
                        'lang_id' => $langId,
                    ])->execute();
            }
        } else {
            $this->queryFactory->newDelete()
                ->from('__settings_lang')
                ->where('param = ?', $param)
                ->execute();

            $insert = $this->queryFactory->newInsert()
                ->into('__settings_lang')
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
     * Updating by $param (current language), or adding;
     * if a setting with specified $param exists - it will be updated,
     * otherwise, it will be added(add() function will be called).
     * @param string $param
     * @param string|array $value
     * @return bool
     * @throws \Exception
     */
    public function update(string $param, $value): bool
    {
        if (empty($param)) {
            return false;
        }
        $this->vars_lang[$param] = $value;
        $value = is_array($value) ? serialize($value) : (string)$value;
        
        $select = $this->queryFactory->newSelect()
            ->cols(['1'])
            ->from('__settings_lang')
            ->where('param = ?', $param)
            ->limit(1);
        
        if (!$select->result()) {
            return $this->add($param, $value);
        } else {
            $delete = $this->queryFactory->newDelete()
                ->from('__settings_lang')
                ->where('param = ?', $param);
            
            $insert = $this->queryFactory->newInsert()
                ->into('__settings_lang')
                ->cols([
                    'param' => $param,
                    'value' => $value,
                ]);
            
            if ($langId = $this->languages->getLangId()) {
                $delete->where('lang_id = ?', $langId);
                $insert->cols(['lang_id' => $langId]);
            }
            
            $delete->execute();
            $insert->execute();
            
            return true;
        }
    }
    
    /**
     * Getting settings.
     * If $langId is not specified, the current language will be used.
     * $langId = 0 is wrong, false will be returned.
     * @param int|null $langId
     * @return array|false
     * @throws \Exception
     */
    public function getSettings(?int $langId = null)
    {
        $langExists = $this->queryFactory->newSelect()
            ->cols(['1'])
            ->from(LanguagesEntity::getTable())
            ->where('id = ?', $langId)
            ->limit(1)
            ->result();
        
        if (!is_null($langId) && !$langExists) {
            return false;
        }
        
        $select = $this->queryFactory->newSelect()
            ->cols(['*'])
            ->from('__settings_lang');
        
        $langId = $langId ?? $this->languages->getLangId();
        if ($langId) {
            $select->where('lang_id = :action_object_lang_id');
            $select->bindValues(['action_object_lang_id' => $langId]);
        }
        return $select->results();
    }
    
}
