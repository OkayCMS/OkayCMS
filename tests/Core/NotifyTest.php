<?php

declare(strict_types=1);

namespace Okay\Core {
    function mail(string $to, string $subject, string $message, string $headers): bool
    {
        $GLOBALS['okay_notify_mail_calls'][] = [
            'to' => $to,
            'subject' => $subject,
            'message' => $message,
            'headers' => $headers,
        ];

        return true;
    }
}

namespace Core {
    use Okay\Core\BackendTranslations;
    use Okay\Core\Design;
    use Okay\Core\EntityFactory;
    use Okay\Core\FrontTranslations;
    use Okay\Core\Languages;
    use Okay\Core\Notify;
    use Okay\Core\Settings;
    use Okay\Core\TemplateConfig\FrontTemplateConfig;
    use Okay\Helpers\NotifyHelper;
    use Okay\Helpers\OrdersHelper;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPUnit\Framework\TestCase;
    use Psr\Log\NullLogger;

    final class NotifyTest extends TestCase
    {
        protected function setUp(): void
        {
            parent::setUp();

            $GLOBALS['okay_notify_mail_calls'] = [];
        }

        public function testSmtpSendConfiguresPhpMailerTransport(): void
        {
            $recipients = [];
            $mailer = $this->getMockBuilder(PHPMailer::class)
                ->onlyMethods(['addAddress', 'send'])
                ->getMock();
            $mailer->method('addAddress')
                ->willReturnCallback(static function (string $address, string $name = '') use (&$recipients): bool {
                    $recipients[] = [$address, $name];

                    return true;
                });
            $mailer->expects(self::once())
                ->method('send')
                ->willReturn(true);

            $notify = $this->notify($mailer, [
                'smtp_server' => 'smtp.example.test',
                'smtp_port' => 587,
                'smtp_user' => 'sender@example.test',
                'smtp_pass' => 'secret',
                'notify_from_name' => 'Okay CMS',
                'disable_validate_smtp_certificate' => true,
            ]);

            self::assertTrue($notify->SMTP('first@example.test,second@example.test', 'Subject', '<b>Hello</b>'));

            self::assertSame('smtp', $mailer->Mailer);
            self::assertSame('tls://smtp.example.test', $mailer->Host);
            self::assertSame(587, $mailer->Port);
            self::assertTrue($mailer->SMTPAuth);
            self::assertSame(PHPMailer::ENCRYPTION_STARTTLS, $mailer->SMTPSecure);
            self::assertSame('sender@example.test', $mailer->Username);
            self::assertSame('secret', $mailer->Password);
            self::assertSame('Subject', $mailer->Subject);
            self::assertSame('<b>Hello</b>', $mailer->Body);
            self::assertSame([
                ['first@example.test', ''],
                ['second@example.test', ''],
            ], $recipients);
            self::assertSame([
                'sender@example.test' => ['sender@example.test', 'Okay CMS'],
            ], $this->replyToAddressesByEmail($mailer));
            self::assertSame([], $mailer->getAllRecipientAddresses());
            self::assertSame([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ], $mailer->SMTPOptions);
        }

        public function testEmailUsesNativeMailFallbackWhenSmtpDisabled(): void
        {
            $mailer = $this->getMockBuilder(PHPMailer::class)
                ->onlyMethods(['send'])
                ->getMock();
            $mailer->expects(self::never())
                ->method('send');

            $notify = $this->notify($mailer, [
                'use_smtp' => false,
            ]);

            $notify->email(
                'customer@example.test',
                'Order created',
                '<p>Thanks</p>',
                'shop@example.test',
                'reply@example.test'
            );

            self::assertSame([
                [
                    'to' => 'customer@example.test',
                    'subject' => '=?utf-8?B?' . base64_encode('Order created') . '?=',
                    'message' => '<p>Thanks</p>',
                    'headers' => implode('', [
                        "MIME-Version: 1.0\r\n",
                        "Content-type: text/html; charset=utf-8;\r\n",
                        "From: shop@example.test\r\n",
                        "reply-to: reply@example.test\r\n",
                    ]),
                ],
            ], $GLOBALS['okay_notify_mail_calls']);
        }

        /**
         * @param array<string, mixed> $settings
         */
        private function notify(PHPMailer $mailer, array $settings): Notify
        {
            return new Notify(
                $this->settings($settings),
                $this->createStub(Languages::class),
                $this->createStub(EntityFactory::class),
                $this->createStub(Design::class),
                $this->createStub(FrontTemplateConfig::class),
                $this->createStub(OrdersHelper::class),
                $this->createStub(BackendTranslations::class),
                $this->createStub(FrontTranslations::class),
                $mailer,
                new NullLogger(),
                $this->createStub(NotifyHelper::class),
                dirname(__DIR__, 2)
            );
        }

        /**
         * @param array<string, mixed> $values
         */
        private function settings(array $values): Settings
        {
            return new class ($values) extends Settings {
                /**
                 * @param array<string, mixed> $values
                 */
                public function __construct(private array $values)
                {
                }

                public function get($param)
                {
                    return $this->values[$param] ?? null;
                }
            };
        }

        /**
         * @return array<string, array{0: string, 1: string}>
         */
        private function replyToAddressesByEmail(PHPMailer $mailer): array
        {
            $addresses = [];
            foreach ($mailer->getReplyToAddresses() as $address) {
                $addresses[$address[0]] = $address;
            }

            return $addresses;
        }
    }
}
