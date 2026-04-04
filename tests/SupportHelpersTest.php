<?php

namespace Teguh02\Rijanphp\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests for Bug #2: Missing helper functions asset(), url(), database_path(), public_path().
 *
 * These helpers are plain functions defined in Core/Helpers/Support.php.
 * We bootstrap them by requiring the file directly after stubbing base_path().
 */
class SupportHelpersTest extends TestCase
{
    protected function setUp(): void
    {
        // Provide a minimal base_path() stub so Support.php helpers work without a full app boot.
        if (!function_exists('base_path')) {
            eval('function base_path($path = "") { return "/var/www/" . ltrim($path, "/"); }');
        }

        // Load the helpers file (idempotent due to function_exists guards)
        require_once __DIR__ . '/../Core/Helpers/Support.php';
    }

    // -------------------------------------------------------------------------
    // database_path()
    // -------------------------------------------------------------------------

    public function testDatabasePathWithNoArgument()
    {
        $result = database_path();
        $this->assertStringContainsString('storage/database/', $result);
    }

    public function testDatabasePathWithFilename()
    {
        $result = database_path('app.sqlite');
        $this->assertStringEndsWith('app.sqlite', $result);
        $this->assertStringContainsString('storage/database', $result);
    }

    // -------------------------------------------------------------------------
    // public_path()
    // -------------------------------------------------------------------------

    public function testPublicPathWithNoArgument()
    {
        $result = public_path();
        $this->assertStringContainsString('public/', $result);
    }

    public function testPublicPathWithFile()
    {
        $result = public_path('css/app.css');
        $this->assertStringContainsString('public/', $result);
        $this->assertStringEndsWith('css/app.css', $result);
    }

    // -------------------------------------------------------------------------
    // asset()
    // -------------------------------------------------------------------------

    public function testAssetGeneratesUrl()
    {
        putenv('APP_URL=http://localhost');
        $result = asset('css/app.css');
        $this->assertStringStartsWith('http://localhost', $result);
        $this->assertStringContainsString('/public/', $result);
        $this->assertStringEndsWith('css/app.css', $result);
    }

    public function testAssetStripsLeadingSlash()
    {
        putenv('APP_URL=http://localhost');
        $result = asset('/images/logo.png');
        // Should not produce double slash
        $this->assertStringNotContainsString('//images', $result);
    }

    public function testAssetPassesThroughAbsoluteUrl()
    {
        $absoluteUrl = 'https://cdn.example.com/style.css';
        $result = asset($absoluteUrl);
        $this->assertSame($absoluteUrl, $result);
    }

    public function testAssetEmptyPath()
    {
        putenv('APP_URL=http://localhost');
        $result = asset('');
        $this->assertStringStartsWith('http://localhost', $result);
        $this->assertStringContainsString('/public/', $result);
    }

    // -------------------------------------------------------------------------
    // url()
    // -------------------------------------------------------------------------

    public function testUrlGeneratesCorrectUrl()
    {
        putenv('APP_URL=http://localhost');
        $result = url('dashboard');
        $this->assertSame('http://localhost/dashboard', $result);
    }

    public function testUrlStripsLeadingSlash()
    {
        putenv('APP_URL=http://localhost');
        $result = url('/login');
        $this->assertSame('http://localhost/login', $result);
    }

    public function testUrlPassesThroughAbsoluteUrl()
    {
        $absoluteUrl = 'https://example.com/path';
        $result = url($absoluteUrl);
        $this->assertSame($absoluteUrl, $result);
    }

    public function testUrlEmptyPath()
    {
        putenv('APP_URL=http://localhost');
        $result = url('');
        $this->assertSame('http://localhost/', $result);
    }

    public function testUrlTrailingSlashOnAppUrlIsStripped()
    {
        putenv('APP_URL=http://localhost/');
        $result = url('about');
        $this->assertSame('http://localhost/about', $result);
    }
}
