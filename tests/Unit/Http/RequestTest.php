<?php
namespace Teguh02\Rijanphp\Tests\Unit\Http;

use Teguh02\Rijanphp\Tests\TestCase;
use Teguh02\Rijanphp\Core\Http\Request;

class RequestTest extends TestCase
{
    public function testGetMethod(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $request = new Request();
        $this->assertEquals('GET', $request->method());
    }

    public function testPostMethod(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['name'] = 'John';
        $request = new Request();
        $this->assertEquals('POST', $request->method());
        $this->assertEquals('John', $request->input('name'));
    }

    public function testGetInput(): void
    {
        $_GET['id'] = 123;
        $request = new Request();
        $this->assertEquals(123, $request->input('id'));
        $this->assertEquals(123, $request->query('id'));
    }

    public function testAllInput(): void
    {
        $_GET['page'] = 1;
        $_POST['search'] = 'test';
        $request = new Request();
        $all = $request->all();
        $this->assertArrayHasKey('page', $all);
        $this->assertArrayHasKey('search', $all);
    }

    public function testOnlyInput(): void
    {
        $_POST['name'] = 'John';
        $_POST['email'] = 'john@example.com';
        $_POST['password'] = 'secret';
        $request = new Request();
        $only = $request->only(['name', 'email']);
        $this->assertArrayHasKey('name', $only);
        $this->assertArrayHasKey('email', $only);
        $this->assertArrayNotHasKey('password', $only);
    }

    public function testHasInput(): void
    {
        $_POST['username'] = 'john_doe';
        $request = new Request();
        $this->assertTrue($request->has('username'));
        $this->assertFalse($request->has('nonexistent'));
    }
}
