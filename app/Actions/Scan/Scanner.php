<?php

declare(strict_types=1);

namespace App\Actions\Scan;

use App\Data\Config\Config;
use FilesystemIterator;
use Illuminate\Support\Facades\File;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Scan filesystem for config files.
 */
abstract class Scanner
{
    /**
     * Scan a directory and process all matching config files.
     *
     * @param  string|null  $path  Custom path to scan (null uses default)
     * @return array<int, array<string, mixed>> Array of processed config data
     */
    public function scan(?string $path = null): array
    {
        $scanPath = $path ?? $this->getDefaultPath();

        if (! File::isDirectory($scanPath)) {
            return [];
        }

        $files = $this->getFiles($scanPath);

        if (empty($files)) {
            return [];
        }

        $results = [];

        foreach ($files as $file) {
            $config = $this->process($file, $scanPath);
            if ($config !== null) {
                $results[] = $config;
            }
        }

        return $results;
    }

    /**
     * Recursively find all config files in the given directory.
     *
     * @return string[] Array of absolute file paths
     */
    protected function getFiles(string $basePath): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === $this->getFileName()) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Get the default path to scan.
     */
    abstract protected function getDefaultPath(): string;

    /**
     * Get the config file name to search for.
     */
    abstract protected function getFileName(): string;

    /**
     * Parse and extract metadata from a config file.
     */
    abstract protected function process(string $filePath, string $basePath): ?Config;
}
