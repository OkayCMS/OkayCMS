<?php


namespace Okay\Modules\OkayCMS\YandexXMLVendorModel\Controllers;


use Aura\Sql\ExtendedPdo;
use Okay\Controllers\AbstractController;
use Okay\Core\Money;
use Okay\Core\QueryFactory;
use Okay\Core\Router;
use Okay\Core\Routes\ProductRoute;
use Okay\Entities\CategoriesEntity;
use Okay\Helpers\XmlFeedHelper;
use Okay\Modules\OkayCMS\YandexXMLVendorModel\Entities\YandexXMLVendorModelFeedsEntity;
use Okay\Modules\OkayCMS\YandexXMLVendorModel\Entities\YandexXMLVendorModelRelationsEntity;
use Okay\Modules\OkayCMS\YandexXMLVendorModel\Helpers\YandexXMLHelper;
use PDO;

class YandexXMLController extends AbstractController
{
    public function render(
        CategoriesEntity $categoriesEntity,
        QueryFactory $queryFactory,
        ExtendedPdo $pdo,
        YandexXMLHelper $yandexXMLHelper,
        XmlFeedHelper $feedHelper,
        YandexXMLVendorModelFeedsEntity $feedsEntity,
        Money                $money,
        $url
    ) {
        if (!($feed = $feedsEntity->findOne(['url' => $url])) || !$feed->enabled) {
            return false;
        }
        
        if (!empty($this->currencies)) {
            $this->design->assign('main_currency', reset($this->currencies));

            // Передаем валюты, чтобы класс потом не лез в базу за валютами, т.к. мы работаем с небуферизированными запросами
            foreach ($this->currencies as $c) {
                $money->setCurrency($c);
            }
        }

        $sql = $queryFactory->newSqlQuery();
        $sql->setStatement('SET SQL_BIG_SELECTS=1');
        $sql->execute();

        $select = $queryFactory->newSelect();
        $select ->from(YandexXMLVendorModelRelationsEntity::getTable())
                ->cols(['entity_id'])
                ->where("feed_id = :feed_id AND entity_type = 'category'")
                ->bindValue('feed_id', $feed->id);

        $categoriesToFeed = $select->results('entity_id');
        $uploadCategories = $feedHelper->addAllChildrenToList($categoriesToFeed);
        
        $this->design->assign('all_categories', $categoriesEntity->find());

        $this->response->setContentType(RESPONSE_XML);
        $this->response->sendHeaders();
        $this->response->sendStream($this->design->fetch('feed_head.xml.tpl'));
        
        // На всякий случай наполним кеш роутов
        Router::generateRouterCache();

        // Запрещаем выполнять запросы в БД во время генерации урла т.к. мы работаем с небуферизированными запросами
        ProductRoute::setNotUseSqlToGenerate();

        // Увеличиваем лимит ф-ции GROUP_CONCAT()
        $query = $queryFactory->newSqlQuery();
        $query->setStatement('SET SESSION group_concat_max_len = 1000000;')->execute();
        
        // Для экономии памяти работаем с небуферизированными запросами
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $query = $yandexXMLHelper->getQuery($feed->id, $uploadCategories);
        
        $prevProductId = null;
        while ($product = $query->result()) {
            $product = $feedHelper->attachFeatures($product);
            $product = $feedHelper->attachDescriptionByTemplate($product);
            $product = $feedHelper->attachProductImages($product);

            $addVariantUrl = false;
            if ($prevProductId === $product->product_id) {
                $addVariantUrl = true;
            }
            $prevProductId = $product->product_id;
            $item = $yandexXMLHelper->getItem($product, $addVariantUrl);
            $xmlProduct = $feedHelper->compileItem($item, 'offer', [
                'id' => $product->variant_id,
                'group_id' => $product->product_id,
                'type' => "vendor.model",
                'available' => ($product->stock > 0 || $product->stock === null ? 'true' : 'false'),
            ]);
            $this->response->sendStream($xmlProduct);
        }

        $this->response->sendStream($this->design->fetch('feed_footer.xml.tpl'));
    }
}
