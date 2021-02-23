<?php


namespace Okay\Modules\OkayCMS\GoogleMerchant\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Image;
use Okay\Core\Languages;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Money;
use Okay\Core\QueryFactory;
use Okay\Core\Router;
use Okay\Core\Routes\ProductRoute;
use Okay\Core\Settings;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\RouterCacheEntity;
use Okay\Entities\VariantsEntity;
use Okay\Helpers\XmlFeedHelper;
use Okay\Core\QueryFactory\Select;
use Okay\Modules\OkayCMS\GoogleMerchant\Entities\GoogleMerchantRelationsEntity;

class GoogleMerchantHelper
{
    /** @var Settings */
    private $settings;

    /** @var Languages */
    private $languages;

    /** @var QueryFactory */
    private $queryFactory;

    /** @var XmlFeedHelper */
    private $feedHelper;

    /** @var Image */
    private $image;

    /** @var Money */
    private $money;


    private $mainCurrency;
    private $allCurrencies;

    public function __construct(
        Settings      $settings,
        Languages     $languages,
        QueryFactory  $queryFactory,
        XmlFeedHelper $feedHelper,
        EntityFactory $entityFactory,
        Image         $image,
        Money         $money
    ) {
        $this->settings     = $settings;
        $this->languages    = $languages;
        $this->queryFactory = $queryFactory;
        $this->feedHelper   = $feedHelper;
        $this->image        = $image;
        $this->money        = $money;

        /** @var CurrenciesEntity $currenciesEntity */
        $currenciesEntity = $entityFactory->get(CurrenciesEntity::class);

        $this->mainCurrency  = $currenciesEntity->getMainCurrency();
        $this->allCurrencies = $currenciesEntity->mappedBy('id')->find();
    }

    /**
     * Метод возвращает итоговый запрос, который достаёт все товары с изображениями с свойствами.
     * По результатам замеров, лучшие результаты производительности достигаются при джоине ленговых таблиц
     * после фильтрации.
     * Фильтрация результатов и группировка свойств с изображениями вынесена в подзапрос,
     * который формируется методом getSubSelect()
     *
     * @param string|integer $feedId
     * @param array $uploadCategories
     * @return Select
     */
    public function getQuery($feedId, $uploadCategories = []) : Select
    {
        $subSelect = $this->getSubSelect($feedId, $uploadCategories);
        if ($this->settings->get('okaycms__google_merchant__use_full_description_to_google')) {
            $descriptionField = 'lp.description';
        } else {
            $descriptionField = 'lp.annotation';
        }

        $sql = $this->queryFactory->newSelect();
        $sql->cols([
            't.*',
            'lp.name as product_name',
            'lv.name as variant_name',
            'lb.name as vendor',
            $descriptionField . ' AS description',
        ])  ->fromSubSelect($subSelect, 't')
            ->leftJoin(ProductsEntity::getLangTable().' AS lp', 'lp.product_id = t.product_id and lp.lang_id=' . $this->languages->getLangId())
            ->leftJoin(VariantsEntity::getLangTable().' AS lv', 'lv.variant_id = t.variant_id and lv.lang_id=' . $this->languages->getLangId())
            ->leftJoin(BrandsEntity::getLangTable().' AS lb', 'lb.brand_id = t.brand_id and lb.lang_id=' . $this->languages->getLangId());

        return ExtenderFacade::execute(__METHOD__, $sql, func_get_args());
    }

    /**
     * Метод возвращает подзапрос, который фильтрует и сортирует результаты, здесь достаются только не мультиязычные
     * данные, кроме свойств. Свойства нужно доставать здесь, т.к. их группируем через GROUP_CONCAT()
     *
     * @param string|integer $feedId
     * @param array $uploadCategories
     * @return Select
     */
    private function getSubSelect($feedId, $uploadCategories = []) : Select
    {
        $sql = $this->queryFactory->newSelect();

        $categoryFilter = '';
        if (!empty($uploadCategories)) {
            $categoryFilter = "OR p.id IN (SELECT product_id FROM __products_categories WHERE category_id IN (:category_id))";
            $sql->bindValue('category_id', (array)$uploadCategories);
        }

        $sql->cols([
            'v.stock',
            'v.price',
            'v.compare_price',
            'v.sku',
            'v.weight',
            'v.currency_id',
            'v.id AS variant_id',
            'p.id AS product_id',
            'p.url',
            'r.slug_url',
            'p.main_category_id',
            'p.brand_id',
        ])  ->from(VariantsEntity::getTable() . ' AS v')
            ->leftJoin(ProductsEntity::getTable().' AS  p', 'v.product_id=p.id')
            ->leftJoin(RouterCacheEntity::getTable().' AS r', 'r.url = p.url AND r.type="product"')
            ->where('p.visible')
            ->where("p.id NOT IN (SELECT entity_id FROM " . GoogleMerchantRelationsEntity::getTable() . " WHERE feed_id = :feed_id AND entity_type = 'product' AND include = 0)")
            ->where("(p.id IN (SELECT entity_id FROM " . GoogleMerchantRelationsEntity::getTable() . " WHERE feed_id = :feed_id AND entity_type = 'product' AND include = 1) OR
                           p.brand_id IN (SELECT entity_id FROM " . GoogleMerchantRelationsEntity::getTable() . " WHERE feed_id = :feed_id AND entity_type = 'brand')
                           {$categoryFilter})")
            ->bindValue('feed_id', $feedId)
            ->groupBy(['v.id'])
            ->orderBy(['p.position DESC']);

        if (!($this->settings->get('okaycms__google_merchant__upload_non_exists_products_to_google'))) {
            $sql->where('(v.stock >0 OR v.stock is NULL)');
        }

        if (!$this->settings->get('okaycms__google_merchant__upload_without_images')) {
            $sql->where('p.main_image_id != \'\' AND p.main_image_id IS NOT NULL');
        }
        
        if ($this->settings->get('okaycms__google_merchant__no_export_without_price')) {
            $sql->where('v.price > 0');
        }

        // Чтобы не писать запрос на группировку свойств и изображений, который может стать невалидным, используем
        // feedHelper чтобы он добавил этот запрос
        $sql = $this->feedHelper->joinImages($sql);
        $sql = $this->feedHelper->joinFeatures($sql);

        return ExtenderFacade::execute(__METHOD__, $sql, func_get_args());
    }

