<?php


namespace Okay\Modules\OkayCMS\Banners\Requests;


use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Request;
use Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO;

class BannersImagesRequest
{
    
    /** @var Request */
    private $request;
    
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function postBannerImage()
    {
        $bannersImage = new \stdClass();
        $bannersImage->id = $this->request->post('id', 'integer');
        $bannersImage->name = $this->request->post('name');
        $bannersImage->visible = $this->request->post('visible', 'boolean');
        $bannersImage->banner_id = $this->request->post('banner_id', 'integer');
        $bannersImage->is_lang_banner = $this->request->post('is_lang_banner', 'integer');

        $bannersImage->url = $this->request->post('url');
        $bannersImage->title = $this->request->post('title');
        $bannersImage->alt = $this->request->post('alt');
        $bannersImage->description = $this->request->post('description');

        $settings = $this->request->post('settings');
        $bannerImageSettingsDTO = new BannerImageSettingsDTO();
        $bannerImageSettingsDTO->setDesktopWidth((int)($settings['desktop_width'] ?? 0));
        $bannerImageSettingsDTO->setDesktopHeight((int)($settings['desktop_height'] ?? 0));
        $bannerImageSettingsDTO->setMobileWidth((int)($settings['mobile_width'] ?? 0));
        $bannerImageSettingsDTO->setMobileHeight((int)($settings['mobile_height'] ?? 0));
        $bannerImageSettingsDTO->setVariantShow($settings['variant_show'] ?? '');
        $bannerImageSettingsDTO->setMobileVariantShow($settings['mobile_variant_show'] ?? null);

        $bannersImage->settings = serialize($bannerImageSettingsDTO);

        return ExtenderFacade::execute(__METHOD__, $bannersImage, func_get_args());
    }

    public function postDeleteImage()
    {
        $deleteImage = $this->request->post('delete_image');
        return ExtenderFacade::execute(__METHOD__, $deleteImage, func_get_args());
    }

    public function fileImage()
    {
        $image = $this->request->files('image');
        return ExtenderFacade::execute(__METHOD__, $image, func_get_args());
    }

    public function mobileFileImage()
    {
        $image = $this->request->files('image_mobile');
        return ExtenderFacade::execute(__METHOD__, $image, func_get_args());
    }
    
    public function postCheck()
    {
        $check = (array) $this->request->post('check');
        return ExtenderFacade::execute(__METHOD__, $check, func_get_args());
    }

    public function postAction()
    {
        $action = $this->request->post('action');
        return ExtenderFacade::execute(__METHOD__, $action, func_get_args());
    }

    public function postPositions()
    {
        $positions = $this->request->post('positions');
        return ExtenderFacade::execute(__METHOD__, $positions, func_get_args());
    }
}