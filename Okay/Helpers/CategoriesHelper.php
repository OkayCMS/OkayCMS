<?php

namespace Okay\Helpers;

use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Request;
use Okay\Core\Settings;
use Okay\Core\Translit;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesEntity;

class CategoriesHelper
{
    /** @var CatalogHelper */
    protected $catalogHelper;

    /** @var Settings */
    protected $settings;

    /** @var Design */
    protected $design;

    /** @var FilterHelper */
    protected $filterHelper;


    /** @var FeaturesEntity */
    protected $featuresEntity;

    /** @var FeaturesValuesEntity */
    protected $featuresValuesEntity;

    public function __construct(
        CatalogHelper $catalogHelper,
        EntityFactory $entityFactory,
        Settings      $settings,
        Design        $design,
        FilterHelper  $filterHelper
    ) {
        $this->catalogHelper = $catalogHelper;
        $this->settings      = $settings;
        $this->design        = $design;
        $this->filterHelper  = $filterHelper;

        $this->featuresEntity       = $entityFactory->get(FeaturesEntity::class);
        $this->featuresValuesEntity = $entityFactory->get(FeaturesValuesEntity::class);
    }

    public function assignFilterProcedure(
        array  $productsFilter,
        array  $catalogFeatures,
        object $category
    ): void {
        if (!empty($category->subcategories) && $category->count_children_visible) {
            $catalogCategories = $category->subcategories;
        } else if (!empty($category->path[$category->level_depth - 2]->subcategories) && $category->path[$category->level_depth - 2]->count_children_visible) {
            $catalogCategories = $category->path[$category->level_depth - 2]->subcategories;
        } else {
            $catalogCategories = [];
        }

        $this->catalogHelper->assignCatalogDataProcedure(
            $productsFilter,
            $catalogFeatures,
            $catalogCategories
        );

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getCatalogFeatures(object $category): array
    {
        $filter = $this->catalogHelper->getCatalogFeaturesFilter();

        $filter['category_id'] = $category->id;

        $features = $this->catalogHelper->getCatalogFeatures($filter);

        return ExtenderFacade::execute(__METHOD__, $features, func_get_args());
    }

    public function isFilterPage(array $filter): bool
    {
        return ExtenderFacade::execute(__METHOD__, $this->filterHelper->isFilterPage($filter), func_get_args());
    }

    public function getProductsFilter(object $category, string $filtersUrl = null, array $filter = []): ?array
    {
        if (($filter = $this->catalogHelper->getProductsFilter($filtersUrl, $filter)) === null) {
            return ExtenderFacade::execute(__METHOD__, null, func_get_args());
        }

        $filter['category_id'] = $category->children;

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    /**
     * Метод проверяет доступность категории для показа в контроллере
     * если категория корректна, можно переопределить логику работы контроллера и отменить дальнейшие действия
     * для этого после реализации другой логики необходимо вернуть true из экстендера
     *
     * @param object $category
     * @return object
     */
    public function setCatalogCategory(object $category)
    {
        if (empty($category) || (!$category->visible && empty($_SESSION['admin']))) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * Метод генерирует оглавление
     *
     * @param $text
     * @param null $postUrl
     * @return mixed|void|null
     */
    public function getTableOfContent($text, $objectUrl = null)
    {

        if ($objectUrl === null) {
            $objectUrl = Request::getRequestUri();
        }

        $tableOfContent = [];
        $items = [];
        preg_match_all("~<([hH]([1-6]))(.*?)>(.*?)</[hH]([1-6])>~", $text, $items);

        if (!empty($items[4])) {
            $parts = [];
            foreach ($items[4] as $key=>$string) {

                $sourceHeader = $items[0][$key];

                $id = Translit::translit(strip_tags($string));
                $id = preg_replace('~^[^a-zA-Z]*(.+?)[^a-zA-Z0-9]*$~', '$1', $id);
                $anchorUrl = $objectUrl . '#' . $id;

                // формируем массив где ключ оригинальный заголовок (H) значение заголовок со вставленным в него якорем
                $parts[$sourceHeader] = str_replace($string, '<a id="'.$id.'" href="'.$anchorUrl.'" class="fn_auto_navigation_anchor"></a>'.$string, $sourceHeader);

                // Если у заголовка есть свой клас, добавим наш клас к существующим
                if (preg_match("~.*?class=['\"](.*?)?['\"].*~", $items[3][$key], $headerAttr)) {
                    $parts[$sourceHeader] = str_replace($headerAttr[1], "{$headerAttr[1]} fn_auto_navigation_header", $parts[$sourceHeader]);
                    // Иначе добавляем только наш клас
                } else {
                    $parts[$sourceHeader] = str_replace("<{$items[1][$key]}", "<{$items[1][$key]} class=\"fn_auto_navigation_header\"", $parts[$sourceHeader]);
                }

                $tableOfContentItem['anchor_text']  = strip_tags($string);
                $tableOfContentItem['anchor_id']    = $id;
                $tableOfContentItem['url']          = $anchorUrl;
                $tableOfContentItem['header_level'] = $items[2][$key]; // Уровень заголовка, который поймали

                $tableOfContent[] = $tableOfContentItem;
            }

            $text = strtr($text, $parts);
        }

        return ExtenderFacade::execute(__METHOD__, [$text, $tableOfContent], func_get_args());
    }
}