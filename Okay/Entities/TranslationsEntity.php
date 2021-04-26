<?php


namespace Okay\Entities;


use Okay\Core\EntityFactory;
use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Modules;
use Okay\Core\TemplateConfig\FrontTemplateConfig;
use Okay\Core\ServiceLocator;
use Okay\Core\Modules\Extender\ExtenderFacade;

class TranslationsEntity extends Entity
{
    const TRANS_T_THEME   = 'theme';
    const TRANS_T_GENERAL = 'general';
    const TRANS_T_MODULE  = 'module';

    /** @var FrontTemplateConfig */
    private $frontTemplateConfig;

    /** @var LanguagesEntity */
    private $languagesEntity;

    /** @var Modules */
    private $modules;


    /** @var array general translations */
    private $generalVars = [];

    /** @var array theme translations */
    private $themeVars = [];

    /** @var array modules translations */
    private $modulesVars = [];

    /** @var array all translations */
    private $vars = [];

    public function __construct()
    {
        parent::__construct();
        $serviceLocator = ServiceLocator::getInstance();
        $this->frontTemplateConfig = $serviceLocator->getService(FrontTemplateConfig::class);
        $this->modules             = $serviceLocator->getService(Modules::class);

        $this->languagesEntity = $serviceLocator->getService(EntityFactory::class)->get(LanguagesEntity::class);
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
        
        foreach ($this->languagesEntity->find() as $l) {
            $modulesResult = $this->initOneModulesTranslation($l->label);
            $themeResult   = $this->initOneThemeTranslation($l->label);
            $generalResult = $this->initOneGeneralTranslation($l->label);
            if (isset($modulesResult[$id])) {
                $translation['lang_' . $l->label] = $modulesResult[$id];
                $translation['values'][$l->id]    = $modulesResult[$id];
            } else if (isset($themeResult[$id])) {
                $translation['lang_' . $l->label] = $themeResult[$id];
                $translation['values'][$l->id]    = $themeResult[$id];
            } else if (isset($generalResult[$id])) {
                $translation['lang_' . $l->label] = $generalResult[$id];
                $translation['values'][$l->id]    = $generalResult[$id];
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
        $force = false;
        
        if (isset($filter['force'])) {
            $force = $filter['force'];
        }
        
        if (!empty($filter['lang_id']) && ($lang = $this->languagesEntity->get((int)$filter['lang_id']))) {
            $result = $this->initOneTranslation($lang->label, $force);
        } elseif (!empty($filter['lang'])) {
            $result = $this->initOneTranslation($filter['lang'], $force);
        } else {
            $result = $this->initTranslations($force);
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

        return ExtenderFacade::execute([static::class, __FUNCTION__], $result, func_get_args());
    }

    public function update($id, $data)
    {
        $data = (array)$data;
        $this->initTranslations(true);
        foreach ($this->languagesEntity->find() as $l) {
            if (isset($this->modulesVars[$l->label][$id])) {
                $translation = $this->vars[$l->label][$id];
                if ($id != $data['label']) {
                    unset($this->modulesVars[$l->label][$id]);
                    unset($this->vars[$l->label][$id]);
                }

                $translation->value = $data['lang_'.$l->label];
                $this->modulesVars[$l->label][$data['label']] = $translation;
                $this->vars[$l->label][$data['label']] = $translation;
                $this->writeModuleTranslation(
                    $l->label,
                    $id,
                    $data['label'],
                    $translation->value,
                    $translation->module->vendor,
                    $translation->module->name
                );

                if (isset($this->themeVars[$l->label][$id]) && $id != $data['label']) {
                    $translation = $this->themeVars[$l->label][$id];
                    unset($this->themeVars[$l->label][$id]);
                    $this->themeVars[$l->label][$data['label']] = $translation;
                    $this->writeThemeTranslations($l->label, $this->themeVars[$l->label]);
                }
            } else {
                if (isset($this->themeVars[$l->label][$id]) && $id != $data['label']) {
                    unset($this->themeVars[$l->label][$id]);
                }
                $translation = (object) [
                    'value' => $data['lang_'.$l->label],
                    'type' => self::TRANS_T_THEME
                ];
                $this->vars[$l->label][$data['label']]      = $translation;
                $this->themeVars[$l->label][$data['label']] = $translation;
                $this->writeThemeTranslations($l->label, $this->themeVars[$l->label]);
            }
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

        foreach ($this->languagesEntity->find() as $l) {
            foreach ($ids as $id) {
                unset($this->themeVars[$l->label][$id]);

                if (!isset($this->modulesVars[$l->label][$id])) {
                    unset($this->vars[$l->label][$id]);
                }
            }
            $this->writeThemeTranslations($l->label, $this->themeVars[$l->label]);
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

    public function initTranslations($force = false)
    {
        foreach ($this->languagesEntity->find() as $l) {
            $this->initOneTranslation($l->label, $force);
        }
        return ExtenderFacade::execute([static::class, __FUNCTION__], $this->vars, func_get_args());
    }

    public function initOneTranslation($langLabel, $force = false)
    {
        if (empty($langLabel)) {
            return false;
        }

        if ($force === true) {
            unset($this->vars[$langLabel]);
        }

        if (!isset($this->vars[$langLabel])) {
            $translations = [];
            if ($generalVars = $this->initOneGeneralTranslation($langLabel, $force)) {
                foreach ($generalVars as $id => $generalVar) {
                    $translations[$id] = $generalVar;
                }
            }
            if ($themeVars = $this->initOneThemeTranslation($langLabel, $force)) {
                foreach ($themeVars as $id => $themeVar) {
                    $translations[$id] = $themeVar;
                }
            }
            if ($modulesVars = $this->initOneModulesTranslation($langLabel, $force)) {
                foreach ($modulesVars as $id => $moduleVar) {
                    $translations[$id] = $moduleVar;
                }
            }
            $this->vars[$langLabel] = ExtenderFacade::execute([static::class, __FUNCTION__], $translations, func_get_args());
        }

        return $this->vars[$langLabel];// no ExtenderFacade
    }

    public function initOneGeneralTranslation($langLabel, $force = false)
    {
        if (empty($langLabel)) {
            return false;
        }

        if ($force === true) {
            unset($this->generalVars[$langLabel]);
        }

        if (!isset($this->generalVars[$langLabel])) {
            $translations = [];
            $fileLangGeneral = __DIR__ . '/../lang_general/' . $langLabel . '.php';
            if (file_exists($fileLangGeneral)) {
                $lang_general = [];
                if ($force === false) {
                    require_once $fileLangGeneral;
                } else {
                    require $fileLangGeneral;
                }

                foreach ($lang_general as $id => $translationValue) {
                    $translations[$id] = (object) [
                        'value' => $translationValue,
                        'type'  => self::TRANS_T_GENERAL
                    ];
                }
            }
            $this->generalVars[$langLabel] = ExtenderFacade::execute([static::class, __FUNCTION__], $translations, func_get_args());
        }
        return $this->generalVars[$langLabel];// no ExtenderFacade
    }

    public function initOneThemeTranslation($langLabel, $force = false)
    {
        if (empty($langLabel)) {
            return false;
        }

        if ($force === true) {
            unset($this->themeVars[$langLabel]);
        }
        
        if (!isset($this->themeVars[$langLabel])) {
            $translations = [];
            $langFile = $this->getReadLangFile($langLabel, $this->frontTemplateConfig->getTheme());
            if (file_exists($langFile)) {
                $lang = array();
                if ($force === false) {
                    require_once $langFile;
                } else {
                    require $langFile;
                }

                foreach ($lang as $id => $translationValue) {
                    $translations[$id] = (object)[
                        'value' => $translationValue,
                        'type' => self::TRANS_T_THEME
                    ];
                }
            }
            $this->themeVars[$langLabel] = ExtenderFacade::execute([static::class, __FUNCTION__], $translations, func_get_args());
        }
        return $this->themeVars[$langLabel];// no ExtenderFacade
    }

    public function initOneModulesTranslation($langLabel, $force = false)
    {
        if (empty($langLabel)) {
            return false;
        }

        if ($force === true) {
            unset($this->modulesVars[$langLabel]);
        }

        if (!isset($this->modulesVars[$langLabel])) {
            $translations = [];
            foreach ($this->modules->getRunningModules() as $runningModule) {
                $moduleTranslations = $this->modules->getModuleFrontTranslations($runningModule['vendor'], $runningModule['module_name'], $langLabel);
                foreach ($moduleTranslations as $id => $translation) {
                    $translations[$id] = (object) [
                        'value'  => $translation,
                        'type'   => self::TRANS_T_MODULE,
                        'module' => (object) [
                            'vendor' => $runningModule['vendor'],
                            'name'   => $runningModule['module_name']
                        ]
                    ];
                }
            }
            $this->modulesVars[$langLabel] = ExtenderFacade::execute([static::class, __FUNCTION__], $translations, func_get_args());
        }
        return $this->modulesVars[$langLabel];// no ExtenderFacade
    }

    private function writeThemeTranslations($langLabel, $translations)
    {
        if (empty($langLabel)) {
            return false;
        }

        $langFile = $this->getWriteLangFile($langLabel, $this->frontTemplateConfig->getTheme());

        $translationsToWrite = [];
        foreach ($translations as $id => $translation) {
            $translationsToWrite[$id] = $translation->value;
        }

        $this->writeTranslationsToLangFile($langFile, $translationsToWrite);
            
        return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }

    private function writeModuleTranslation(
        $langLabel,
        $oldId,
        $newId,
        $translation,
        $vendor,
        $name
    ) {
        if (empty($langLabel)) {
            return false;
        }

        $translations = $this->modules->getModuleFrontTranslations($vendor, $name, $langLabel);
        if ($oldId != $newId) {
            unset($translations[$oldId]);
        }
        $translations[$newId] = $translation;

        $this->writeTranslationsToLangFile($this->getWriteModuleLangFile($langLabel, $vendor, $name), $translations);

        return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }

    public function writeTranslationsToLangFile($langFile, $translations)
    {
        $content = "<?php\n\n";
        $content .= "\$lang = [];\n";
        foreach($translations as $label => $translation) {
            $content .= "\$lang['".$label."'] = '".addcslashes($translation, "\n\r\\\"'")."';\n";
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

    private function getWriteModuleLangFile($langLabel, $vendor, $name)
    {
        $langFile = $this->modules->getModuleFrontTranslationsLangFile($vendor, $name, $langLabel);
        return ExtenderFacade::execute([static::class, __FUNCTION__], $langFile, func_get_args());
    }
}
