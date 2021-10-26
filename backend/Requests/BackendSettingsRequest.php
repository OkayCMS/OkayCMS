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
}