    /**
     * Формируем описание офера в виде массива
     *
     * @param object $product строка выборки из базы (запрос формирующийся методом getQuery),
     * но после отработки методов attachFeatures и attachImages.
     * @param bool $addVariantUrl Если true будет добавлен урл на определенный вариант
     * @param array $allCategories Передаем сюда все категории, чтобы сгенерировать product_type
     * @return array
     * @throws \Exception
     */
    public function getItem($product, array $allCategories, $addVariantUrl = false) : array
    {
        if ($this->settings->get('okaycms__google_merchant__use_variant_name_like_size')) {
            $result['title']['data'] = $this->feedHelper->escape($product->product_name);
            if (!empty($product->variant_name)) {
                $result['g:size']['data'] = $this->feedHelper->escape($product->variant_name);
            }
        } else {
            if (!empty($product->variant_name)) {
                $result['title']['data'] = $this->feedHelper->escape($product->product_name . ' ' . $product->variant_name);
            } else {
                $result['title']['data'] = $this->feedHelper->escape($product->product_name);
            }
        }

        ProductRoute::setUrlSlugAlias($product->url, $product->slug_url);
        if ($addVariantUrl) {
            $result['link']['data'] = Router::generateUrl('product', ['url' => $product->url, 'variantId' => $product->variant_id], true);
        } else {
            $result['link']['data'] = Router::generateUrl('product', ['url' => $product->url], true);
        }

        $result['description']['data'] = $this->feedHelper->escape($product->description);
        $result['g:id']['data'] = $this->feedHelper->escape($product->variant_id);

        if (!empty($product->sku)) {
            $result['g:mpn']['data'] = $this->feedHelper->escape($product->sku);
        }

        $result['g:condition']['data'] = 'new';

        $price = round($product->price, 2);
        $comparePrice = round($product->compare_price, 2);
        if (isset($this->allCurrencies[$product->currency_id])) {
            // Переводим в основную валюту сайта
            $variantCurrency = $this->allCurrencies[$product->currency_id];
            if (!empty($product->currency_id) && $variantCurrency->rate_from != $variantCurrency->rate_to) {
                $price = round($product->price * $variantCurrency->rate_to / $variantCurrency->rate_from, 2);
                if (!empty($product->compare_price)) {
                    $comparePrice = round($product->compare_price * $variantCurrency->rate_to / $variantCurrency->rate_from, 2);
                }
            }
        }
        $price = $this->money->convert($price, $this->mainCurrency->id);
        $comparePrice = $this->money->convert($comparePrice, $this->mainCurrency->id);

        if ($product->compare_price > $product->price) {
            $result['g:price']['data'] = $this->feedHelper->escape($comparePrice . ' ' . $this->mainCurrency->sign);
            $result['g:sale_price']['data'] = $this->feedHelper->escape($price . ' ' . $this->mainCurrency->sign);
        } else {
            $result['g:price']['data'] = $this->feedHelper->escape($price . ' ' . $this->mainCurrency->sign);
        }

        $result['g:availability']['data'] = (!in_array($product->stock, [0, '0'], true) ? 'in_stock' : 'out_of_stock');

        if (!empty($product->vendor)) {
            $result['g:brand']['data'] = $this->feedHelper->escape($product->vendor);
        }

        $result['g:adult']['data'] = $this->settings->get('okaycms__google_merchant__adult') ? 'true' : 'false';

        $productColor = $this->settings->get('okaycms__google_merchant__color');

        if (isset($product->features[$productColor])) {
            $result['g:color']['data'] = $this->feedHelper->escape($product->features[$productColor]['values_string']);
            unset($product->features[$productColor]);
        }

        if (!empty($allCategories[$product->main_category_id])) {
            $categoryPath = $allCategories[$product->main_category_id]->path;

            $productType = '';

            foreach($categoryPath as $category) {
                $productType .= $category->name.' > ';
            }

            $result['g:product_type']['data'] = mb_substr($productType, 0, -3);
        }

        if (!empty($product->images)) {
            $iNum = 0;
            foreach ($product->images as $imageFilename) {
                if ($iNum == 0) {
                    $i['tag'] = 'g:image_link';
                } else {
                    $i['tag'] = 'g:additional_image_link';
                }
                $i['data'] = $this->image->getResizeModifier($imageFilename, 800, 600);
                $result[] = $i;
                if ($iNum++ == 10) {
                    break;
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }
}