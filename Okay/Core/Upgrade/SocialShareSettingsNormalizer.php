<?php

declare(strict_types=1);

namespace Okay\Core\Upgrade;

use Aura\Sql\ExtendedPdo;

final class SocialShareSettingsNormalizer
{
    public const NAME = 'social-share-settings';

    private const DEFAULT_SHARE_IDS = [
        'copy_url',
        'facebook',
        'x-twitter',
        'linkedin',
    ];

    private const LEGACY_SHARE_THEMES = [
        '',
        'flat',
        'classic',
        'minima',
        'plain',
    ];

    private const LEGACY_DEFAULT_SHARE_ID_SETS = [
        ['twitter', 'facebook', 'vkontakte', 'odnoklassniki'],
        ['twitter', 'facebook', 'googleplus', 'linkedin'],
        ['twitter', 'facebook', 'googleplus', 'linkedin', 'odnoklassniki'],
    ];

    private const LEGACY_SHARE_ID_MAP = [
        'twitter' => 'x-twitter',
        'email' => 'envelope',
    ];

    private const SUPPORTED_SHARE_IDS = [
        'copy_url' => true,
        'facebook' => true,
        'telegram' => true,
        'viber' => true,
        'whatsapp' => true,
        'linkedin' => true,
        'x-twitter' => true,
        'threads' => true,
        'pinterest' => true,
        'reddit' => true,
        'envelope' => true,
    ];

    public function normalize(ExtendedPdo $pdo): int
    {
        $changed = 0;

        $changed += $this->normalizeSetting(
            $pdo,
            'social_share_theme',
            $this->normalizeThemeValue($this->fetchSettingValue($pdo, 'social_share_theme'))
        );

        $changed += $this->normalizeSetting(
            $pdo,
            'sj_shares',
            $this->normalizeSerializedShareIds($this->fetchSettingValue($pdo, 'sj_shares'))
        );

        return $changed;
    }

    public function normalizeThemeValue(?string $value): string
    {
        $value = trim((string) $value);

        if (in_array($value, self::LEGACY_SHARE_THEMES, true)) {
            return 'default';
        }

        return $value;
    }

    public function normalizeSerializedShareIds(?string $value): string
    {
        $shareIds = $this->decodeShareIds($value);

        if ($shareIds === null) {
            return serialize(self::DEFAULT_SHARE_IDS);
        }

        if ($shareIds === []) {
            return serialize([]);
        }

        if ($this->isLegacyDefaultShareIdSet($shareIds)) {
            return serialize(self::DEFAULT_SHARE_IDS);
        }

        $normalizedShareIds = [];
        foreach ($shareIds as $shareId) {
            $normalizedShareId = $this->normalizeShareId($shareId);
            if ($normalizedShareId === null || in_array($normalizedShareId, $normalizedShareIds, true)) {
                continue;
            }

            $normalizedShareIds[] = $normalizedShareId;
        }

        if ($normalizedShareIds === []) {
            return serialize(self::DEFAULT_SHARE_IDS);
        }

        return serialize($normalizedShareIds);
    }

    private function normalizeSetting(ExtendedPdo $pdo, string $param, string $value): int
    {
        $currentValue = $this->fetchSettingValue($pdo, $param);
        if ($currentValue === $value) {
            return 0;
        }

        if ($currentValue === null) {
            $pdo->perform(
                'INSERT INTO `ok_settings` (`param`, `value`) VALUES (:param, :value)',
                [
                    'param' => $param,
                    'value' => $value,
                ]
            );

            return 1;
        }

        $pdo->perform(
            'UPDATE `ok_settings` SET `value` = :value WHERE `param` = :param',
            [
                'param' => $param,
                'value' => $value,
            ]
        );

        return 1;
    }

    private function fetchSettingValue(ExtendedPdo $pdo, string $param): ?string
    {
        $row = $pdo->fetchOne(
            'SELECT `value` FROM `ok_settings` WHERE `param` = :param',
            [
                'param' => $param,
            ]
        );

        if ($row === false) {
            return null;
        }

        return is_scalar($row['value']) ? (string) $row['value'] : '';
    }

    /**
     * @return list<string>|null
     */
    private function decodeShareIds(?string $value): ?array
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $success = true;
        set_error_handler(static function (
            int $errno,
            string $errstr,
            string $errfile,
            int $errline
        ) use (&$success): bool {
            unset($errno, $errstr, $errfile, $errline);
            $success = false;

            return true;
        });

        try {
            $decoded = unserialize($value, ['allowed_classes' => false]);
        } finally {
            restore_error_handler();
        }

        if (!$success || !is_array($decoded)) {
            return null;
        }

        $shareIds = [];
        foreach ($decoded as $shareId) {
            if (!is_string($shareId)) {
                continue;
            }

            $shareIds[] = $shareId;
        }

        return $shareIds;
    }

    /**
     * @param list<string> $shareIds
     */
    private function isLegacyDefaultShareIdSet(array $shareIds): bool
    {
        foreach (self::LEGACY_DEFAULT_SHARE_ID_SETS as $legacyDefaultShareIdSet) {
            if ($shareIds === $legacyDefaultShareIdSet) {
                return true;
            }
        }

        return false;
    }

    private function normalizeShareId(string $shareId): ?string
    {
        $shareId = trim($shareId);
        if ($shareId === '') {
            return null;
        }

        $shareId = self::LEGACY_SHARE_ID_MAP[$shareId] ?? $shareId;

        return isset(self::SUPPORTED_SHARE_IDS[$shareId]) ? $shareId : null;
    }
}
