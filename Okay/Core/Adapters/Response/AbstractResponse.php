<?php

namespace Okay\Core\Adapters\Response;

use Okay\Core\DebugBar\DebugBar;

abstract class AbstractResponse
{
    abstract public function send($content);

    abstract public function getSpecialHeaders();

    /**
     * Перевіряє, чи потрібно викликати DebugBar::stackData() для поточного запиту.
     * Виключає AJAX-ендпоінти, які можуть повертати великі обсяги даних.
     *
     * @return bool true якщо потрібно викликати stackData(), false якщо ні
     */
    protected static function shouldCallStackData(): bool
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';

        // Список ендпоінтів, для яких не викликаємо stackData() щоб уникнути вичерпання пам'яті
        $skipDebugBarEndpoints = [
            // Автокомпліт пошуку
            '/ajax/search_products',
            // Nova Poshta AJAX-ендпоінти
            '/ajax/np/find_city',
            '/ajax/np/find_city_for_door',
            '/ajax/np/find_street',
            '/ajax/np/get_warehouses',
            // XML фіди (використовують sendStream, викликають send() багато разів)
            '/feed/',
            '/feeds/',
            // OpenSearch live search
            '/ajax/opensearch',
        ];

        foreach ($skipDebugBarEndpoints as $endpoint) {
            if (strpos($requestUri, $endpoint) !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Безпечний виклик DebugBar::stackData() з перевіркою
     */
    protected static function safeStackData(): void
    {
        if (self::shouldCallStackData()) {
            DebugBar::stackData();
        }
    }
}
