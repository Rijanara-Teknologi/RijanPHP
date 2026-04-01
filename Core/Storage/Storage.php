<?php

namespace Teguh02\Rijanphp\Core\Storage;

class Storage
{
    protected $disks = [];
    protected $defaultDisk = 'local';
    protected $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $this->disks['local'] = [
            'root' => $this->basePath . 'storage' . DIRECTORY_SEPARATOR . 'app',
            'url' => env('APP_URL', 'http://localhost') . '/storage'
        ];
    }

    public function disk(?string $name = null)
    {
        $name = $name ?: $this->defaultDisk;

        if (!isset($this->disks[$name])) {
            throw new \Exception("Disk [{$name}] not configured.");
        }

        $this->defaultDisk = $name;
        return $this;
    }

    public function addDisk(string $name, string $root, ?string $url = null)
    {
        $this->disks[$name] = [
            'root' => rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR,
            'url' => $url,
        ];
        return $this;
    }

    public function put(string $path, $contents, $visibility = 'private'): bool
    {
        $this->assertSafePath($path);
        $fullPath = $this->getFullPath($path);
        $directory = dirname($fullPath);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (is_resource($contents)) {
            $result = stream_copy_to_stream($contents, fopen($fullPath, 'w'));
            return $result !== false;
        }

        return file_put_contents($fullPath, $contents) !== false;
    }

    public function putFile(string $path, $file, ?string $name = null): ?string
    {
        if (is_string($file) && file_exists($file)) {
            $name = $name ?: basename($file);
            $contents = file_get_contents($file);
            $this->put(rtrim($path, '/') . '/' . $name, $contents);
            return $name;
        }

        if (is_resource($file)) {
            $name = $name ?: 'file_' . uniqid();
            $this->put(rtrim($path, '/') . '/' . $name, $file);
            return $name;
        }

        return null;
    }

    public function get(string $path): ?string
    {
        $this->assertSafePath($path);

        if (!$this->exists($path)) {
            return null;
        }

        return file_get_contents($this->getFullPath($path));
    }

    public function delete(string $path): bool
    {
        $this->assertSafePath($path);

        if (!$this->exists($path)) {
            return false;
        }

        return unlink($this->getFullPath($path));
    }

    public function deleteDirectory(string $path): bool
    {
        $this->assertSafePath($path);
        $fullPath = $this->getFullPath($path);

        if (!is_dir($fullPath)) {
            return false;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($fullPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getRealPath()) : unlink($item->getRealPath());
        }

        return rmdir($fullPath);
    }

    public function exists(string $path): bool
    {
        $this->assertSafePath($path);
        return file_exists($this->getFullPath($path));
    }

    public function missing(string $path): bool
    {
        return !$this->exists($path);
    }

    public function size(string $path): int
    {
        $this->assertSafePath($path);

        if (!$this->exists($path)) {
            return 0;
        }

        return filesize($this->getFullPath($path));
    }

    public function lastModified(string $path): int
    {
        $this->assertSafePath($path);

        if (!$this->exists($path)) {
            return 0;
        }

        return filemtime($this->getFullPath($path));
    }

    public function files(string $directory = '', $recursive = false): array
    {
        $this->assertSafePath($directory);
        $fullPath = $this->getFullPath($directory);

        if (!is_dir($fullPath)) {
            return [];
        }

        $files = [];
        $iterator = $recursive
            ? new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($fullPath, \RecursiveDirectoryIterator::SKIP_DOTS))
            : new \DirectoryIterator($fullPath);

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $files[] = str_replace($this->getFullPath(''), '', $file->getRealPath());
            }
        }

        return $files;
    }

    public function directories(string $directory = ''): array
    {
        $this->assertSafePath($directory);
        $fullPath = $this->getFullPath($directory);

        if (!is_dir($fullPath)) {
            return [];
        }

        $dirs = [];
        foreach (new \DirectoryIterator($fullPath) as $item) {
            if ($item->isDir() && !$item->isDot()) {
                $dirs[] = $item->getFilename();
            }
        }

        return $dirs;
    }

    public function makeDirectory(string $path): bool
    {
        $this->assertSafePath($path);
        $fullPath = $this->getFullPath($path);

        if (is_dir($fullPath)) {
            return true;
        }

        return mkdir($fullPath, 0755, true);
    }

    public function copy(string $from, string $to): bool
    {
        $this->assertSafePath($from);
        $this->assertSafePath($to);

        $sourcePath = $this->getFullPath($from);
        $destPath = $this->getFullPath($to);

        if (!file_exists($sourcePath)) {
            return false;
        }

        $destDir = dirname($destPath);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        return copy($sourcePath, $destPath);
    }

    public function move(string $from, string $to): bool
    {
        $this->assertSafePath($from);
        $this->assertSafePath($to);

        $sourcePath = $this->getFullPath($from);
        $destPath = $this->getFullPath($to);

        if (!file_exists($sourcePath)) {
            return false;
        }

        $destDir = dirname($destPath);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        return rename($sourcePath, $destPath);
    }

    public function url(string $path): string
    {
        $disk = $this->disks[$this->defaultDisk];
        return rtrim($disk['url'] ?? '', '/') . '/' . ltrim($path, '/');
    }

    protected function getFullPath(string $path): string
    {
        $disk = $this->disks[$this->defaultDisk];
        return rtrim($disk['root'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
    }

    protected function assertSafePath(string $path): void
    {
        $normalized = str_replace(['\\', '..'], ['/', ''], $path);

        if (strpos($normalized, '..') !== false) {
            throw new \InvalidArgumentException("Path traversal detected: {$path}");
        }

        if (preg_match('/^[a-zA-Z]:/', $path)) {
            throw new \InvalidArgumentException("Absolute paths are not allowed: {$path}");
        }

        if (strpos($path, "\0") !== false) {
            throw new \InvalidArgumentException("Null byte detected in path: {$path}");
        }
    }
}
