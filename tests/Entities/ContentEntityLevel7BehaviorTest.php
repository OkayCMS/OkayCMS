<?php

namespace Entities;

use Okay\Core\EntityFactory;
use Okay\Core\ServiceLocator;
use Okay\Entities\AuthorsEntity;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\PagesEntity;
use Okay\Entities\ProductsEntity;
use PHPUnit\Framework\TestCase;

final class ContentEntityLevel7BehaviorTest extends TestCase
{
    private EntityFactory $entityFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityFactory = ServiceLocator::getInstance()->getService(EntityFactory::class);
    }

    public function testDuplicateReturnsFalseForMissingContentRows(): void
    {
        $missingId = -2147483648;

        /** @var AuthorsEntity $authorsEntity */
        $authorsEntity = $this->entityFactory->get(AuthorsEntity::class);
        /** @var BrandsEntity $brandsEntity */
        $brandsEntity = $this->entityFactory->get(BrandsEntity::class);
        /** @var PagesEntity $pagesEntity */
        $pagesEntity = $this->entityFactory->get(PagesEntity::class);
        /** @var ProductsEntity $productsEntity */
        $productsEntity = $this->entityFactory->get(ProductsEntity::class);

        self::assertFalse($authorsEntity->duplicate($missingId));
        self::assertFalse($brandsEntity->duplicate($missingId));
        self::assertFalse($pagesEntity->duplicate($missingId));
        self::assertFalse($productsEntity->duplicate($missingId));
    }

    public function testMarketCategoriesSkipEmptyCsvRows(): void
    {
        $dir = 'files/downloads';
        $file = 'files/downloads/market_categories.csv';
        $dirExisted = is_dir($dir);
        $oldContents = is_file($file) ? file_get_contents($file) : null;

        if (!$dirExisted) {
            self::assertTrue(mkdir($dir, 0777, true));
        }

        self::assertIsInt(file_put_contents($file, "Header\n\nАвтотовары^1\n"));

        try {
            /** @var CategoriesEntity $categoriesEntity */
            $categoriesEntity = $this->entityFactory->get(CategoriesEntity::class);

            self::assertSame(['Автотовары'], $categoriesEntity->getMarket('авто'));
        } finally {
            if ($oldContents === null) {
                @unlink($file);
            } else {
                file_put_contents($file, $oldContents);
            }

            if (!$dirExisted) {
                @rmdir($dir);
            }
        }
    }
}
