<?php


namespace Okay\Core;


use Okay\Entities\BrandsEntity;
use Okay\Entities\CommentsEntity;
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

        $this->truncateTable(BrandsEntity::getTable());
        $this->truncateTable(BrandsEntity::getLangTable());

        $this->truncateTable(CategoriesEntity::getTable());
        $this->truncateTable(CategoriesEntity::getLangTable());
        $this->truncateTable('__categories_features');

        $this->truncateTable(FeaturesEntity::getTable());
        $this->truncateTable(FeaturesAliasesValuesEntity::getTable());
        $this->truncateTable(FeaturesValuesEntity::getTable());
        $this->truncateTable(FeaturesValuesAliasesValuesEntity::getTable());
        $this->truncateTable(FeaturesEntity::getLangTable());
        $this->truncateTable(FeaturesAliasesValuesEntity::getLangTable());
        $this->truncateTable(FeaturesValuesEntity::getLangTable());

        $this->truncateTable(ProductsEntity::getTable());
        $this->truncateTable(ProductsEntity::getLangTable());
        $this->truncateTable(ImagesEntity::getTable());
        $this->truncateTable('__related_products');
        $this->truncateTable('__products_categories');
        $this->truncateTable('__products_features_values');

        $this->truncateTable(VariantsEntity::getTable());
        $this->truncateTable(VariantsEntity::getLangTable());

        $this->truncateTable('__related_blogs');
        $this->truncateTable('__import_log');

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
    
    public function clearAllCatalogImages()
    {
        $this->clearFilesDirs($this->config->get('original_images_dir'));
        $this->clearFilesDirs($this->config->get('resized_images_dir'));

        $this->clearFilesDirs($this->config->get('original_brands_dir'));
        $this->clearFilesDirs($this->config->get('resized_brands_dir'));

        $this->clearFilesDirs($this->config->get('original_categories_dir'));
        $this->clearFilesDirs($this->config->get('resized_categories_dir'));

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