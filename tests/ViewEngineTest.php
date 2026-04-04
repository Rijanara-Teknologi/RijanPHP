<?php

namespace Teguh02\Rijanphp\Tests;

use PHPUnit\Framework\TestCase;
use Teguh02\Rijanphp\Core\View\ViewEngine;
use Teguh02\Rijanphp\Core\View\View;

/**
 * Tests for Bug #4: ViewEngine corrupted duplicate code + missing dot-notation namespace support.
 *
 * - No duplicate findView() method (syntax error would prevent class loading)
 * - Dot-notation view name "ns.view" resolves to registered namespace "ns" with view "view"
 * - Explicit namespace syntax "ns::view" still works
 * - View::extends() typo ("View:: extends") is fixed
 */
class ViewEngineTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        // Create a temporary views directory for testing
        $this->tmpDir = sys_get_temp_dir() . '/rijan_view_tests_' . uniqid();
        mkdir($this->tmpDir . '/auth', 0777, true);

        // Create test view files
        file_put_contents($this->tmpDir . '/auth/login.php', '<form>login form</form>');
        file_put_contents($this->tmpDir . '/home.php', '<h1>Home</h1>');

        // Clear View state before each test
        View::clear();
    }

    protected function tearDown(): void
    {
        // Clean up temp files
        array_map('unlink', glob($this->tmpDir . '/auth/*.php'));
        array_map('unlink', glob($this->tmpDir . '/*.php'));
        @rmdir($this->tmpDir . '/auth');
        @rmdir($this->tmpDir);
    }

    // -------------------------------------------------------------------------
    // Class loads without syntax errors (verifies no duplicate methods)
    // -------------------------------------------------------------------------

    public function testViewEngineClassLoadsWithoutError()
    {
        $engine = new ViewEngine();
        $this->assertInstanceOf(ViewEngine::class, $engine);
    }

    // -------------------------------------------------------------------------
    // Explicit namespace syntax "ns::view"
    // -------------------------------------------------------------------------

    public function testExplicitNamespaceSyntax()
    {
        $engine = new ViewEngine();
        $engine->addNamespace('auth', $this->tmpDir . '/auth');

        $output = $engine->make('auth::login');
        $this->assertStringContainsString('login form', $output);
    }

    // -------------------------------------------------------------------------
    // Dot-notation namespace resolution "ns.view" (Bug #4)
    // -------------------------------------------------------------------------

    public function testDotNotationResolvesToRegisteredNamespace()
    {
        $engine = new ViewEngine();
        $engine->addNamespace('auth', $this->tmpDir . '/auth');

        $output = $engine->make('auth.login');
        $this->assertStringContainsString('login form', $output);
    }

    public function testDotNotationFallsBackToGlobalPath()
    {
        // Add the tmp root dir as a global path
        $engine = new ViewEngine();
        $engine->addPath($this->tmpDir);

        // "home.php" sits at the root — without a namespace match the dot-notation
        // falls back to global path search (dot→DIRECTORY_SEPARATOR conversion)
        // Create sub-path structure: tmpDir/sub/page.php
        mkdir($this->tmpDir . '/sub', 0777, true);
        file_put_contents($this->tmpDir . '/sub/page.php', '<p>subpage</p>');

        $output = $engine->make('sub.page');
        $this->assertStringContainsString('subpage', $output);

        unlink($this->tmpDir . '/sub/page.php');
        rmdir($this->tmpDir . '/sub');
    }

    // -------------------------------------------------------------------------
    // View not found exception
    // -------------------------------------------------------------------------

    public function testThrowsExceptionForMissingView()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/not found/i');

        $engine = new ViewEngine();
        $engine->make('nonexistent.view');
    }

    // -------------------------------------------------------------------------
    // View::extends() method (no space typo — Bug #4)
    // -------------------------------------------------------------------------

    public function testViewExtendsMethodExists()
    {
        $this->assertTrue(
            method_exists(View::class, 'extends'),
            'View::extends() method must exist'
        );
    }

    public function testViewEngineExtendsCallsViewExtends()
    {
        View::clear();
        $engine = new ViewEngine();
        $engine->addPath($this->tmpDir);

        // Call extends on the engine — should not throw a parse/call error
        $engine->extends('home');

        // If extends() worked, View::$layout would have been set
        // We verify indirectly by calling clear (no exception = success)
        View::clear();
        $this->assertTrue(true);
    }

    public function testViewEngineExtendsSourceHasNoSpaceTypo()
    {
        $source = file_get_contents(__DIR__ . '/../Core/View/ViewEngine.php');

        // There must be no "View:: extends" (with a space) anywhere in the file
        $this->assertStringNotContainsString(
            'View:: extends',
            $source,
            'ViewEngine.php must not contain "View:: extends" (space is a typo; use "View::extends")'
        );
    }

    public function testViewHelperExtendsSourceHasNoSpaceTypo()
    {
        $source = file_get_contents(__DIR__ . '/../Core/Helpers/View.php');

        $this->assertStringNotContainsString(
            'View:: extends',
            $source,
            'Core/Helpers/View.php must not contain "View:: extends" space typo'
        );
    }

    // -------------------------------------------------------------------------
    // Data passing to views
    // -------------------------------------------------------------------------

    public function testDataIsExtractedIntoView()
    {
        file_put_contents($this->tmpDir . '/greet.php', '<?php echo $name; ?>');

        $engine = new ViewEngine();
        $engine->addPath($this->tmpDir);

        $output = $engine->make('greet', ['name' => 'Rijan']);
        $this->assertSame('Rijan', $output);

        unlink($this->tmpDir . '/greet.php');
    }
}
