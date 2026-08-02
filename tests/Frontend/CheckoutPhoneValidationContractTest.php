<?php

declare(strict_types=1);

namespace Frontend;

use PHPUnit\Framework\TestCase;

final class CheckoutPhoneValidationContractTest extends TestCase
{
    public function testCartPhoneValidationMethodIsRegisteredBeforeRuleUsage(): void
    {
        $scripts = file_get_contents(dirname(__DIR__, 2) . '/design/okay_shop/html/scripts.tpl');

        self::assertIsString($scripts);

        $methodPosition = strpos($scripts, "$.validator.addMethod('phone'");
        $rulePosition = strpos($scripts, 'phone: true');

        self::assertNotFalse($methodPosition, 'The cart phone validation rule needs a registered jQuery Validate method.');
        self::assertNotFalse($rulePosition, 'The cart should keep phone validation enabled.');
        self::assertLessThan(
            $rulePosition,
            $methodPosition,
            'The phone method must be registered before .fn_validate_cart initializes its rules.'
        );
    }
}
