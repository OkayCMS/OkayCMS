<?php

declare(strict_types=1);

namespace Security;

use Okay\Core\Request;
use PHPUnit\Framework\TestCase;

final class AdminCsrfGuardTest extends TestCase
{
    private array $server;
    private array $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->server = $_SERVER;
        $this->post = $_POST;
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        session_id('csrfguardtest');
        session_start();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_X_OKAY_SESSION_ID'] = session_id();
        $_POST = [];
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->server;
        $_POST = $this->post;
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        parent::tearDown();
    }

    public function testUnsafeRequestAcceptsExplicitHeaderToken(): void
    {
        $request = new Request();

        self::assertTrue($request->checkSession());
    }

    public function testUnsafeRequestRejectsMissingTokenBeforeMutation(): void
    {
        unset($_SERVER['HTTP_X_OKAY_SESSION_ID']);
        $_POST = ['name' => 'changed'];

        $request = new Request();

        self::assertFalse($request->checkSession());
        self::assertSame([], $_POST);
    }
}
