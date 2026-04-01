<?php

namespace Teguh02\Rijanphp\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Teguh02\Rijanphp\Core\Rijan;
use Teguh02\Rijanphp\Core\Router\Router;
use Teguh02\Rijanphp\Core\View\View;
use Teguh02\Rijanphp\Core\Security\Hash;
use Teguh02\Rijanphp\Core\Security\Encrypter;
use Teguh02\Rijanphp\Core\Security\Csrf;
use Teguh02\Rijanphp\Core\Session\Session;
use Teguh02\Rijanphp\Core\Cookie\Cookie;
use Teguh02\Rijanphp\Core\Http\Request;
use Teguh02\Rijanphp\Core\Http\Response;

class FrameworkTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Router::clear();
        View::clear();
    }

    public function testVersionConstant()
    {
        $this->assertMatchesRegularExpression('/^\d+\.\d+\.\d+$/', Rijan::version());
    }

    public function testRouterRegistration()
    {
        Router::get('test', function() {
            return 'test';
        });

        $routes = Router::getRoutes();
        $this->assertArrayHasKey('GET', $routes);
        $this->assertCount(1, $routes['GET']);
    }

    public function testRouterGroup()
    {
        Router::group(['prefix' => 'api'], function() {
            Router::get('users', function() {
                return 'users';
            });
        });

        $routes = Router::getRoutes();
        $this->assertEquals('/api/users', $routes['GET'][0]['path']);
    }

    public function testNamedRoute()
    {
        Router::get('home', function() {
            return 'home';
        })->name('home');

        $this->assertEquals('/home', Router::route('home'));
    }

    public function testRouteWithParameters()
    {
        Router::get('users/{id}', function() {
            return 'user';
        })->name('user.show');

        $this->assertEquals('/users/123', Router::route('user.show', ['id' => '123']));
    }

    public function testDuplicateRouteThrowsException()
    {
        Router::get('duplicate', function() {
            return 'first';
        });

        $this->expectException(\Exception::class);
        Router::get('duplicate', function() {
            return 'second';
        });
    }

    public function testViewSectionHelpers()
    {
        View::section('content');
        echo '<h1>Test</h1>';
        View::endSection();

        $this->assertEquals('<h1>Test</h1>', View::yield('content'));
    }

    public function testViewYieldWithDefault()
    {
        $this->assertEquals('default', View::yield('nonexistent', 'default'));
    }

    public function testHashMakeAndCheck()
    {
        $password = 'testpassword123';
        $hash = Hash::make($password);

        $this->assertTrue(Hash::check($password, $hash));
        $this->assertFalse(Hash::check('wrongpassword', $hash));
    }

    public function testHashNeedsRehash()
    {
        $hash = Hash::make('password');
        $this->assertFalse(Hash::needsRehash($hash, ['cost' => 10]));
    }

    public function testEncryptAndDecrypt()
    {
        $key = random_bytes(32);
        $encrypter = new Encrypter($key);

        $data = 'sensitive information';
        $encrypted = $encrypter->encrypt($data);
        $decrypted = $encrypter->decrypt($encrypted);

        $this->assertEquals($data, $decrypted);
    }

    public function testEncryptReturnsDifferentStrings()
    {
        $key = random_bytes(32);
        $encrypter = new Encrypter($key);

        $encrypted1 = $encrypter->encrypt('same');
        $encrypted2 = $encrypter->encrypt('same');

        $this->assertNotEquals($encrypted1, $encrypted2);
    }

    public function testDecryptWithWrongKey()
    {
        $key1 = random_bytes(32);
        $key2 = random_bytes(32);

        $encrypter1 = new Encrypter($key1);
        $encrypter2 = new Encrypter($key2);

        $encrypted = $encrypter1->encrypt('secret');

        $this->expectException(\Exception::class);
        $encrypter2->decrypt($encrypted);
    }

    public function testCsrfGenerateAndVerify()
    {
        Session::start();
        $token = Csrf::generate();

        $this->assertTrue(strlen($token) === 64);
        $this->assertTrue(Csrf::verify($token));
        $this->assertFalse(Csrf::verify('invalid-token'));
    }

    public function testCsrfTokenReturnsExisting()
    {
        Session::start();
        $token1 = Csrf::generate();
        $token2 = Csrf::token();

        $this->assertEquals($token1, $token2);
    }

    public function testResponseJson()
    {
        $response = (new Response())->json(['status' => 'ok']);

        $this->assertJson($response->getContent());
        $this->assertEquals('application/json', $response->getHeader('Content-Type'));
    }

    public function testResponseStatus()
    {
        $response = (new Response())->status(404);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testResponseRedirect()
    {
        $response = (new Response())->redirect('/login');
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/login', $response->getHeader('Location'));
    }

    public function testSessionSetAndGet()
    {
        Session::start();
        Session::set('user_id', 123);

        $this->assertEquals(123, Session::get('user_id'));
    }

    public function testSessionHas()
    {
        Session::start();
        Session::set('exists', true);

        $this->assertTrue(Session::has('exists'));
        $this->assertFalse(Session::has('not_exists'));
    }

    public function testSessionRemove()
    {
        Session::start();
        Session::set('temp', 'value');
        Session::remove('temp');

        $this->assertFalse(Session::has('temp'));
    }

    public function testSessionFlashData()
    {
        Session::start();
        Session::flash('message', 'Hello');

        $this->assertEquals('Hello', Session::getFlash('message'));
    }

    public function testCookieQueue()
    {
        Cookie::clearQueuedCookies();
        Cookie::queue('queued_cookie', 'queued_value', 60);

        $queued = Cookie::getQueuedCookies();
        $this->assertArrayHasKey('queued_cookie', $queued);
        $this->assertEquals('queued_value', $queued['queued_cookie']['value']);
    }

    public function testCookieDelete()
    {
        $_COOKIE['delete_me'] = 'value';
        Cookie::delete('delete_me');

        $this->assertFalse(Cookie::has('delete_me'));
    }

    public function testCookieForever()
    {
        Cookie::clearQueuedCookies();
        Cookie::forever('forever_cookie', 'forever_value');

        $queued = Cookie::getQueuedCookies();
        $this->assertArrayHasKey('forever_cookie', $queued);
    }

    public function testCookieForget()
    {
        $_COOKIE['forget_me'] = 'value';
        Cookie::forget('forget_me');

        $this->assertFalse(Cookie::has('forget_me'));
    }
}
