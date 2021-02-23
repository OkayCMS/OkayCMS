<?php


namespace Okay\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Request;
use Okay\Core\Response;
use Okay\Core\Router;
use Okay\Core\Settings;
use Okay\Entities\BlogEntity;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\PagesEntity;
use Okay\Entities\ProductsEntity;

class SiteMapHelper
{
    
    private $entityFactory;
    private $response;
    private $settings;
    
    private $language;
    private $siteMapIndex = 1;
    private $urlIndex = 0;
    private $params = [];

    const MAX_URLS = 50000;
    
    public function __construct(EntityFactory $entityFactory, Response $response, MainHelper $mainHelper, Settings $settings)
    {
        $this->entityFactory = $entityFactory;
        $this->response = $response;
        $this->settings = $settings;
        $this->language = $mainHelper->getCurrentLanguage();

        if ($argv = Request::getArgv()) {
            $this->params['output'] = 'file';
            $this->params['root_url'] = preg_replace("~^(https?://[^/]+)/.*$~", "$1", $argv['root_url']);
        } else {
            if (isset($_GET['output']) && $_GET['output'] == 'file') {
                $this->params['output'] = 'file';
            } else {
                $this->params['output'] = 'browser';
            }
            $this->params['root_url'] = Request::getRootUrl();
        }
        $this->params['lang_label'] = '_'.$this->language->label;
        if ($this->params['output'] == 'file') {
            $this->removeSiteMap();
        }
    }
    
