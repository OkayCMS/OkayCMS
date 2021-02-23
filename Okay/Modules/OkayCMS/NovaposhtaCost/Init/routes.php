<?php

namespace Okay\Modules\OkayCMS\NovaposhtaCost;

return [
    'OkayCMS_NovaposhtaCost_find_city' => [
        'slug' => 'ajax/np/find_city',
        'to_front' => true,
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\NovaposhtaCostSearchController',
            'method' => 'findCity',
        ],
    ],
    'OkayCMS_NovaposhtaCost_find_city_for_door' => [
        'slug' => 'ajax/np/find_city_for_door',
        'to_front' => true,
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\NovaposhtaCostSearchController',
            'method' => 'findCityForDoor',
        ],
    ],
    'OkayCMS_NovaposhtaCost_find_street' => [
        'slug' => 'ajax/np/find_street',
        'to_front' => true,
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\NovaposhtaCostSearchController',
            'method' => 'findStreet',
        ],
    ],
    'OkayCMS_NovaposhtaCost_get_cities' => [
        'slug' => 'ajax/np/get_cities',
        'to_front' => true,
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\NovaposhtaCostController',
            'method' => 'getCities',
        ],
    ],
    'OkayCMS_NovaposhtaCost_get_warehouses' => [
        'slug' => 'ajax/np/get_warehouses',
        'to_front' => true,
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\NovaposhtaCostController',
            'method' => 'getWarehouses',
        ],
    ],
    'OkayCMS_NovaposhtaCost_calc' => [
        'slug' => 'ajax/np/calc',
        'to_front' => true,
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\NovaposhtaCostController',
            'method' => 'calc',
        ],
    ],
];