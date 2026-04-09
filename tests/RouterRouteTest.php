<?php

namespace Teguh02\Rijanphp\Tests;

use PHPUnit\Framework\TestCase;
use Teguh02\Rijanphp\Core\Router\Router;

/**
 * Tests for Bug #14: Router::route() discards unmatched params & undefined $uri variable.
 *
 * route('name', ['param' => 'value']) must:
 *  - Replace {param} wildcards in the URI with the provided values
 *  - Append any remaining (unmatched) key-value pairs as a query string
 *  - Not throw an "Undefined variable $uri" error
 */
class RouterRouteTest extends TestCase
{
    protected function setUp(): void
    {
        Router::clear();
    }

    // -------------------------------------------------------------------------
    // Basic named route resolution (no params)
    // -------------------------------------------------------------------------

    public function testRouteWithNoParamsReturnsUri()
    {
        Router::get('/dashboard', fn() => '')->name('dashboard');
        $this->assertSame('/dashboard', Router::route('dashboard'));
    }

    // -------------------------------------------------------------------------
    // Route param substitution
    // -------------------------------------------------------------------------

    public function testRouteSubstitutesPathParam()
    {
        Router::get('/users/{id}', fn() => '')->name('users.show');
        $this->assertSame('/users/42', Router::route('users.show', ['id' => 42]));
    }

    public function testRouteSubstitutesMultiplePathParams()
    {
        Router::get('/posts/{post}/comments/{comment}', fn() => '')->name('posts.comments.show');
        $result = Router::route('posts.comments.show', ['post' => 5, 'comment' => 99]);
        $this->assertSame('/posts/5/comments/99', $result);
    }

    // -------------------------------------------------------------------------
    // Bug #14: Unmatched params become query string
    // -------------------------------------------------------------------------

    public function testUnmatchedParamsAreAppendedAsQueryString()
    {
        Router::get('/files', fn() => '')->name('files.index');
        $result = Router::route('files.index', ['path' => 'home/docs', 'page' => 2]);

        $this->assertStringContainsString('?', $result);
        $this->assertStringContainsString('path=', $result);
        $this->assertStringContainsString('page=2', $result);
    }

    public function testMixedMatchedAndUnmatchedParams()
    {
        Router::get('/users/{id}/files', fn() => '')->name('user.files');
        $result = Router::route('user.files', ['id' => 7, 'path' => 'public/img']);

        $this->assertStringStartsWith('/users/7/files', $result);
        $this->assertStringContainsString('path=', $result);
        $this->assertStringNotContainsString('{id}', $result);
    }

    public function testNoQueryStringWhenAllParamsMatchRoute()
    {
        Router::get('/orders/{order}', fn() => '')->name('orders.show');
        $result = Router::route('orders.show', ['order' => 100]);

        $this->assertStringNotContainsString('?', $result);
        $this->assertSame('/orders/100', $result);
    }

    public function testEmptyParamsReturnsUriWithNoQueryString()
    {
        Router::get('/profile', fn() => '')->name('profile');
        $result = Router::route('profile', []);

        $this->assertStringNotContainsString('?', $result);
        $this->assertSame('/profile', $result);
    }

    // -------------------------------------------------------------------------
    // Undefined route must throw an exception (not undefined variable error)
    // -------------------------------------------------------------------------

    public function testThrowsExceptionForUndefinedNamedRoute()
    {
        $this->expectException(\Exception::class);
        Router::route('nonexistent.route');
    }

    public function testExceptionMessageContainsRouteName()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/nonexistent\.route/');
        Router::route('nonexistent.route');
    }
}
