<?php

namespace Teguh02\Rijanphp\Tests;

use PHPUnit\Framework\TestCase;
use Teguh02\Rijanphp\Core\Session\Session;

/**
 * Tests for Bug #3: Deprecated `hash_function` option in session_start().
 *
 * In PHP 8+, passing `hash_function` to session_start() raises an error.
 * These tests verify that Session::start() runs cleanly in the CLI context
 * (where $_SESSION is set directly) and that no deprecated option leaks into
 * a web-like context.
 */
class SessionTest extends TestCase
{
    protected function setUp(): void
    {
        // Reset static state before each test using Reflection
        $ref = new \ReflectionClass(Session::class);

        $started = $ref->getProperty('started');
        $started->setAccessible(true);
        $started->setValue(null, false);

        $regenerated = $ref->getProperty('regenerated');
        $regenerated->setAccessible(true);
        $regenerated->setValue(null, false);
    }

    public function testStartDoesNotThrowInCliMode()
    {
        // PHP_SAPI is 'cli' in test context; start() should set $_SESSION = []
        $this->assertSame('cli', PHP_SAPI);

        Session::start();

        $this->assertIsArray($_SESSION);
    }

    public function testStartIsIdempotent()
    {
        Session::start();
        Session::start(); // second call should be a no-op

        $this->assertTrue(true); // If we reach here, no error was thrown
    }

    public function testSetAndGet()
    {
        Session::start();
        Session::set('foo', 'bar');
        $this->assertSame('bar', Session::get('foo'));
    }

    public function testGetWithDefault()
    {
        Session::start();
        $this->assertSame('default_value', Session::get('nonexistent', 'default_value'));
    }

    public function testHas()
    {
        Session::start();
        Session::set('key', 'value');
        $this->assertTrue(Session::has('key'));
        $this->assertFalse(Session::has('missing'));
    }

    public function testRemove()
    {
        Session::start();
        Session::set('temp', 'data');
        Session::remove('temp');
        $this->assertFalse(Session::has('temp'));
    }

    public function testFlashAndGetFlash()
    {
        Session::start();
        Session::flash('success', 'Operation done');
        $this->assertSame('Operation done', Session::getFlash('success'));
    }

    public function testPull()
    {
        Session::start();
        Session::set('token', 'abc123');
        $value = Session::pull('token');
        $this->assertSame('abc123', $value);
        $this->assertFalse(Session::has('token'));
    }

    public function testIncrement()
    {
        Session::start();
        Session::set('counter', 5);
        $result = Session::increment('counter', 3);
        $this->assertSame(8, $result);
    }

    public function testDecrement()
    {
        Session::start();
        Session::set('counter', 10);
        Session::decrement('counter', 2);
        $this->assertSame(8, Session::get('counter'));
    }

    public function testSessionOptionsDoNotContainHashFunction()
    {
        // Verify by inspection: read Session::start() source and ensure
        // hash_function is absent. We check via ReflectionClass on the source file.
        $source = file_get_contents(
            __DIR__ . '/../Core/Session/Session.php'
        );
        $this->assertStringNotContainsString(
            'hash_function',
            $source,
            'hash_function option must not appear in Session::start() — it was removed in PHP 8.0'
        );
    }
}
