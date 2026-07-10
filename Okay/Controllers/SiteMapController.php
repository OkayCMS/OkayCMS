<?php


namespace Okay\Controllers;


use Okay\Core\Languages;
use Okay\Entities\LanguagesEntity;
use Okay\Helpers\SiteMapHelper;

class SiteMapController extends AbstractController
{
    
    public function renderXml(SiteMapHelper $siteMapHelper,
                              Languages   $languages,
                              LanguagesEntity $languagesEntity)
    {

        chdir(dirname(dirname(__DIR__)));

        $languagesList   = $languagesEntity->mappedBy('id')->find();
        $currentLanguage = $languagesList[$languages->getLangId()];

        if (empty($currentLanguage->enabled) && empty($_SESSION['admin'])) {
            return false;
        }

        /*
         * параметры с крона в виде key=val
         * доступные пары:
         * root_url=http://domain.com
         *
         * чтобы сгенерировать файлы с браузера нужно в браузере перейти по ссылке
         * http://domain.com/sitemap.xml?output=file
         */

        $siteMapHelper->writeHead();

        $siteMapHelper->writePagesProcedure();
        $siteMapHelper->writeBlogProcedure();
        $siteMapHelper->writeCategoriesProcedure();
        $siteMapHelper->writeBrandsProcedure();
        $siteMapHelper->writeCustomProcedure();
        $siteMapHelper->writeProductsProcedure();

        $siteMapHelper->writeFooter();
    }
}
