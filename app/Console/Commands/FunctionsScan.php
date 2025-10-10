<?php

namespace App\Console\Commands;

use Exception;
use FilesystemIterator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Yaml\Yaml;

/**
 * Scan and list all function definitions in the functions directory.
 */
class FunctionsScan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'functions:scan
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
     * Execute the console command.
     */
    public function handle(): int
    {
        $functionsPath = $this->getFunctionsPath();

        if (! $this->directoryExists($functionsPath)) {
            $this->error("Functions directory not found: {$functionsPath}");

            return self::FAILURE;
        }

        $functionFiles = $this->findFunctionFiles($functionsPath);

        if (empty($functionFiles)) {
            $this->warn('No function.yml files found in the functions directory.');

            return self::SUCCESS;
        }

        $this->info('Found '.count($functionFiles)." function(s):\n");

        $functions = [];

        foreach ($functionFiles as $file) {
            $function = $this->processFunctionFile($file, $functionsPath);
            if ($function) {
                $functions[] = $function;
            }
        }

        if ($this->option('json')) {
            $this->line(json_encode($functions, JSON_PRETTY_PRINT));
        } else {
            $this->displayFunctionsTable($functions);
        }

        return self::SUCCESS;
    }

    /**
     * Get the functions directory path.
     */
    protected function getFunctionsPath(): string
    {
        return $this->option('path') ?: base_path('../functions');
    }

    /**
     * Check if a directory exists.
     */
    protected function directoryExists(string $path): bool
    {
        return File::isDirectory($path);
    }

    /**
     * Get file contents.
     */
    protected function getFileContents(string $path): string
    {
        return File::get($path);
    }

    /**
     * Recursively find all function.yml files in the given directory.
     *
     * @return string[] Array of absolute file paths
     */
    protected function findFunctionFiles(string $basePath): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === 'function.yml') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Parse and extract function metadata from a YAML file.
     *
     * @return array<string, string>|null Function metadata array or null on error
     */
    protected function processFunctionFile(string $filePath, string $basePath): ?array
    {
        try {
            $content = $this->getFileContents($filePath);
            $data = Yaml::parse($content);

            $relativePath = str_replace($basePath.'/', '', dirname($filePath));

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
            $this->error("Error processing {$filePath}: ".$e->getMessage());

            return null;
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

    /**
     * Display functions in a formatted table.
     *
     * @param  array<int, array<string, string>>  $functions
     */
    protected function displayFunctionsTable(array $functions): void
    {
        $headers = ['Path', 'Name', 'Route', 'Method', 'Runtime'];
        $rows = array_map(fn ($f) => [
            $f['path'],
            $f['name'],
            $f['route'],
            $f['method'],
            $f['runtime'],
        ], $functions);

        $this->table($headers, $rows);

        if ($this->option('validate')) {
            $this->newLine();
            $this->info('✓ Validation complete');
        }
    }
}
