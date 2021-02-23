<?php


namespace Okay\Admin\Requests;


use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Request;

class BackendSettingsRequest
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function postTruncateTableConfirm()
    {
        $confirm = $this->request->post('truncate_table_confirm');
        return ExtenderFacade::execute(__METHOD__, $confirm, func_get_args());
    }

    public function postCounters()
    {
        $counters = [];
        if ($this->request->post('counters')) {
            foreach ($this->request->post('counters') as $n=>$co) {
                foreach ($co as $i=>$c) {
                    if (empty($counters[$i])) {
                        $counters[$i] = new \stdClass;
                    }
                    $counters[$i]->$n = $c;
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $counters, func_get_args());
    }

    public function filesFavicon()
    {
        $siteFavicon = $this->request->files('site_favicon');
        return ExtenderFacade::execute(__METHOD__, $siteFavicon, func_get_args());
    }

    public function postFavicon()
    {
        $favicon = $this->request->post('site_favicon');
        return ExtenderFacade::execute(__METHOD__, $favicon, func_get_args());
    }

    public function postMultiLangLogo()
    {
        $multiLangLogo = $this->request->post('multilang_logo', 'integer');
        return ExtenderFacade::execute(__METHOD__, $multiLangLogo, func_get_args());
    }

    public function filesSiteLogo()
    {
        $siteLogo = $this->request->files('site_logo');
        return ExtenderFacade::execute(__METHOD__, $siteLogo, func_get_args());
    }

    public function postSiteLogo()
    {
        $siteLogo = $this->request->post('site_logo');
        return ExtenderFacade::execute(__METHOD__, $siteLogo, func_get_args());
    }

    public function filesAdvantageImages()
    {
        $images = (array) $this->request->files('advantages_image');

        if (empty($images['name'])) {
            return ExtenderFacade::execute(__METHOD__, [], func_get_args());
        }

        $advantageImages = [];
        foreach($images['name'] as $advantageId => $imageName) {
            $advantageImage = [];
            $advantageImage['name']        = $imageName;
            $advantageImage['tmp_name']    = $images['tmp_name'][$advantageId];
            $advantageImages[$advantageId] = $advantageImage;
        }

        return ExtenderFacade::execute(__METHOD__, $advantageImages, func_get_args());
    }

    public function filesNewAdvantageImages()
    {
        $images = $this->request->files('new_advantage_images');

        if (empty($images)) {
            return ExtenderFacade::execute(__METHOD__, [], func_get_args());
        }

        $newAdvantageImages = [];
        foreach($images['name'] as $key => $imageName) {
            $newAdvantageImage = [];
            $newAdvantageImage['name']     = $imageName;
            $newAdvantageImage['tmp_name'] = $images['tmp_name'][$key];
            $newAdvantageImages[$key]      = $newAdvantageImage;
        }

        return ExtenderFacade::execute(__METHOD__, $newAdvantageImages, func_get_args());
    }

    public function postAdvantageImagesToDelete()
    {
        $deleteAdvantageImages = (array) $this->request->post('advantage_images_to_delete');

        $preparedDeleteAdvantageImages = [];
        foreach($deleteAdvantageImages as $advantageId => $deleteAdvantageImage) {
            if (!empty($deleteAdvantageImage)) {
                $preparedDeleteAdvantageImages[] = $advantageId;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $preparedDeleteAdvantageImages, func_get_args());
    }


    public function postAdvantagesUpdates()
    {
        $advantages = [];

        $position = 0;
        foreach((array) $this->request->post('advantages_text') as $id => $advantageText) {
            $advantage           = new \stdClass();
            $advantage->text     = $advantageText;
            $advantage->position = $position;

            $advantages[$id] = $advantage;

            $position++;
        }

        return ExtenderFacade::execute(__METHOD__, $advantages, func_get_args());
    }

    public function postNewAdvantages()
    {
        $newAdvantages = $this->request->post('new_advantages');

        if (empty($newAdvantages)) {
            return ExtenderFacade::execute(__METHOD__, [], func_get_args());
        }

        $preparedNewAdvantages = [];
        foreach($newAdvantages['text'] as $key => $text) {
            $newAdvantage = new \stdClass();
            $newAdvantage->text = $text;
            $preparedNewAdvantages[] = $newAdvantage;
        }

        return ExtenderFacade::execute(__METHOD__, $preparedNewAdvantages, func_get_args());
    }

    public function postCheckAdvantage()
    {
        $ids = $this->request->post('check');
        return ExtenderFacade::execute(__METHOD__, $ids, func_get_args());
    }

    public function postActionAdvantage()
    {
        $action = $this->request->post('action');
        return ExtenderFacade::execute(__METHOD__, $action, func_get_args());
    }

    public function postDeleteAdvantageImages()
    {
        $deleteImages = $this->request->post('delete_image');

        if (empty($deleteImages)) {
            return ExtenderFacade::execute(__METHOD__, [], func_get_args());
        }

        foreach($deleteImages as $key => $deleteImage) {
            if (empty($deleteImage)) {
                unset($deleteImages[$key]);
            }
        }

        return ExtenderFacade::execute(__METHOD__, $deleteImages, func_get_args());
    }

    public function postPositionAdvantage()
    {
        $positions = $this->request->post('positions');
        return ExtenderFacade::execute(__METHOD__, $positions, func_get_args());
    }
}