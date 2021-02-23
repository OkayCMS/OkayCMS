<?php


namespace Okay\Controllers;


use Okay\Helpers\SiteMapHelper;

class SiteMapController extends AbstractController
{
    
    public function renderXml(SiteMapHelper $siteMapHelper)
    {

        chdir(dirname(dirname(__DIR__)));

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
