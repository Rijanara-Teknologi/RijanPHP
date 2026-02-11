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

        // Initialize default local disk
        $this->disks['local'] = [
            'root' => $this->basePath . 'storage' . DIRECTORY_SEPARATOR . 'app',
            'url' => env('APP_URL', 'http://localhost') . '/storage'
        ];
    }

    /**
     * Get a disk instance.
     */
    public function disk(?string $name = null)
    {
        $name = $name ?: $this->defaultDisk;

        if (!isset($this->disks[$name])) {
            throw new \Exception("Disk [{$name}] not configured.");
        }

        return $this;
    }

    /**
     * Store a file.
     */
    public function put(string $path, string $contents): bool
    {
        $fullPath = $this->getFullPath($path);
        $directory = dirname($fullPath);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        return file_put_contents($fullPath, $contents) !== false;
    }

    /**
     * Retrieve a file's contents.
     */
    public function get(string $path): ?string
    {
        $fullPath = $this->getFullPath($path);

        if (!$this->exists($path)) {
            return null;
        }

        return file_get_contents($fullPath);
    }

    /**
     * Delete a file.
     */
    public function delete(string $path): bool
    {
        if (!$this->exists($path)) {
            return false;
        }

        return unlink($this->getFullPath($path));
    }

    /**
     * Check if a file exists.
     */
    public function exists(string $path): bool
    {
        return file_exists($this->getFullPath($path));
    }

    /**
     * Get the public URL for a file.
     */
    public function url(string $path): string
    {
        $disk = $this->disks[$this->defaultDisk];
        return rtrim($disk['url'], '/') . '/' . ltrim($path, '/');
    }

    /**
     * Get the full filesystem path for a relative path.
     */
    protected function getFullPath(string $path): string
    {
        $disk = $this->disks[$this->defaultDisk];
        return rtrim($disk['root'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
    }
}
