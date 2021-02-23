<?php


namespace Okay\Admin\Helpers;


use Okay\Core\Entity\UrlUniqueValidator;
use Okay\Core\Managers;
use Okay\Core\Request;
use Okay\Core\Settings;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\AuthorsEntity;
use Okay\Entities\BlogCategoriesEntity;
use Okay\Entities\BlogEntity;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\CouponsEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\ManagersEntity;
use Okay\Entities\PagesEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\UsersEntity;

class BackendValidateHelper
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * @var UrlUniqueValidator
     */
    private $urlUniqueValidator;

    /**
     * @var Managers
     */
    private $managers;

    public function __construct(
        EntityFactory      $entityFactory,
        Settings           $settings,
        Request            $request,
        UrlUniqueValidator $urlUniqueValidator,
        Managers           $managers
    ){
        $this->entityFactory      = $entityFactory;
        $this->settings           = $settings;
        $this->request            = $request;
        $this->urlUniqueValidator = $urlUniqueValidator;
        $this->managers           = $managers;
    }

    public function getProductValidateError($product, $productCategories)
    {
        $productsEntity = $this->entityFactory->get(ProductsEntity::class);

        $error = '';
        if (empty($product->name)) {
            $error = 'empty_name';
        } elseif (empty($product->url)) {
            $error = 'empty_url';
        } elseif (($p = $productsEntity->get($product->url)) && $p->id != $product->id) {
            $error = 'url_exists';
        } elseif ($this->settings->get('global_unique_url') && !$this->urlUniqueValidator->validateGlobal($product->url, ProductsEntity::class, $product->id)) {
            $error = 'global_url_exists';
        } elseif (substr($product->url, -1) == '-' || substr($product->url, 0, 1) == '-') {
            $error = 'url_wrong';
        } elseif (empty($productCategories)) {
            $error = 'empty_categories';
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }

    public function getCategoryValidateError($category)
    {
        $categoriesEntity = $this->entityFactory->get(CategoriesEntity::class);

        $error = '';
        if (($c = $categoriesEntity->get($category->url)) && $c->id != $category->id) {
            $error = 'url_exists';
        } elseif ($this->settings->get('global_unique_url') && !$this->urlUniqueValidator->validateGlobal($category->url, CategoriesEntity::class, $category->id)) {
            $error = 'global_url_exists';
        } elseif (empty($category->name)) {
            $error = 'empty_name';
        } elseif (empty($category->url)) {
            $error = 'empty_url';
        } elseif (substr($category->url, -1) == '-' || substr($category->url, 0, 1) == '-') {
            $error = 'url_wrong';
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }

    public function getBrandsValidateError($brand)
    {
        $brandsEntity = $this->entityFactory->get(BrandsEntity::class);

        $error = '';
        if (($b = $brandsEntity->get($brand->url)) && $b->id!=$brand->id) {
            $error = 'url_exists';
        } elseif ($this->settings->get('global_unique_url') && !$this->urlUniqueValidator->validateGlobal($brand->url, BrandsEntity::class, $brand->id)) {
            $error = 'global_url_exists';
        } elseif(empty($brand->name)) {
            $error = 'empty_name';
        } elseif(empty($brand->url)) {
            $error = 'empty_url';
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }

    public function getBlogCategoryValidateError($category)
    {
        $categoriesEntity = $this->entityFactory->get(BlogCategoriesEntity::class);

        $error = '';
        if (($c = $categoriesEntity->get($category->url)) && $c->id != $category->id) {
            $error = 'url_exists';
        } elseif ($this->settings->get('global_unique_url') && !$this->urlUniqueValidator->validateGlobal($category->url, BlogCategoriesEntity::class, $category->id)) {
            $error = 'global_url_exists';
        } elseif (empty($category->name)) {
            $error = 'empty_name';
        } elseif (empty($category->url)) {
            $error = 'empty_url';
        } elseif (substr($category->url, -1) == '-' || substr($category->url, 0, 1) == '-') {
            $error = 'url_wrong';
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }
    
    public function getAuthorsValidateError($author)
    {
        $authorsEntity = $this->entityFactory->get(AuthorsEntity::class);

        $error = '';
        if (($b = $authorsEntity->get($author->url)) && $b->id!=$author->id) {
            $error = 'url_exists';
        } elseif ($this->settings->get('global_unique_url') && !$this->urlUniqueValidator->validateGlobal($author->url, AuthorsEntity::class, $author->id)) {
            $error = 'global_url_exists';
        } elseif(empty($author->name)) {
            $error = 'empty_name';
        } elseif(empty($author->url)) {
            $error = 'empty_url';
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }

    public function getUsersValidateError($user)
    {
        $usersEntity = $this->entityFactory->get(UsersEntity::class);

        $error = '';
        if (empty($user->name)) {
            $error = 'empty_name';
        } elseif (empty($user->email)) {
            $error = 'empty_email';
        } elseif (($u = $usersEntity->get($user->email)) && $u->id!=$user->id) {
            $error = 'login_exists';
        }
        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }

    public function getUserGroupsValidateError($group)
    {
        $error = '';
        if (empty($group->name)) {
            $error = 'empty_name';
        }
        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }

    public function getBlogValidateError($post)
    {
        
        /** @var BlogEntity $blogEntity */
        $blogEntity = $this->entityFactory->get(BlogEntity::class);

        $error = '';
        if (($b = $blogEntity->get($post->url)) && $b->id!=$post->id) {
            $error = 'url_exists';
        } elseif ($this->settings->get('global_unique_url') && !$this->urlUniqueValidator->validateGlobal($post->url, BlogEntity::class, $post->id)) {
            $error = 'global_url_exists';
        } elseif(empty($post->name)) {
            $error = 'empty_name';
        } elseif(empty($post->url)) {
            $error = 'empty_url';
        } elseif (substr($post->url, -1) == '-' || substr($post->url, 0, 1) == '-') {
            $error = 'url_wrong';
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }

    public function getDeliveriesValidateError($delivery)
    {
        $error = '';
        if(empty($delivery->name)) {
            $error = 'empty_name';
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }

    public function getPaymentValidateError($payment)
    {
        $error = '';
        if(empty($payment->name)) {
            $error = 'empty_name';
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }

    public function getCouponsValidateError($coupon)
    {
        $couponsEntity = $this->entityFactory->get(CouponsEntity::class);

        $error = '';
        if(($a = $couponsEntity->get((string)$coupon->code)) && $a->id != $coupon->id) {
            $error = 'code_exists';
        } elseif(empty($coupon->code)) {
            $error = 'empty_code';
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }

    public function getTruncateTableValidateError()
    {
        $managersEntity = $this->entityFactory->get(ManagersEntity::class);

        $error = '';
        $pass = $this->request->post('truncate_table_password');
        $manager = $managersEntity->get($_SESSION['admin']);
        if (! $this->managers->checkPassword($pass, $manager->password)) {
            $error = 'truncate_table_password_failed';
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }

    public function getFaviconValidateError()
    {
        $error = '';
        if (!empty($_FILES['site_favicon']['name'])) {
            $ext = pathinfo($_FILES['site_favicon']['name'],PATHINFO_EXTENSION);
            if (!in_array($ext, ['png', 'gif', 'jpg', 'jpeg', 'ico', 'svg'])) {
                $error = 'wrong_favicon_ext';
            }
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }

    public function getSiteLogoValidateError()
    {
        $error = '';

        $ext = pathinfo($_FILES['site_logo']['name'],PATHINFO_EXTENSION);
        if (!in_array($ext, ['png', 'gif', 'jpg', 'jpeg', 'ico', 'svg'])) {
            $error = 'wrong_favicon_ext';
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }

    public function getFeatureValidateError($feature)
    {
        $featuresEntity = $this->entityFactory->get(FeaturesEntity::class);

        $error = '';
        if (($f = $featuresEntity->get($feature->url)) && $f->id!=$feature->id) {
            $error = 'duplicate_url';
        } elseif(empty($feature->name)) {
            $error = 'empty_name';
        } elseif (!$featuresEntity->checkAutoId($feature->id, $feature->auto_name_id)) {
            $error = 'auto_name_id_exists';
        } elseif (!$featuresEntity->checkAutoId($feature->id, $feature->auto_value_id, "auto_value_id")) {
            $error = 'auto_value_id_exists';
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }

    public function getPageValidateError($page)
    {
        $pagesEntity = $this->entityFactory->get(PagesEntity::class);

        $error = '';
        if (($p = $pagesEntity->get((string)$page->url)) && $p->id!=$page->id) {
            $error = 'url_exists';
        } elseif (empty($page->name)) {
            $error = 'empty_name';
        } elseif (substr($page->url, -1) == '-' || substr($page->url, 0, 1) == '-') {
            $error = 'url_wrong';
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }

    public function getChangeSystemUrlValidateErrors($page)
    {
        $pagesEntity = $this->entityFactory->get(PagesEntity::class);

        $checkPage = $pagesEntity->get(intval($page->id));

        $error = '';
        if (in_array($checkPage->url, $pagesEntity->getSystemPages()) && $page->url != $checkPage->url) {
            $error = 'url_system';
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }
}