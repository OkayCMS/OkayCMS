<?php


namespace Okay\Admin\Requests;


use Okay\Core\Request;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendProductsRequest
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function postProduct()
    {
        $product = new \stdClass();
        $product->id       = $this->request->post('id', 'integer');
        $product->name     = $this->request->post('name');
        $product->visible  = $this->request->post('visible', 'integer');
        $product->featured = $this->request->post('featured', 'integer');
        $product->brand_id = $this->request->post('brand_id', 'integer');

        $product->url              = trim($this->request->post('url', 'string'));
        $product->meta_title       = $this->request->post('meta_title');
        $product->meta_keywords    = $this->request->post('meta_keywords');
        $product->meta_description = $this->request->post('meta_description');

        $product->annotation  = $this->request->post('annotation');
        $product->description = $this->request->post('description');
        $product->rating      = $this->request->post('rating', 'float');
        $product->votes       = $this->request->post('votes', 'integer');
        $product->special     = $this->request->post('special','string');

        return ExtenderFacade::execute(__METHOD__, $product, func_get_args());
    }

    public function postVariants()
    {
        $postFields = $this->request->post('variants');

        if (empty($postFields)) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        $productVariants = [];
        foreach ($postFields as $n=>$va) {
            foreach ($va as $i=>$v) {
                if (empty($productVariants[$i])) {
                    $productVariants[$i] = new \stdClass();
                }
                if (empty($v) && in_array($n, ['id', 'weight'])) {
                    $v = null;
                }
                $productVariants[$i]->$n = $v;
            }
        }

        foreach($productVariants as $key => $variant) {
            if (empty($variant->name)         &&
                empty($variant->sku)          &&
                trim($variant->price)         === "" &&
                trim($variant->compare_price) === ""
            ) {
                unset($productVariants[$key]);
            }
        }

        if (empty($productVariants)) {
            $mockVariant = new \stdClass();
            $mockVariant->name  = '';
            $mockVariant->price = 0;
            $mockVariant->sku   = '';

            $productVariants[] = $mockVariant;
        }

        return ExtenderFacade::execute(__METHOD__, $productVariants, func_get_args());
    }

    public function postCategories()
    {
        $productCategories = $this->request->post('categories');
        if (is_array($productCategories)) {
            $pc = [];
            foreach ($productCategories as $c) {
                $x = new \stdClass();
                $x->id = $c;
                $pc[$x->id] = $x;
            }
            $productCategories = $pc;
        }

        return ExtenderFacade::execute(__METHOD__, $productCategories, func_get_args());
    }

    public function postRelatedProducts()
    {
        if (is_array($this->request->post('related_products'))) {
            $rp = [];
            foreach($this->request->post('related_products') as $p) {
                $rp[$p] = new \stdClass();
                $rp[$p]->product_id = $this->request->post('id', 'integer');
                $rp[$p]->related_id = $p;
            }
            $relatedProducts = $rp;
        } else {
            $relatedProducts = [];
        }

        return ExtenderFacade::execute(__METHOD__, $relatedProducts, func_get_args());
    }

    public function postImages()
    {
        $images = (array) $this->request->post('images_ids');
        return ExtenderFacade::execute(__METHOD__, $images, func_get_args());
    }

    public function fileDroppedImages()
    {
        $droppedImages = $this->request->files('dropped_images');
        return ExtenderFacade::execute(__METHOD__, $droppedImages, func_get_args());
    }

    public function postSpecialImages()
    {
        $images = (array) $this->request->post('spec_images_ids');
        return ExtenderFacade::execute(__METHOD__, $images, func_get_args());
    }

    public function fileDroppedSpecialImages()
    {
        $droppedImages = $this->request->files('spec_dropped_images');
        return ExtenderFacade::execute(__METHOD__, $droppedImages, func_get_args());
    }

    public function postFeaturesValues()
    {
        $featuresValues     = $this->request->post('features_values');
        return ExtenderFacade::execute(__METHOD__, $featuresValues, func_get_args());
    }

    public function postFeaturesValuesText()
    {
        $featuresValuesText     = $this->request->post('features_values_text');
        return ExtenderFacade::execute(__METHOD__, $featuresValuesText, func_get_args());
    }

    public function postNewFeaturesNames()
    {
        $newFeaturesNames     = $this->request->post('new_features_names');
        return ExtenderFacade::execute(__METHOD__, $newFeaturesNames, func_get_args());
    }

    public function postNewFeaturesValues()
    {
        $newFeaturesValues     = $this->request->post('new_features_values');
        return ExtenderFacade::execute(__METHOD__, $newFeaturesValues, func_get_args());
    }

    public function postCheckedIds()
    {
        $check = $this->request->post('check');
        return ExtenderFacade::execute(__METHOD__, $check, func_get_args());
    }

    public function postPrices()
    {
        $prices = $this->request->post('price');
        return ExtenderFacade::execute(__METHOD__, $prices, func_get_args());
    }

    public function postStocks()
    {
        $stocks = $this->request->post('stock');
        return ExtenderFacade::execute(__METHOD__, $stocks, func_get_args());
    }

    public function postPositions()
    {
        $positions = $this->request->post('positions');
        return ExtenderFacade::execute(__METHOD__, $positions, func_get_args());
    }
}