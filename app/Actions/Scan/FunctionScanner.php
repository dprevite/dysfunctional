<?php

declare(strict_types=1);

namespace App\Actions\Scan;

use Exception;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

/**
 * Scan and process function config definitions.
 */
final class FunctionScanner extends Scanner
{
    public function __construct(
        protected bool $validate = false
    )
    {
    }

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
    protected function getFileName(): string
    {
        return 'function.yml';
    }

    /**
     * Parse and extract function metadata from a YAML file.
     *
     * @return array<string, mixed>|null Function metadata array or null on error
     */
    protected function process(string $filePath, string $basePath): ?array
    {
        try {
            $content = File::get($filePath);
            $data = Yaml::parse($content);

            if (!is_array($data)) {
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

            if ($this->validate) {
                $function['validation_errors'] = $this->validate($data);
            }

            return $function;
        } catch (Exception) {
            return null;
        }
    }

    /**
     * Validate function definition against schema requirements.
     *
     * @param array<string, mixed> $data Parsed YAML data
     * @return string[] Array of validation error messages
     */
    protected function validate(array $data): array
    {
        $errors = [];

        if (!isset($data['function'])) {
            $errors[] = "Missing 'function' section";
        } else {
            $required = ['name', 'description', 'route', 'method', 'runtime', 'entrypoint'];
            foreach ($required as $field) {
                if (!isset($data['function'][$field])) {
                    $errors[] = "Missing required field: function.{$field}";
                }
            }

            if (isset($data['function']['route']) && !str_starts_with($data['function']['route'], '/')) {
                $errors[] = "Route must start with '/'";
            }

            if (isset($data['function']['method'])) {
                $validMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];
                if (!in_array($data['function']['method'], $validMethods)) {
                    $errors[] = "Invalid HTTP method: {$data['function']['method']}";
                }
            }
        }

        return $errors;
    }
}
