<?php

declare(strict_types=1);

namespace Modules\OkayCMS\NovaposhtaCost;

use Okay\Core\Adapters\Response\AdapterManager;
use Okay\Core\Request;
use Okay\Core\Response;
use Okay\Modules\OkayCMS\NovaposhtaCost\Controllers\NovaposhtaCostSearchController;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPCitiesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPDeliveryTypesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPWarehousesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPApiHelper;
use PHPUnit\Framework\TestCase;

final class NovaposhtaCostFrontendContractTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 4);

        if (!defined('RESPONSE_JSON')) {
            require_once $this->root . '/Okay/Core/config/constants.php';
        }
    }

    protected function tearDown(): void
    {
        $_GET = [];
    }

    public function testTemplateUsesSeparateCityFieldsAndRefsForWarehouseAndDoorDelivery(): void
    {
        $template = $this->read('Okay/Modules/OkayCMS/NovaposhtaCost/design/html/front_cart_delivery_block.tpl');

        self::assertStringContainsString('data-delivery-id="{$delivery->id}"', $template);
        self::assertStringContainsString('id="search_np_street_{$delivery->id}"', $template);

        foreach (
            [
            'warehouse_city',
            'warehouse_city_ref',
            'warehouse_city_name',
            'delivery_warehouse_id',
            'door_city',
            'door_settlement_ref',
            'city_name',
            'area_name',
            'region_name',
            'street',
            'street_name',
            'house',
            'apartment',
            'delivery_price',
            'delivery_term',
            'redelivery',
            ] as $field
        ) {
            self::assertStringContainsString('data-np-field="' . $field . '"', $template);
            self::assertStringContainsString('name="novaposhta[{$delivery->id}][' . $field . ']"', $template);
        }

        foreach (
            [
            'novaposhta_door_city',
            'novaposhta_warehouse_city',
            'novaposhta_door_settlement_ref',
            'novaposhta_city_name',
            'novaposhta_area_name',
            'novaposhta_region_name',
            'novaposhta_street',
            'novaposhta_street_name',
            'novaposhta_house',
            'novaposhta_apartment',
            'novaposhta_warehouse_city_ref',
            'novaposhta_warehouse_city_name',
            'novaposhta_delivery_warehouse_id',
            'novaposhta_delivery_price',
            'novaposhta_delivery_term',
            'novaposhta_redelivery',
            'is_novaposhta_delivery',
            ] as $flatName
        ) {
            self::assertStringNotContainsString('name="' . $flatName . '"', $template);
        }

        self::assertStringContainsString('{$request_data.novaposhta_delivery_warehouse_id|escape}', $template);
        self::assertStringContainsString('{$request_data.novaposhta_city_name|escape}', $template);
        self::assertStringContainsString('{$request_data.novaposhta_delivery_price|escape}', $template);
        self::assertStringNotContainsString('name="is_novaposhta_delivery"', $template);
        self::assertStringNotContainsString('name="novaposhta_delivery_city_id"', $template);
        self::assertStringNotContainsString('id="search_np_street"', $template);
    }

    public function testJavascriptClearsCityRefsWhenVisibleCityTextChangesOutsideAutocomplete(): void
    {
        $source = $this->read('Okay/Modules/OkayCMS/NovaposhtaCost/design/js/np.js');

        self::assertStringContainsString('function clearWarehouseCitySelection', $source);
        self::assertStringContainsString('function clearDoorCitySelection', $source);
        self::assertStringContainsString('[data-np-field="warehouse_city"]', $source);
        self::assertStringContainsString('[data-np-field="door_city"]', $source);
        self::assertStringContainsString('"warehouse_city_ref"', $source);
        self::assertStringContainsString('"door_settlement_ref"', $source);
        self::assertStringContainsString('"input change paste blur"', $source);
        self::assertStringNotContainsString('novaposhta_delivery_city_id', $source);
    }

    public function testDoorDeliveryAutocompleteIsScopedToSelectedInputBlock(): void
    {
        $source = $this->read('Okay/Modules/OkayCMS/NovaposhtaCost/design/js/np.js');
        $doorCityAutocomplete = $this->sliceBetween(
            $source,
            ".fn_delivery_novaposhta input.city_novaposhta_for_door",
            '});',
        );

        self::assertStringContainsString('let delivery_block = $(this).closest(".delivery__item");', $doorCityAutocomplete);
        self::assertStringContainsString('setStreetAutocomplete(delivery_block, suggestion.ref);', $doorCityAutocomplete);
        self::assertStringNotContainsString('input[name="delivery_id"]:checked', $doorCityAutocomplete);
    }

    public function testStreetAutocompleteUsesStableNovaPoshtaSearchSettings(): void
    {
        $source = $this->read('Okay/Modules/OkayCMS/NovaposhtaCost/design/js/np.js');

        self::assertStringContainsString('function setStreetAutocomplete', $source);
        self::assertStringContainsString('minChars: 1', $source);
        self::assertStringContainsString('noCache: true', $source);
        self::assertStringContainsString('preventBadQueries: false', $source);
    }

    public function testCheckoutPersistenceUsesModeSpecificCityRefs(): void
    {
        $source = $this->read('Okay/Modules/OkayCMS/NovaposhtaCost/Extenders/FrontExtender.php');

        self::assertStringContainsString("\$selectedData['door_settlement_ref']", $source);
        self::assertStringContainsString("\$selectedData['warehouse_city_ref']", $source);
        self::assertStringContainsString("\$selectedData['door_city']", $source);
        self::assertStringContainsString("\$selectedData['warehouse_city']", $source);
        self::assertStringNotContainsString("\$this->request->post('novaposhta_delivery_city_id')", $source);
    }

    public function testJavascriptUsesDataNpFieldsInsteadOfFlatNamesForCheckoutState(): void
    {
        $source = $this->read('Okay/Modules/OkayCMS/NovaposhtaCost/design/js/np.js');
        $validation = $this->read('Okay/Modules/OkayCMS/NovaposhtaCost/design/html/validation.js');

        self::assertStringContainsString('data-np-field', $source);
        self::assertStringContainsString('novaposhta[', $source);
        self::assertStringContainsString('delivery_price', $source);
        self::assertStringContainsString('redelivery', $source);
        self::assertStringContainsString('isActiveNovaPoshtaField', $validation);
        self::assertStringContainsString('data-np-field="warehouses"', $validation);

        foreach (
            [
            'novaposhta_warehouse_city_ref',
            'novaposhta_delivery_warehouse_id',
            'novaposhta_door_settlement_ref',
            'novaposhta_city_name',
            'novaposhta_street',
            'novaposhta_house',
            'novaposhta_apartment',
            'novaposhta_delivery_price',
            'novaposhta_delivery_term',
            'novaposhta_redelivery',
            ] as $flatName
        ) {
            self::assertStringNotContainsString('name="' . $flatName . '"', $source);
            self::assertStringNotContainsString('name=\"' . $flatName . '\"', $source);
        }
    }

    public function testCheckoutValidationAndPersistenceDoNotTrustHiddenNovaPoshtaFlag(): void
    {
        $source = $this->read('Okay/Modules/OkayCMS/NovaposhtaCost/Extenders/FrontExtender.php');

        self::assertStringContainsString('selectedNovaPoshtaDelivery()', $source);
        self::assertStringContainsString('selectedDataForDelivery', $source);
        self::assertStringNotContainsString("\$this->request->post('is_novaposhta_delivery', 'boolean')", $source);
        self::assertStringNotContainsString("\$this->request->post('is_novaposhta_delivery'", $source);
        self::assertStringNotContainsString("\$result['delivery_price'] = \$selectedData['delivery_price']", $source);
        self::assertStringContainsString('calculateSelectedDeliveryPrice', $source);
    }

    public function testDoorCityAutocompleteReturnsEmptySuggestionsOnNovaPoshtaError(): void
    {
        $_GET = ['query' => 'ab'];

        $apiHelper = $this->createMock(NPApiHelper::class);
        $apiHelper->expects(self::once())
            ->method('request')
            ->willReturn((object)['success' => false]);

        $response = $this->createResponse();
        (new NovaposhtaCostSearchController())->findCityForDoor(new Request(), $response, $apiHelper);

        /** @var object{query: string, suggestions: list<object>} $payload */
        $payload = $this->decodeLastJson($response);

        self::assertSame('ab', $payload->query);
        self::assertSame([], $payload->suggestions);
    }

    public function testStreetAutocompleteReturnsEmptySuggestionsOnNovaPoshtaError(): void
    {
        $_GET = [
            'query' => 'ab',
            'city_ref' => 'city-ref',
        ];

        $apiHelper = $this->createMock(NPApiHelper::class);
        $apiHelper->expects(self::once())
            ->method('request')
            ->willReturn(false);

        $response = $this->createResponse();
        (new NovaposhtaCostSearchController())->findStreet(new Request(), $response, $apiHelper);

        /** @var object{query: string, suggestions: list<object>} $payload */
        $payload = $this->decodeLastJson($response);

        self::assertSame('ab', $payload->query);
        self::assertSame([], $payload->suggestions);
    }

    public function testGetWarehousesRejectsUnknownWarehouseCityRef(): void
    {
        $_GET = ['city' => 'door-settlement-ref'];

        $citiesEntity = $this->createMock(NPCitiesEntity::class);
        $citiesEntity->expects(self::once())
            ->method('findOne')
            ->with(['ref' => 'door-settlement-ref'])
            ->willReturn(false);

        $deliveryTypesEntity = $this->createMock(NPDeliveryTypesEntity::class);
        $deliveryTypesEntity->expects(self::never())->method('find');

        $warehousesEntity = $this->createMock(NPWarehousesEntity::class);
        $warehousesEntity->expects(self::never())->method('find');

        $response = $this->callGetWarehouses($warehousesEntity, $deliveryTypesEntity, $citiesEntity);
        /** @var object{success: bool, reason: string} $payload */
        $payload = $this->decodeLastJson($response);

        self::assertFalse($payload->success);
        self::assertSame('invalid_city_ref', $payload->reason);
    }

    public function testGetWarehousesReturnsWarehousesForKnownWarehouseCityRef(): void
    {
        $_GET = ['city' => 'warehouse-city-ref'];

        $citiesEntity = $this->createMock(NPCitiesEntity::class);
        $citiesEntity->expects(self::once())
            ->method('findOne')
            ->with(['ref' => 'warehouse-city-ref'])
            ->willReturn((object)['ref' => 'warehouse-city-ref']);

        $deliveryTypesEntity = $this->createMock(NPDeliveryTypesEntity::class);
        $deliveryTypesEntity->expects(self::once())
            ->method('find')
            ->willReturn([
                (object)[
                    'name' => 'Warehouse',
                    'warehouses_type_refs' => ['warehouse-type-ref'],
                ],
            ]);

        $warehousesEntity = $this->createMock(NPWarehousesEntity::class);
        $warehousesEntity->expects(self::once())
            ->method('find')
            ->with([
                'city_ref' => 'warehouse-city-ref',
                'type' => ['warehouse-type-ref' => 'warehouse-type-ref'],
            ])
            ->willReturn([
                (object)[
                    'name' => 'Warehouse #1',
                    'ref' => 'warehouse-ref',
                    'type' => 'warehouse-type-ref',
                ],
            ]);

        $response = $this->callGetWarehouses($warehousesEntity, $deliveryTypesEntity, $citiesEntity);
        /** @var object{success: bool, delivery_types: list<object{name: string}>, warehouses: list<object{name: string}>} $payload */
        $payload = $this->decodeLastJson($response);

        self::assertTrue($payload->success);
        self::assertSame('Warehouse', $payload->delivery_types[0]->name);
        self::assertSame('Warehouse #1', $payload->warehouses[0]->name);
    }

    private function callGetWarehouses(
        NPWarehousesEntity $warehousesEntity,
        NPDeliveryTypesEntity $deliveryTypesEntity,
        NPCitiesEntity $citiesEntity
    ): Response {
        return (new NovaposhtaCostSearchController())->getWarehouses(
            new Request(),
            $this->createResponse(),
            $warehousesEntity,
            $deliveryTypesEntity,
            $citiesEntity
        );
    }

    private function createResponse(): Response
    {
        return new Response(new AdapterManager(RESPONSE_JSON));
    }

    private function decodeLastJson(Response $response): object
    {
        $content = $response->getContent();
        $json = end($content);

        self::assertIsString($json);

        $decoded = json_decode($json);

        self::assertIsObject($decoded);

        return $decoded;
    }

    private function read(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);

        self::assertIsString($source);

        return $source;
    }

    private function sliceBetween(string $source, string $start, string $end): string
    {
        $startPosition = strpos($source, $start);
        self::assertIsInt($startPosition);

        $endPosition = strpos($source, $end, $startPosition);
        self::assertIsInt($endPosition);

        return substr($source, $startPosition, $endPosition - $startPosition);
    }
}
