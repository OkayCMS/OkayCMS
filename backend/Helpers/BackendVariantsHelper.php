<?php


namespace Okay\Admin\Helpers;


use Okay\Core\EntityFactory;
use Okay\Entities\ProductsEntity;
use Okay\Entities\VariantsEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendVariantsHelper
{
    private $variantsEntity;
    private $productsEntity;

    public function __construct(EntityFactory $entityFactory)
    {
        $this->variantsEntity = $entityFactory->get(VariantsEntity::class);
        $this->productsEntity = $entityFactory->get(ProductsEntity::class);
    }

    public function prepareUpdateVariants($productVariants)
    {
        foreach ($productVariants as $index => $variant) {
            if (property_exists($variant, 'stock') && ($variant->stock == '∞' || $variant->stock == '')) {
                $variant->stock = null;
            }
            if (!empty($variant->price)) {
                $variant->price = $variant->price > 0 ? str_replace(',', '.', $variant->price) : 0;
            }
            if (!empty($variant->compare_price)) {
                $variant->compare_price = $variant->compare_price > 0 ? str_replace(',', '.', $variant->compare_price) : 0;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $productVariants, func_get_args());
    }

    public function updateVariants($product, $productVariants)
    {
        $variantsIds = [];

        foreach ($productVariants as $index=>&$variant) {
            if (!empty($variant->id)) {
                $this->variantsEntity->update($variant->id, $variant);
            } else {
                $variant->product_id = $product->id;
                $variant->id = $this->variantsEntity->add($variant);
            }

            $variant = $this->variantsEntity->get((int) $variant->id);
            if (!empty($variant->id)) {
                $variantsIds[] = $variant->id;
            }
        }

        // Удалить непереданные варианты
        $current_variants = $this->variantsEntity->find(['product_id'=>$product->id]);
        foreach ($current_variants as $current_variant) {
            if (!in_array($current_variant->id, $variantsIds)) {
                $this->variantsEntity->delete($current_variant->id);
            }
        }

        // Отсортировать варианты
        asort($variantsIds);
        $i = 0;
        foreach ($variantsIds as $variant_id) {
            $this->variantsEntity->update($variantsIds[$i], ['position'=>$variant_id]);
            $i++;
        }

        // Оновимо агреговане інфо товара
        $this->productsEntity->updateVariantsAggregatedInfo([$product->id]);

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function findProductVariants($product)
    {
        $productVariants   = [];
        if (!empty($product->id)) {
            $productVariants = $this->variantsEntity->find(['product_id' => $product->id]);
        }
        
        if (empty($productVariants)) {
            $productVariants = [1];
        }

        return ExtenderFacade::execute(__METHOD__, $productVariants, func_get_args());
    }

    public function updateStocksAndPrices($stocks, $prices)
    {
        foreach ($prices as $id => $price) {
            $stock = $stocks[$id];
            if ($stock == '∞' || $stock == '') {
                $stock = null;
            }
            $this->variantsEntity->update($id, [
                'price' => str_replace(',', '.', $price),
                'stock' => $stock
            ]);
        }

        // Оновимо агреговане інфо товара
        $productsIds = $this->variantsEntity->cols(['product_id'])->find(['id' => array_keys($prices)]);
        if ($productsIds) {
            $this->productsEntity->updateVariantsAggregatedInfo($productsIds);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
}