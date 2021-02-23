<?php


namespace Okay\Entities;


use Okay\Core\EntityFactory;
use Okay\Core\Entity\Entity;
use Okay\Core\TemplateConfig\FrontTemplateConfig;
use Okay\Core\ServiceLocator;
use Okay\Core\Modules\Extender\ExtenderFacade;

class TranslationsEntity extends Entity
{

    /**
     * @var FrontTemplateConfig
     */
    private $frontTemplateConfig;

    /**
     * @var LanguagesEntity
     */
    private $languages;

    private $templateOnly = false;
    private $vars = [];

    public function __construct()
    {
        parent::__construct();
        $serviceLocator = ServiceLocator::getInstance();
        $this->frontTemplateConfig = $serviceLocator->getService(FrontTemplateConfig::class);
        $this->languages      = $serviceLocator->getService(EntityFactory::class)->get(LanguagesEntity::class);
    }
    
    public function templateOnly($state)
    {
        $this->templateOnly = (bool)$state;
        return $this;
    }
    
    public function flush()
    {
        $this->templateOnly = false;
        parent::flush();
    }

    public function get($id) 
    {
        $translation = [];
        
        foreach ($this->languages->find() as $l) {
            $result = $this->initOneTranslation($l->label, $this->templateOnly);
            if (isset($result[$id])) {
                $translation['lang_' . $l->label] = $result[$id];
                $translation['values'][$l->id] = $result[$id];
            }
        }

        $this->flush();
        
        if (count($translation) > 0) {
            $translation['id'] = $id;
            $translation['label'] = $id;
            $result = (object) $translation;
            return ExtenderFacade::execute([static::class, __FUNCTION__], $result, func_get_args());
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
    }

    public function find(array $filter = [])
    {
        $template_only = false;
        $force = false;
        
        if (isset($filter['template_only'])) {
            $template_only = $filter['template_only'];
        }
        
        if ($this->templateOnly === true) {
            $template_only = true;
        }
        
        if (isset($filter['force'])) {
            $force = $filter['force'];
        }
        
        if (!empty($filter['lang_id']) && ($lang = $this->languages->get((int)$filter['lang_id']))) {
            $result = $this->initOneTranslation($lang->label, $template_only, $force);
        } elseif (!empty($filter['lang'])) {
            $result = $this->initOneTranslation($filter['lang'], $template_only, $force);
        } else {
            die('get_translations empty(filter["lang"])');
        }
        if (!empty($filter['sort'])) {
            switch ($filter['sort']) {
                case 'label':
                    ksort($result);
                    break;
                case 'label_desc':
                    krsort($result);
                    break;
                case 'date_desc':
                    $result = array_reverse($result);
                    break;
                case 'translation':
                    asort($result);
                    break;
                case 'translation_desc':
                    arsort($result);
                    break;
            }
        }
        $this->flush();

        return ExtenderFacade::execute([static::class, __FUNCTION__], (object) $result, func_get_args());
    }

    public function update($id, $data)
    {
        $data = (array)$data;
        $this->initTranslations(true);
        foreach ($this->vars as $langLabel=>&$translations) {
            if ($id != $data['label']) {
                unset($translations[$id]);
            }
            $translations[$data['label']] = $data['lang_'.$langLabel];
            $this->writeTranslations($langLabel, $translations);
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $data['label'], func_get_args());
    }

    public function delete($ids)
    {
        $ids = (array)$ids;
        if (empty($ids)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
        }

        $this->initTranslations(true);
        foreach ($this->vars as $langLabel=>&$translations) {
            foreach ($ids as $id) {
                unset($translations[$id]);
            }
            $this->writeTranslations($langLabel, $translations);
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }
    
    /*Дублирование переводов*/
    public function copyTranslations($labelSrc, $labelDest) 
    {
        if (empty($labelSrc) || empty($labelDest) || $labelSrc == $labelDest) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
        }

        $themesDir = __DIR__ . '/../../design/';
        foreach (glob($themesDir.'*', GLOB_ONLYDIR) as $theme) {
            if (file_exists($theme.'/lang/')) {
                $src = $theme.'/lang/'.$labelSrc.'.php';
                $dest = $theme.'/lang/'.$labelDest.'.php';
                if (file_exists($src) && !file_exists($dest)) {
                    copy($src, $dest);
                    @chmod($dest, 0664);
                }
            }
        }

        // Копируем общие переводы
        $generalDir = dirname(__DIR__) . '/lang_general/';
        if (file_exists($generalDir.$labelSrc.'.php')) {
            $src = $generalDir.$labelSrc.'.php';
            $dest = $generalDir.$labelDest.'.php';
            if (file_exists($src) && !file_exists($dest)) {
                copy($src, $dest);
                @chmod($dest, 0664);
            }
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }

    /**
     * @param $label
     * Метод удаляет все переводы фронта по лейблу языка
     * @return null
     */
    public function deleteLang($label)
    {
        if (empty($label)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
        }

        $themesDir = __DIR__ . '/../../design/';
        foreach (glob($themesDir.'*', GLOB_ONLYDIR) as $theme) {
            if (file_exists($theme.'/lang/')) {
                @unlink($theme.'/lang/'.$label.'.php');
            }
        }

        // Удаляем общие переводы
        $generalDir = dirname(__DIR__) . '/lang_general/';
        if (file_exists($generalDir.$label.'.php')) {
            @unlink($generalDir.$label.'.php');
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }

    private function initTranslations($template_only = false) 
    {
        foreach ($this->languages->find() as $l) {
            $this->initOneTranslation($l->label, $template_only);
        }
        return ExtenderFacade::execute([static::class, __FUNCTION__], $this->vars, func_get_args());
    }

    private function initOneTranslation($label = "", $template_only = false, $force = false) 
    {
        if (empty($label)) {
            return false;
        }

        if ($force === true) {
            unset($this->vars[$label]);
        }
        
        if (!isset($this->vars[$label])) {
            
            $langFile = $this->getReadLangFile($label, $this->frontTemplateConfig->getTheme());
            
            if (file_exists($langFile)) {
                $lang = array();
                if ($force === false) {
                    require_once $langFile;
                } else {
                    require $langFile;
                }
                
                // Подключаем файл переводов по умолчанию, но с возможностью переопределить в самом шаблоне
                if ($template_only === false) {
                    $fileLangGeneral = __DIR__ . '/../lang_general/' . $label . '.php';
                    if (file_exists($fileLangGeneral)) {
                        $lang_general = array();
                        if ($force === false) {
                            require_once $fileLangGeneral;
                        } else {
                            require $fileLangGeneral;
                        }
                        $lang = $lang + $lang_general;
                    }
                }

                $this->vars[$label] = ExtenderFacade::execute([static::class, __FUNCTION__], $lang, func_get_args());
            } else {
                $this->vars[$label] = ExtenderFacade::execute([static::class, __FUNCTION__], [], func_get_args());
            }
        }

        return $this->vars[$label];// no ExtenderFacade
    }

    private function writeTranslations($langLabel, $translations)
    {
        if (empty($langLabel)) {
            return false;
        }

        $langFile = $this->getWriteLangFile($langLabel, $this->frontTemplateConfig->getTheme());
        
        $content = "<?php\n\n";
        $content .= "\$lang = [];\n";
        foreach($translations as $label=>$value) {
            $content .= "\$lang['".$label."'] = '".addcslashes($value, "\n\r\\\"'")."';\n";
        }
        $file = fopen($langFile, 'w');
        fwrite($file, $content);
        fclose($file);
            
        return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }
    
    private function getReadLangFile($langLabel, $theme)
    {
        $langFile = __DIR__ . '/../../design/' . $theme . '/lang/' . $langLabel . '.php';
        return ExtenderFacade::execute([static::class, __FUNCTION__], $langFile, func_get_args());
    }
    
    private function getWriteLangFile($langLabel, $theme)
    {
        $langFile = __DIR__ . '/../../design/' . $theme . '/lang/' . $langLabel . '.php';
        return ExtenderFacade::execute([static::class, __FUNCTION__], $langFile, func_get_args());
    }
}
