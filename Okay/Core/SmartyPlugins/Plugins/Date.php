<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\Languages;
use Okay\Core\EntityFactory;
use Okay\Entities\TranslationsEntity;
use Okay\Entities\LanguagesEntity;
use Okay\Core\SmartyPlugins\Modifier;

class Date extends Modifier
{
    private $translations;
    private $languages;
    private $langEntity;
    private $dateFormat;

    public function __construct(EntityFactory $entityFactory, Languages $languages) 
    {
        $this->translations = $entityFactory->get(TranslationsEntity::class);
        $this->langEntity   = $entityFactory->get(LanguagesEntity::class);
        $this->languages    = $languages;
        
    }

    public function setDateFormat($dateFormat)
    {
        $this->dateFormat   = $dateFormat;
    }
    
    public function run($date, $format = null) 
    {
        if (is_numeric($date) || (!$time = strtotime($date))) {
            $time = $date;
        }
        if ($format !== null) {
            $language = $this->langEntity->get($this->languages->getLangId());
            
            $translations = $this->translations->find(['lang' => $language->label]);
    
            $day_num = date('N', $time);
            $mon_num = date('n', $time);
            $custom_format = [
                'cD'  => addcslashes($translations->{"date_D_{$day_num}"}, 'A..z'), // Дни недели сокращенно
                'cl'  => addcslashes($translations->{"date_l_{$day_num}"}, 'A..z'), // Дни недели полностью
                'cS'  => addcslashes($translations->{"date_S_{$mon_num}"}, 'A..z'), // Месяцы сокращенно
                'cF'  => addcslashes($translations->{"date_F_{$mon_num}"}, 'A..z'), // Месяцы полностью
                'cFR' => addcslashes($translations->{"date_FR_{$mon_num}"}, 'A..z'), // Месяцы полностью, родительный падеж
            ];
    
            $format = strtr($format, $custom_format);
        }
        
        return date(!empty($format) ? $format : $this->dateFormat, $time);
    }
}