<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class HeaderNavigationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function test_header_uses_active_nav_when_active_page_is_not_set(): void
    {
        $activeNav = 'desktops';
        $pageTitle = 'Test Header';

        ob_start();
        include __DIR__ . '/../../resources/views/storefront/header.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('DESKTOPS', $output);
        $this->assertMatchesRegularExpression('/DESKTOPS.*text-purple-600|text-purple-600.*DESKTOPS/s', $output);
    }
}
