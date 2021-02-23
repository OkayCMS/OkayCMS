<?php


namespace Okay\Admin\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Request;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesEntity;
use Okay\Entities\ImagesEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\VariantsEntity;

class BackendExportHelper
{
    /**
     * @var ProductsEntity
     */
    private $productsEntity;

    /**
     * @var VariantsEntity
     */
    private $variantsEntity;

    /**
     * @var CategoriesEntity
     */
    private $categoriesEntity;

    /**
     * @var ImagesEntity
     */
    private $imagesEntity;

    /**
     * @var BrandsEntity
     */
    private $brandsEntity;

    /**
     * @var FeaturesEntity
     */
    private $featuresEntity;

    /**
     * @var FeaturesValuesEntity
     */
    private $featuresValuesEntity;

    /**
     * @var Request
     */
    private $request;



    public function __construct(EntityFactory $entityFactory, Request $request)
    {
        $this->productsEntity       = $entityFactory->get(ProductsEntity::class);
        $this->variantsEntity       = $entityFactory->get(VariantsEntity::class);
        $this->categoriesEntity     = $entityFactory->get(CategoriesEntity::class);
        $this->imagesEntity         = $entityFactory->get(ImagesEntity::class);
        $this->brandsEntity         = $entityFactory->get(BrandsEntity::class);
        $this->featuresEntity       = $entityFactory->get(FeaturesEntity::class);
        $this->featuresValuesEntity = $entityFactory->get(FeaturesValuesEntity::class);
        $this->request              = $request;
    }

    public function getColumnsNames()
    {
        $columnsNames = [
            'category'         => 'Category',
            'brand'            => 'Brand',
            'name'             => 'Product',
            'variant'          => 'Variant',
            'sku'              => 'SKU',
            'price'            => 'Price',
            'compare_price'    => 'Old price',
            'currency'         => 'Currency ID',
            'weight'           => 'Weight',
            'stock'            => 'Stock',
            'units'            => 'Units',
            'visible'          => 'Visible',
            'featured'         => 'Featured',
            'meta_title'       => 'Meta title',
            'meta_keywords'    => 'Meta keywords',
            'meta_description' => 'Meta description',
            'annotation'       => 'Annotation',
            'description'      => 'Description',
            'images'           => 'Images',
            'url'              => 'URL',
        ];

        return ExtenderFacade::execute(__METHOD__, $columnsNames, func_get_args());
    }

    public function getConfigParams()
    {
        $params = (object) [
            'column_delimiter'      => ';',
            'values_delimiter'      => ',,',
            'subcategory_delimiter' => '/',
            'products_count'        => 100,
            'export_files_dir'      => 'backend/files/export/',
            'filename'              => 'export.csv',
        ];

        return ExtenderFacade::execute(__METHOD__, $params, func_get_args());
    }

    public function setUp($exportFilesDir, $filename, &$columnsNames, $columnDelimiter, $productsCount)
    {
        session_write_close();
        unset($_SESSION['lang_id']);
        unset($_SESSION['admin_lang_id']);

        $page = $this->request->get('page');
        if(empty($page) || $page==1) {
            $page = 1;
            if(is_writable($exportFilesDir.$filename)) {
                unlink($exportFilesDir.$filename);
            }
        }

        $f = fopen($exportFilesDir.$filename, 'ab');

        $filter = ['page' => $page, 'limit' => $productsCount];
        $featuresFilter = [];
        if (($cid = $this->request->get('category_id', 'integer')) && ($category = $this->categoriesEntity->get($cid))) {
            $featuresFilter['product_category_id'] = $category->children;
            $filter['category_id']         = $category->children;
        }

        if ($brandId = $this->request->get('brand_id', 'integer')) {
            $filter['brand_id'] = $brandId;
        }

        $featuresFilter['limit'] = $this->featuresEntity->count($featuresFilter);
        $features = $this->featuresEntity->find($featuresFilter);
        foreach($features as $feature) {
            $columnsNames[$feature->name] = $feature->name;
        }

        if($page == 1) {
            fputcsv($f, $columnsNames, $columnDelimiter);
        }

        fclose($f);
        return ExtenderFacade::execute(__METHOD__, [$filter, $page], func_get_args());
    }

    public function fetchProducts($filter)
    {
        $products = array();
        foreach($this->productsEntity->find($filter) as $p) {
            $products[$p->id] = (array) $p;
        }

        return ExtenderFacade::execute(__METHOD__, $products, func_get_args());
    }