    public function writeHead()
    {
        $this->writeString("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
        $this->writeString("<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n");
    }
    
    public function writeFooter()
    {
        $this->writeString("</urlset>\n");
        // Если пишем в файл, создадим один итоговый файл со ссылками на все сайтмапы
        if ($this->params['output'] == 'file') {
            $last_modify = date("Y-m-d");
            $file = 'sitemap'.$this->params['lang_label'].'.xml';
            file_put_contents($file, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
            file_put_contents($file, "<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n", FILE_APPEND);
            for ($i = 1; $i <= $this->siteMapIndex; $i++) {
                $url = $this->params['root_url'].'/sitemap'.$this->params['lang_label'].'_'.$i.'.xml';
                file_put_contents($file, "\t<sitemap>"."\n", FILE_APPEND);
                file_put_contents($file, "\t\t<loc>$url</loc>"."\n", FILE_APPEND);
                file_put_contents($file, "\t\t<lastmod>$last_modify</lastmod>"."\n", FILE_APPEND);
                file_put_contents($file, "\t</sitemap>"."\n", FILE_APPEND);
            }
            file_put_contents($file, '</sitemapindex>'."\n", FILE_APPEND);
            return;
        }
    }

    /**
     * Метод по умолчанию ничего не делает, но модули могут через него что-то своё добавить
     */
    public function writeCustomProcedure()
    {
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
    
    public function writePagesProcedure()
    {
        /** @var PagesEntity $pagesEntity */
        $pagesEntity = $this->entityFactory->get(PagesEntity::class);
        
        /** @var BlogEntity $blogEntity */
        $blogEntity = $this->entityFactory->get(BlogEntity::class);

        // Главная страница
        $page = [
            'url' => Router::generateUrl('main', [], true),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        $page = ExtenderFacade::execute(__METHOD__, $page, func_get_args());
        $this->write($page, true);
        
        foreach ($pagesEntity->find(['visible'=>1]) as $p) {
            if ($p->url && $p->url != '404') {
                $lastModify = [];
                if ($p->url == 'blog') {
                    $lastModify = $blogEntity->cols(['last_modify'])->order('last_modify_desc')->find(['limit'=>1]);
                    $lastModify[] = $this->settings->get('lastModifyPosts');
                }
                $lastModify[] = $p->last_modify;
                $lastModify = max($lastModify);
                $lastModify = substr($lastModify, 0, 10);
                
                $page = [
                    'url' => Router::generateUrl('page', ['url' => $p->url], true),
                    'lastmod' => $lastModify,
                    'changefreq' => 'daily',
                    'priority' => '1.0',
                ];

                $page = ExtenderFacade::execute(__METHOD__, $page, func_get_args());
                $this->write($page, true);
            }
        }
    }

    public function writeBlogProcedure()
    {
        /** @var BlogEntity $blogEntity */
        $blogEntity = $this->entityFactory->get(BlogEntity::class);
        
        $postsCount = $blogEntity->count(['visible'=>1]);
        foreach ($blogEntity->find(['visible'=>1, 'limit'=>$postsCount]) as $p) {
            $url = Router::generateUrl('post', ['url' => $p->url], true);
            $lastModify = substr($p->last_modify, 0, 10);

            $post = [
                'url' => $url,
                'lastmod' => $lastModify,
                'changefreq' => 'daily',
                'priority' => '1.0',
            ];

            $post = ExtenderFacade::execute(__METHOD__, $post, func_get_args());
            $this->write($post, true);
        }
    }

    public function writeCategoriesProcedure()
    {
        /** @var CategoriesEntity $categoriesEntity */
        $categoriesEntity = $this->entityFactory->get(CategoriesEntity::class);
        
        /** @var ProductsEntity $productsEntity */
        $productsEntity = $this->entityFactory->get(ProductsEntity::class);
        
        foreach ($categoriesEntity->find() as $c) {
            if ($c->visible) {
                $url = Router::generateUrl('category', ['url' => $c->url], true);
                $lastModify = $productsEntity->cols(['last_modify'])->order('last_modify_desc')->find([
                    'category_id' => $c->children,
                    'limit'=>1,
                ]);

                $lastModify[] = $c->last_modify;
                $lastModify = substr(max($lastModify), 0, 10);

                $category = [
                    'url' => $url,
                    'lastmod' => $lastModify,
                    'changefreq' => 'daily',
                    'priority' => '1.0',
                ];

                $category = ExtenderFacade::execute(__METHOD__, $category, func_get_args());
                $this->write($category, true);
            }
        }
    }

    public function writeBrandsProcedure()
    {
        /** @var BrandsEntity $brandsEntity */
        $brandsEntity = $this->entityFactory->get(BrandsEntity::class);

        /** @var ProductsEntity $productsEntity */
        $productsEntity = $this->entityFactory->get(ProductsEntity::class);
        
        $brandsCount = $brandsEntity->count(['visible'=>1]);
        foreach ($brandsEntity->find(['visible'=>1, 'limit'=>$brandsCount]) as $b) {
            $url = Router::generateUrl('brand', ['url' => $b->url], true);
            $lastModify = $productsEntity->cols(['last_modify'])->order('last_modify_desc')->find([
                'brand_id' => $b->id,
                'limit'=>1,
            ]);
            $lastModify[] = $b->last_modify;
            $lastModify = substr(max($lastModify), 0, 10);
            $brand = [
                'url' => $url,
                'lastmod' => $lastModify,
                'changefreq' => 'daily',
                'priority' => '1.0',
            ];

            $brand = ExtenderFacade::execute(__METHOD__, $brand, func_get_args());
            $this->write($brand, true);
        }
    }

    public function writeProductsProcedure()
    {
        /** @var ProductsEntity $productsEntity */
        $productsEntity = $this->entityFactory->get(ProductsEntity::class);
        $products = $productsEntity->cols([
            'url',
            'last_modify',
        ])->find(['visible' => 1]);
        foreach ($products as $p) {
            $url = Router::generateUrl('product', ['url' => $p->url], true);
            $lastModify = substr($p->last_modify, 0, 10);

            $product = [
                'url' => $url,
                'lastmod' => $lastModify,
                'changefreq' => 'weekly',
                'priority' => '0.5',
            ];

            $product = ExtenderFacade::execute(__METHOD__, $product, func_get_args());
            $this->write($product, true);
        }
    }
    
    private function removeSiteMap()
    {
        if ($this->params['output'] == 'file') {
            $subSiteMaps = glob("sitemap" . $this->params['lang_label'] . "_*.xml");
            if (is_array($subSiteMaps)) {
                foreach ($subSiteMaps as $siteMap) {
                    @unlink($siteMap);
                }
            }
            if (file_exists("sitemap" . $this->params['lang_label'] . ".xml")) {
                @unlink("sitemap" . $this->params['lang_label'] . ".xml");
            }
        }
    }
    
    public function write(array $params, $countUrl = false)
    {
        if (!empty($params)) {
            $str = "\t<url>\n";
            if (!empty($params['url'])) {
                $str .= "\t\t<loc>{$params['url']}</loc>\n";
            }
            if (!empty($params['lastmod'])) {
                $str .= "\t\t<lastmod>{$params['lastmod']}</lastmod>\n";
            }
            if (!empty($params['changefreq'])) {
                $str .= "\t\t<changefreq>{$params['changefreq']}</changefreq>\n";
            }
            if (!empty($params['priority'])) {
                $str .= "\t\t<priority>{$params['priority']}</priority>\n";
            }
            $str .= "\t</url>\n";

            $this->writeString($str, $countUrl);
        }
    }
    
    private function writeString($str, $countUrl = false)
    {
        if ($this->params['output'] == 'file') {
            $this->fileWrite($str, $countUrl);
        } elseif ($this->params['output'] == 'browser') {
            $this->browserWrite($str, $countUrl);
        }
    }
    
    private function fileWrite($str, $countUrl = false)
    {
        $file = 'sitemap'.$this->params['lang_label'].'_'.$this->siteMapIndex.'.xml';
        file_put_contents($file, $str, FILE_APPEND);
        if ($countUrl && ++$this->urlIndex == self::MAX_URLS) {
            file_put_contents($file, '</urlset>'."\n", FILE_APPEND);
            $this->urlIndex=0;
            $this->siteMapIndex++;
            $file = 'sitemap'.$this->params['lang_label'].'_'.$this->siteMapIndex.'.xml';
            file_put_contents($file, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
            file_put_contents($file, "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n", FILE_APPEND);
        }
    }
    
    private function browserWrite($str, $countUrl = false)
    {
        $this->response->setContent($str, RESPONSE_XML);
    }
}