<?php

namespace Teguh02\Rijanphp\Tests\Feature;

use Teguh02\Rijanphp\Tests\TestCase;

class StorageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Ensure storage directory exists for testing
        if (!is_dir(\BASE_PATH . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app')) {
            mkdir(\BASE_PATH . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app', 0755, true);
        }
    }

    public function test_storage_can_put_and_get_files()
    {
        $path = 'test-file.txt';
        $content = 'Hello RijanPHP Storage';

        storage()->put($path, $content);

        $this->assertTrue(storage()->exists($path));
        $this->assertEquals($content, storage()->get($path));

        storage()->delete($path);
    }

    public function test_storage_can_handle_directories()
    {
        $path = 'nested/dir/file.txt';
        $content = 'Nested content';

        storage()->put($path, $content);

        $this->assertTrue(storage()->exists($path));
        $this->assertEquals($content, storage()->get($path));

        storage()->delete($path);
    }

    public function test_storage_url_generation()
    {
        $path = 'image.jpg';
        $url = storage()->url($path);

        $this->assertStringContainsString('/storage/image.jpg', $url);
    }
}
