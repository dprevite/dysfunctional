<?php

declare(strict_types=1);

namespace App\Console\Commands\Scan;

use FilesystemIterator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Abstract base class for scanning and listing config definitions.
 */
abstract class Scan extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = $this->getPath();

        if (! File::isDirectory($path)) {
            $this->error("Directory not found: {$path}");

            return self::FAILURE;
        }

        $files = $this->findFiles($path);

        if (empty($files)) {
            $this->warn(sprintf('No config files found in %s.', $path));

            return self::SUCCESS;
        }

        $this->info('Found ' . count($files) . " config(s):\n");

        $results = [];

        foreach ($files as $file) {
            $config = $this->processFile($file, $path);
            if ($config) {
                $results[] = $config;
            }
        }

        if ($this->option('json')) {
            $this->line(json_encode($results, JSON_PRETTY_PRINT));
        } else {
            $this->displayResults($results);
        }

        return self::SUCCESS;
    }

    /**
     * Get the path to scan.
     */
    protected function getPath(): string
    {
        return $this->option('path') ?: $this->getDefaultPath();
    }

    /**
     * Recursively find all config files in the given directory.
     *
     * @return string[] Array of absolute file paths
     */
    protected function findFiles(string $basePath): array
    {
        $files    = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === $this->getConfigFileName()) {
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
    abstract protected function getConfigFileName(): string;

    /**
     * Parse and extract metadata from a config file.
     *
     * @return array<string, mixed>|null Config metadata array or null on error
     */
    abstract protected function processFile(string $filePath, string $basePath): ?array;

    /**
     * Display results in a formatted output.
     *
     * @param  array<int, array<string, mixed>>  $results
     */
    abstract protected function displayResults(array $results): void;
}