    public function attachFeatures($products, $valuesDelimiter)
    {
        $productsIds = array_keys($products);

        $featuresValues = [];
        foreach ($this->featuresValuesEntity->find(['product_id' => $productsIds]) as $fv) {
            $featuresValues[$fv->id] = $fv;
        }

        $productsValues = [];
        foreach ($this->featuresValuesEntity->getProductValuesIds($productsIds) as $pv) {
            $productsValues[$pv->product_id][$pv->value_id] = $pv->value_id;
        }

        foreach($products as $pId=>&$product) {
            if (isset($productsValues[$pId])) {
                $productFeatureValues = [];
                foreach($productsValues[$pId] as $valueId) {
                    if(isset($featuresValues[$valueId])) {
                        $feature = $featuresValues[$valueId];
                        $tempFeature = $this->featuresEntity->get(intval($feature->feature_id));
                        $productFeatureValues[$tempFeature->name][] = str_replace(',', '.', trim($feature->value));
                    }
                }

                foreach ($productFeatureValues as $featureName=>$values) {
                    $product[$featureName] = implode($valuesDelimiter, $values);
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $products, func_get_args());
    }

    public function attachCategories($products, $subcategoryDelimiter)
    {
        foreach($products as $pId => &$product) {
            $categories = [];
            $cats = $this->categoriesEntity->getProductCategories($pId);
            foreach($cats as $category) {
                $path = [];
                $cat = $this->categoriesEntity->get((int)$category->category_id);
                if(!empty($cat)) {
                    // Вычисляем составляющие категории
                    foreach($cat->path as $p) {
                        $path[] = str_replace($subcategoryDelimiter, '\\'.$subcategoryDelimiter, $p->name);
                    }
                    // Добавляем категорию к товару
                    $categories[] = implode('/', $path);
                }
            }
            $product['category'] = implode(',, ', $categories);
        }

        return ExtenderFacade::execute(__METHOD__, $products, func_get_args());
    }

    public function attachImages($products)
    {
        $images = $this->imagesEntity->find(['product_id' => array_keys($products)]);
        foreach($images as $image) {
            // Добавляем изображения к товару чезер запятую
            if(empty($products[$image->product_id]['images'])) {
                $products[$image->product_id]['images'] = $image->filename;
            } else {
                $products[$image->product_id]['images'] .= ', '.$image->filename;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $products, func_get_args());
    }

    public function fetchVariants($products)
    {
        $variants = $this->variantsEntity->find(['product_id'=>array_keys($products)]);
        return ExtenderFacade::execute(__METHOD__, $variants, func_get_args());
    }

    public function prepareVariantsData($variant)
    {
        $v                    = [];
        $v['variant']         = $variant->name;
        $v['price']           = $variant->price;
        $v['compare_price']   = $variant->compare_price;
        $v['sku']             = $variant->sku;
        $v['stock']           = $variant->stock;
        $v['weight']          = $variant->weight;
        $v['units']           = $variant->units;
        $v['currency']        = $variant->currency_id;
        if($variant->infinity) {
            $v['stock']       = '';
        }

        return ExtenderFacade::execute(__METHOD__, $v, func_get_args());
    }

    public function attachBrands($products)
    {
        $allBrands = [];
        $brandsCount = $this->brandsEntity->count();
        foreach ($this->brandsEntity->find(['limit'=>$brandsCount]) as $b) {
            $allBrands[$b->id] = $b;
        }

        foreach($products as &$product) {
            if ($product['brand_id'] && isset($allBrands[$product['brand_id']])) {
                $product['brand'] = $allBrands[$product['brand_id']]->name;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $products, func_get_args());
    }

    public function exportRun($exportFilesDir, $filename,  $products, $filter, $columnsNames, $columnDelimiter, $productsCount, $page)
    {
        $f = fopen($exportFilesDir.$filename, 'ab');

        foreach($products as &$product) {
            if(isset($product['variants'])) {
                $variants = $product['variants'];
                unset($product['variants']);

                foreach($variants as $variant) {
                    $res = [];
                    $result =  $product;
                    foreach($variant as $name=>$value) {
                        $result[$name]=$value;
                    }

                    foreach($columnsNames as $internalName=>$columnName) {
                        if(isset($result[$internalName])) {
                            $res[$internalName] = str_replace(["\r\n", "\r", "\n"], '', $result[$internalName]);
                        } else {
                            $res[$internalName] = '';
                        }
                    }
                    fputcsv($f, $res, $columnDelimiter);
                }
            }
        }

        $totalProducts = $this->productsEntity->count($filter);
        fclose($f);

        if ($productsCount * $page < $totalProducts) {
            return ['end' => false, 'page' => $page, 'totalpages' => $totalProducts/$productsCount];
        }

        $data = ['end' => true, 'page' => $page, 'totalpages' => $totalProducts/$productsCount];
        file_put_contents($exportFilesDir.$filename, iconv( "utf-8", "windows-1251//IGNORE", file_get_contents($exportFilesDir.$filename)));
        return $data;
    }

    public function getBrandsForExportFilter($brandsCount)
    {
        $brands = [];
        $brands = $this->brandsEntity->find(['limit'=>$brandsCount]);
        return ExtenderFacade::execute(__METHOD__, $brands, func_get_args());
    }

    public function getCategoriesForExportFilter()
    {
        $categories = [];
        $categories = $this->categoriesEntity->getCategoriesTree();
        return ExtenderFacade::execute(__METHOD__, $categories, func_get_args());
    }

}