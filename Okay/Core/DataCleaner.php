<?php


namespace Okay\Core;


use Okay\Entities\AuthorsEntity;
use Okay\Entities\BlogCategoriesEntity;
use Okay\Entities\BlogEntity;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CommentsEntity;
use Okay\Entities\FeaturesAliasesEntity;
use Okay\Entities\FeaturesValuesAliasesValuesEntity;
use Okay\Entities\ImagesEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\VariantsEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\PurchasesEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\FeaturesValuesEntity;
use Okay\Entities\FeaturesAliasesValuesEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class DataCleaner
{
    /**
     * @var Database
     */
    private $db;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var QueryFactory
     */
    private $queryFactory;
    
    public function __construct(Database $database, Config $config, QueryFactory $queryFactory)
    {
        $this->db           = $database;
        $this->config       = $config;
        $this->queryFactory = $queryFactory;
    }

    public function clearCatalogData()
    {
        $sql = $this->queryFactory->newSqlQuery()->setStatement("DELETE FROM ".CommentsEntity::getTable()." WHERE `type`='product'");
        $this->db->query($sql);

        $sql = $this->queryFactory->newSqlQuery()->setStatement("UPDATE ".PurchasesEntity::getTable()." SET `product_id`=0, `variant_id`=0");
        $this->db->query($sql);

        $this->truncateTable(CategoriesEntity::getTable());
        $this->truncateTable(CategoriesEntity::getLangTable());
        $this->truncateTable('__categories_features');

        $this->truncateTable(FeaturesEntity::getTable());
        $this->truncateTable(FeaturesEntity::getLangTable());
        $this->truncateTable(FeaturesAliasesEntity::getTable());
        $this->truncateTable(FeaturesAliasesEntity::getLangTable());
        $this->truncateTable(FeaturesAliasesValuesEntity::getTable());
        $this->truncateTable(FeaturesAliasesValuesEntity::getLangTable());
        $this->truncateTable(FeaturesValuesEntity::getTable());
        $this->truncateTable(FeaturesValuesEntity::getLangTable());
        $this->truncateTable(FeaturesValuesAliasesValuesEntity::getTable());

        $this->truncateTable(ProductsEntity::getTable());
        $this->truncateTable(ProductsEntity::getLangTable());
        $this->truncateTable(ImagesEntity::getTable());
        $this->truncateTable('__related_products');
        $this->truncateTable('__products_categories');
        $this->truncateTable('__products_features_values');
        $this->truncateTable('__related_blogs');
        $this->truncateTable('__user_browsed_products');
        $this->truncateTable('__user_comparison_items');
        $this->truncateTable('__user_wishlist_items');
        $this->truncateTable('__user_cart_items');

        $this->truncateTable(VariantsEntity::getTable());
        $this->truncateTable(VariantsEntity::getLangTable());

        $this->truncateTable('__import_log');

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function clearCategoryData()
    {
        $sql = $this->queryFactory->newSqlQuery()->setStatement("UPDATE ".ProductsEntity::getTable()." SET `main_category_id`=null");
        $this->db->query($sql);

        $this->truncateTable(CategoriesEntity::getTable());
        $this->truncateTable(CategoriesEntity::getLangTable());
        $this->truncateTable('__categories_features');

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function clearBrandsData()
    {
        $sql = $this->queryFactory->newSqlQuery()->setStatement("UPDATE ".ProductsEntity::getTable()." SET `brand_id`=0");
        $this->db->query($sql);

        $this->truncateTable(BrandsEntity::getTable());
        $this->truncateTable(BrandsEntity::getLangTable());

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function clearProductVariantData()
    {
        $sql = $this->queryFactory->newSqlQuery()->setStatement("DELETE FROM ".CommentsEntity::getTable()." WHERE `type`='product'");
        $this->db->query($sql);

        $sql = $this->queryFactory->newSqlQuery()->setStatement("UPDATE ".PurchasesEntity::getTable()." SET `product_id`=0, `variant_id`=0");
        $this->db->query($sql);

        $this->truncateTable(ProductsEntity::getTable());
        $this->truncateTable(ProductsEntity::getLangTable());
        $this->truncateTable(ImagesEntity::getTable());
        $this->truncateTable('__related_products');
        $this->truncateTable('__products_categories');
        $this->truncateTable('__products_features_values');
        $this->truncateTable('__related_blogs');
        $this->truncateTable('__user_browsed_products');
        $this->truncateTable('__user_comparison_items');
        $this->truncateTable('__user_wishlist_items');
        $this->truncateTable('__user_cart_items');

        $this->truncateTable(VariantsEntity::getTable());
        $this->truncateTable(VariantsEntity::getLangTable());

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function clearFeaturesData()
    {
        $this->truncateTable(FeaturesEntity::getTable());
        $this->truncateTable(FeaturesEntity::getLangTable());

        $this->truncateTable(FeaturesAliasesEntity::getTable());
        $this->truncateTable(FeaturesAliasesEntity::getLangTable());
        $this->truncateTable(FeaturesAliasesValuesEntity::getTable());
        $this->truncateTable(FeaturesAliasesValuesEntity::getLangTable());
        $this->truncateTable(FeaturesValuesEntity::getTable());
        $this->truncateTable(FeaturesValuesEntity::getLangTable());
        $this->truncateTable(FeaturesValuesAliasesValuesEntity::getTable());
        //  т.к. свойства удаляются то удаляем и связи с товарами
        $this->truncateTable('__products_features_values');
        //  т.к. свойства удаляются то удаляем и связи с категориями
        $this->truncateTable('__categories_features');

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function clearBlogsData()
    {
        $this->truncateTable('__related_blogs');
        $this->truncateTable(BlogEntity::getTable());
        $this->truncateTable(BlogEntity::getLangTable());

        $this->truncateTable('__blog_categories_relation');
        $this->truncateTable(BlogCategoriesEntity::getTable());
        $this->truncateTable(BlogCategoriesEntity::getLangTable());

        $this->truncateTable(AuthorsEntity::getTable());
        $this->truncateTable(AuthorsEntity::getLangTable());

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function clearResizeImages()
    {
        $this->clearFilesDirs($this->config->get('resized_images_dir'));
        $this->clearFilesDirs($this->config->get('resized_blog_dir'));
        $this->clearFilesDirs($this->config->get('resized_brands_dir'));
        $this->clearFilesDirs($this->config->get('resized_categories_dir'));

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * Очищаем картинки категорий
     */
    public function clearCategoryImages()
    {
        $this->clearFilesDirs($this->config->get('original_categories_dir'));
        $this->clearFilesDirs($this->config->get('resized_categories_dir'));

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * Удаляем картинки товаров
     */
    public function clearProductImages()
    {
        $this->clearFilesDirs($this->config->get('original_images_dir'));
        $this->clearFilesDirs($this->config->get('resized_images_dir'));

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * Удаляем картинки брендов
     */
    public function clearBrandImages()
    {
        $this->clearFilesDirs($this->config->get('original_brands_dir'));
        $this->clearFilesDirs($this->config->get('resized_brands_dir'));

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * Удаляем картинки связанные с блогом
     */
    public function clearBlogImages()
    {
        //  удаление картинок статей
        $this->clearFilesDirs($this->config->get('original_blog_dir'));
        $this->clearFilesDirs($this->config->get('resized_blog_dir'));

        //  удаление картинок карегорий блога
        $this->clearFilesDirs($this->config->get('original_blog_categories_dir'));
        $this->clearFilesDirs($this->config->get('resized_blog_categories_dir'));

        //  удаление картинок автонов
        $this->clearFilesDirs($this->config->get('original_authors_dir'));
        $this->clearFilesDirs($this->config->get('resized_authors_dir'));

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * Удаляем картинки товаров, категорий
     */
    public function clearAllCatalogImages()
    {
        $this->clearProductImages();

        $this->clearCategoryImages();

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    private function clearFilesDirs($dir = '')
    {
        if (empty($dir)) {
            return false;
        }
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && $file != '.keep_folder' && $file != '.htaccess') {
                    @unlink($dir."/".$file);
                }
            }
            closedir($handle);
        }
    }

    private function truncateTable($table)
    {
        $sql = $this->queryFactory->newSqlQuery();
        $sql->setStatement("TRUNCATE TABLE $table");
        $this->db->query($sql);
    }
    
}