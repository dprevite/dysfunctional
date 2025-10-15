<?php

declare(strict_types=1);

namespace App\Console\Commands\Scan;

use Exception;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

/**
 * Scan and list all function definitions in the functions directory.
 */
final class Functions extends Scan
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scan:functions
                            {--path= : Custom path to functions directory}
                            {--validate : Validate function definitions against schema}
                            {--json : Output results as JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan and list all function definitions in the functions directory';

    /**
     * Get the default path to scan.
     */
    protected function getDefaultPath(): string
    {
        return storage_path('config/functions');
    }

    /**
     * Get the config file name to search for.
     */
    protected function getConfigFileName(): string
    {
        return 'function.yml';
    }

    /**
     * Parse and extract function metadata from a YAML file.
     *
     * @return array<string, mixed>|null Function metadata array or null on error
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

            $function = [
                'path' => $relativePath,
                'file' => $filePath,
                'name' => $data['function']['name'] ?? 'Unknown',
                'description' => $data['function']['description'] ?? '',
                'route' => $data['function']['route'] ?? '',
                'method' => $data['function']['method'] ?? '',
                'runtime' => $data['function']['runtime'] ?? '',
                'entrypoint' => $data['function']['entrypoint'] ?? '',
            ];

            if ($this->option('validate')) {
                $this->validateFunction($data, $filePath);
            }

            return $function;
        } catch (Exception $e) {
            $this->error("Error processing {$filePath}: " . $e->getMessage());

            return null;
        }
    }

    /**
     * Display functions in a formatted table.
     *
     * @param  array<int, array<string, mixed>>  $results
     */
    protected function displayResults(array $results): void
    {
        $headers = ['Path', 'Name', 'Route', 'Method', 'Runtime'];
        $rows = array_map(fn ($f) => [
            $f['path'],
            $f['name'],
            $f['route'],
            $f['method'],
            $f['runtime'],
        ], $results);

        $this->table($headers, $rows);

        if ($this->option('validate')) {
            $this->newLine();
            $this->info('✓ Validation complete');
        }
    }

    /**
     * Validate function definition against schema requirements.
     *
     * @param  array<string, mixed>  $data  Parsed YAML data
     */
    protected function validateFunction(array $data, string $filePath): void
    {
        $errors = [];

        if (! isset($data['function'])) {
            $errors[] = "Missing 'function' section";
        } else {
            $required = ['name', 'description', 'route', 'method', 'runtime', 'entrypoint'];
            foreach ($required as $field) {
                if (! isset($data['function'][$field])) {
                    $errors[] = "Missing required field: function.{$field}";
                }
            }

            if (isset($data['function']['route']) && ! str_starts_with($data['function']['route'], '/')) {
                $errors[] = "Route must start with '/'";
            }

            if (isset($data['function']['method'])) {
                $validMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];
                if (! in_array($data['function']['method'], $validMethods)) {
                    $errors[] = "Invalid HTTP method: {$data['function']['method']}";
                }
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
