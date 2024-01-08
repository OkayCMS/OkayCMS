<?php


namespace Okay\Modules\OkayCMS\Banners\Requests;


use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Request;
use Okay\Modules\OkayCMS\Banners\DTO\BannerSettingsDTO;

class BannersRequest
{

    /** @var Request */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function postBanner()
    {
        $banner = new \stdClass;
        $banner->id = $this->request->post('id', 'integer');
        $banner->as_individual_shortcode = $this->request->post('as_individual_shortcode', 'integer');
        $banner->name = $this->request->post('name');
        $banner->group_name = $this->request->post('group_name');
        $banner->visible = $this->request->post('visible', 'boolean');
        $banner->show_all_pages = (int)$this->request->post('show_all_pages');
        $banner->show_all_products = (int)$this->request->post('show_all_products');
        $banner->categories = implode(",",$this->request->post('categories', null, []));
        $banner->brands = implode(",",$this->request->post('brands', null, []));
        $banner->pages = implode(",",$this->request->post('pages', null, []));

        $bannerSettings = new BannerSettingsDTO();
        $bannerSettings->fromArray($this->request->post('settings'));
        $banner->settings = serialize($bannerSettings);

        return ExtenderFacade::execute(__METHOD__, $banner, func_get_args());
    }
}