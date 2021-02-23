<?php


namespace Okay\Modules\OkayCMS\Hotline\Extenders;


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Modules\OkayCMS\Hotline\Entities\HotlineFeedsEntity;
use Okay\Modules\OkayCMS\Hotline\Entities\HotlineRelationsEntity;
use Okay\Modules\OkayCMS\Hotline\Init\Init;

class BackendExtender implements ExtensionInterface
{
    /** @var Design */
    private $design;


    /** @var HotlineFeedsEntity */
    private $feedsEntity;

    /** @var HotlineRelationsEntity */
    private $relationsEntity;


    /** @var array */
    private $currentFeeds  = [];

    public function __construct(
        EntityFactory $entityFactory,
        Design        $design
    )
    {
        $this->design = $design;

        $this->feedsEntity     = $entityFactory->get(HotlineFeedsEntity::class);
        $this->relationsEntity = $entityFactory->get(HotlineRelationsEntity::class);

        $this->currentFeeds = $this->feedsEntity->find(['limit' => $this->feedsEntity->count()]);
    }

    public function parseProductData($product)
    {
        $feeds = $this->currentFeeds;

        foreach ($feeds as $feed) {
            $columnName = Init::TO_FEED_FIELD . "@{$feed->id}";
            if (isset($product[$columnName])) {
                unset($product[$columnName]);
            }
        }

        return $product;
    }

    public function importItem($importedItem, $itemFromCsv)
    {
        $feeds = $this->currentFeeds;

        foreach ($feeds as $feed) {
            $columnName = Init::TO_FEED_FIELD . "@{$feed->id}";
            if (isset($itemFromCsv[$columnName])) {
                if (trim($itemFromCsv[$columnName])) {
                    $this->relationsEntity->add([
                        'feed_id'     => $feed->id,
                        'entity_id'   => $importedItem->product->id,
                        'entity_type' => 'product',
                        'include'     => 1
                    ]);
                }
            } else {
                continue;
            }
        }
    }

    public function extendExportColumnsNames($product)
    {
        $feeds = $this->currentFeeds;

        for ($i = 1; $i <= count($feeds); $i++) {
            $product[Init::TO_FEED_FIELD . '_' . $i] = Init::TO_FEED_FIELD . ' ' . $i;
        }

        return $product;
    }

    public function extendFilter($params)
    {
        list($filter, $page) = $params;

        $filter[INIT::FILTER_FEEDS] = true;

        return [$filter, $page];
    }

    public function getModulesColumnsNames($modulesColumnsNames)
    {
        $feeds = $this->currentFeeds;

        foreach ($feeds as $feed) {
            $modulesColumnsNames[] = Init::TO_FEED_FIELD . '@' . $feed->id;
        }

        return $modulesColumnsNames;
    }
}