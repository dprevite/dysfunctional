<?php

declare(strict_types=1);

namespace App\Console\Commands\Scan;

use Exception;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

/**
 * Scan and list all runtime config definitions.
 */
final class Runtimes extends Scan
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scan:runtimes
                            {--path= : Custom path to runtimes directory}
                            {--validate : Validate runtime definitions against schema}
                            {--json : Output results as JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan and list all runtime definitions in the runtimes directory';

    /**
     * Get the default path to scan.
     */
    protected function getDefaultPath(): string
    {
        return storage_path('config/runtimes');
    }

    /**
     * Get the config file name to search for.
     */
    protected function getConfigFileName(): string
    {
        return 'runtime.yml';
    }

    /**
     * Parse and extract runtime metadata from a YAML file.
     *
     * @return array<string, mixed>|null Runtime metadata array or null on error
     */
    protected function processFile(string $filePath, string $basePath): ?array
    {
        try {
            $content = File::get($filePath);
            $data = Yaml::parse($content);

            if (! is_array($data)) {
                $this->error("Invalid YAML in {$filePath}");

                return null;
            }

            $relativePath = str_replace($basePath . '/', '', dirname($filePath));

            $runtime = [
                'path' => $relativePath,
                'file' => $filePath,
                'language' => $data['language'] ?? 'Unknown',
                'version' => $data['version'] ?? '',
                'platform' => $data['platform'] ?? '',
            ];

            if ($this->option('validate')) {
                $this->validateRuntime($data, $filePath);
            }

            return $runtime;
        } catch (Exception $e) {
            $this->error("Error processing {$filePath}: " . $e->getMessage());

            return null;
        }
    }

    /**
     * Display runtimes in a formatted table.
     *
     * @param  array<int, array<string, mixed>>  $results
     */
    protected function displayResults(array $results): void
    {
        $headers = ['Path', 'Language', 'Version', 'Platform'];
        $rows = array_map(fn ($r) => [
            $r['path'],
            $r['language'],
            $r['version'] ?: '-',
            $r['platform'],
        ], $results);

        $this->table($headers, $rows);

        if ($this->option('validate')) {
            $this->newLine();
            $this->info('✓ Validation complete');
        }
    }

    /**
     * Validate runtime definition against schema requirements.
     *
     * @param  array<string, mixed>  $data  Parsed YAML data
     */
    protected function validateRuntime(array $data, string $filePath): void
    {
        $errors = [];

        $required = ['language', 'platform'];
        foreach ($required as $field) {
            if (! isset($data[$field])) {
                $errors[] = "Missing required field: {$field}";
            }
        }

        if (! empty($errors)) {
            $this->warn("Validation errors in {$filePath}:");
            foreach ($errors as $error) {
                $this->line("  • {$error}");
            }
        }
    }
}
