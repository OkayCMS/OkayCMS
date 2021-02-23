<?php


namespace Okay\Modules\OkayCMS\Banners\Extenders;


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\Modules\Module;
use Okay\Modules\OkayCMS\Banners\Entities\BannersEntity;
use Okay\Modules\OkayCMS\Banners\Entities\BannersImagesEntity;
use Okay\Modules\OkayCMS\Banners\Helpers\BannersHelper;

class FrontExtender implements ExtensionInterface
{
    
    private $entityFactory;
    private $design;
    private $module;
    private $totalBannersHtml = '';
    private $shortCodesParts = [];
    private $bannersHelper;

    public function __construct(EntityFactory $entityFactory, Design $design, Module $module, BannersHelper $bannersHelper)
    {
        $this->entityFactory = $entityFactory;
        $this->design = $design;
        $this->module = $module;
        $this->bannersHelper = $bannersHelper;

        $this->init();
    }

    public function assignCurrentBanners()
    {
        if (!empty($this->shortCodesParts)) {
            foreach ($this->shortCodesParts as $individualShortCode => $codesPart) {
                $this->design->assign($individualShortCode, $codesPart);
            }
        }
        $this->design->assign('global_banners', $this->totalBannersHtml);
    }
    
    public function metadataGetParts(array $parts = [])
    {
        $shortCodeParts = [];
        if (!empty($this->shortCodesParts)) {
            foreach ($this->shortCodesParts as $individualShortCode => $codesPart) {
                $shortCodeParts['{$' . $individualShortCode . '}'] = $codesPart;
            }
        }
        $parts = array_merge($parts, $shortCodeParts);
        return $parts;
    }
    
    public function init()
    {
        // Устанавливаем директорию HTML из модуля
        $this->design->setModuleDir(__CLASS__);

        $showOnFilter = $this->bannersHelper->getShowOnFilter();
        
        if ($this->design->getVar('product')) {
            $bannersFilter = [
                'visible' => true,
                'show_all_products' => true,
            ];
        } else {
            $bannersFilter = [
                'visible' => true,
                'show_on' => $showOnFilter,
            ];
        }

        $banners = $this->bannersHelper->getBanners($bannersFilter);

        if(!empty($banners)) {
            foreach ($banners as $banner) {
                if (!empty($banner->settings)) {
                    $banner->settings = unserialize($banner->settings);
                }
                $this->design->assign('banner_data', $banner);
                // Если баннер отмечен как шорткод, передадим такую переменную в дизайн
                if (!empty($banner->as_individual_shortcode)) {
                    $bannerHtml = $this->design->fetch('show_banner.tpl');
                    $this->shortCodesParts['banner_shortcode_' . $banner->group_name] = $bannerHtml;
                } else {
                    $this->totalBannersHtml .= $this->design->fetch('show_banner.tpl');
                }
            }
        }
        
        // Вернём обратно стандартную директорию шаблонов
        $this->design->rollbackTemplatesDir();
    }
}