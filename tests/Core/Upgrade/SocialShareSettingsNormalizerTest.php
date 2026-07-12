<?php

declare(strict_types=1);

namespace Core\Upgrade;

use Aura\Sql\ExtendedPdo;
use Okay\Core\Upgrade\SocialShareSettingsNormalizer;
use PHPUnit\Framework\TestCase;

final class SocialShareSettingsNormalizerTest extends TestCase
{
    public function testLegacyThemeValuesBecomeDefault(): void
    {
        $normalizer = new SocialShareSettingsNormalizer();

        self::assertSame('default', $normalizer->normalizeThemeValue('flat'));
        self::assertSame('default', $normalizer->normalizeThemeValue('classic'));
        self::assertSame('default', $normalizer->normalizeThemeValue(''));
        self::assertSame('custom', $normalizer->normalizeThemeValue('custom'));
    }

    public function testLegacyDefaultShareIdsBecomeCurrentDefault(): void
    {
        $normalizer = new SocialShareSettingsNormalizer();

        $normalized = $normalizer->normalizeSerializedShareIds(
            'a:4:{i:0;s:7:"twitter";i:1;s:8:"facebook";i:2;s:10:"googleplus";i:3;s:8:"linkedin";}'
        );

        self::assertSame(['copy_url', 'facebook', 'x-twitter', 'linkedin'], $this->decode($normalized));
    }

    public function testCustomShareIdsAreMappedAndUnsupportedIdsAreDropped(): void
    {
        $normalizer = new SocialShareSettingsNormalizer();

        $normalized = $normalizer->normalizeSerializedShareIds(
            serialize(['telegram', 'twitter', 'googleplus', 'facebook', 'telegram'])
        );

        self::assertSame(['telegram', 'x-twitter', 'facebook'], $this->decode($normalized));
    }

    public function testEmptyShareSelectionKeepsShowAllContract(): void
    {
        $normalizer = new SocialShareSettingsNormalizer();

        $normalized = $normalizer->normalizeSerializedShareIds(serialize([]));

        self::assertSame([], $this->decode($normalized));
    }

    public function testInvalidShareSelectionFallsBackToCurrentDefault(): void
    {
        $normalizer = new SocialShareSettingsNormalizer();

        $normalized = $normalizer->normalizeSerializedShareIds('not-serialized');

        self::assertSame(['copy_url', 'facebook', 'x-twitter', 'linkedin'], $this->decode($normalized));
    }

    public function testNormalizerUpdatesDatabaseRows(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            self::markTestSkipped('pdo_sqlite is required for this test.');
        }

        $pdo = new ExtendedPdo('sqlite::memory:');
        $pdo->perform('CREATE TABLE `ok_settings` (`param` VARCHAR(255) PRIMARY KEY, `value` TEXT)');
        $pdo->perform(
            'INSERT INTO `ok_settings` (`param`, `value`) VALUES (:param, :value)',
            [
                'param' => 'social_share_theme',
                'value' => 'flat',
            ]
        );
        $pdo->perform(
            'INSERT INTO `ok_settings` (`param`, `value`) VALUES (:param, :value)',
            [
                'param' => 'sj_shares',
                'value' => serialize(['twitter', 'facebook', 'googleplus', 'linkedin']),
            ]
        );

        $changed = (new SocialShareSettingsNormalizer())->normalize($pdo);

        self::assertSame(2, $changed);
        self::assertSame(
            'default',
            $pdo->fetchValue('SELECT `value` FROM `ok_settings` WHERE `param` = :param', ['param' => 'social_share_theme'])
        );
        self::assertSame(
            ['copy_url', 'facebook', 'x-twitter', 'linkedin'],
            $this->decode(
                (string) $pdo->fetchValue(
                    'SELECT `value` FROM `ok_settings` WHERE `param` = :param',
                    ['param' => 'sj_shares']
                )
            )
        );
    }

    /**
     * @return list<string>
     */
    private function decode(string $value): array
    {
        $decoded = unserialize($value, ['allowed_classes' => false]);

        self::assertIsArray($decoded);

        $shareIds = [];
        foreach ($decoded as $shareId) {
            self::assertIsString($shareId);
            $shareIds[] = $shareId;
        }

        return $shareIds;
    }
}
