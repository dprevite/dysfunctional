<?php

declare(strict_types=1);

namespace App\Actions\Scan;

use Exception;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

/**
 * Scan and process runtime config definitions.
 */
final class RuntimeScanner extends Scanner
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
        return storage_path('config/runtimes');
    }

    /**
     * Get the config file name to search for.
     */
    protected function getFileName(): string
    {
        return 'runtime.yml';
    }

    /**
     * Parse and extract runtime metadata from a YAML file.
     *
     * @return array<string, mixed>|null Runtime metadata array or null on error
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

            $runtime = [
                'path' => $relativePath,
                'file' => $filePath,
                'language' => $data['language'] ?? 'Unknown',
                'version' => $data['version'] ?? '',
                'platform' => $data['platform'] ?? '',
            ];

            if ($this->validate) {
                $runtime['validation_errors'] = $this->validate($data);
            }

            return $runtime;
        } catch (Exception) {
            return null;
        }
    }

    /**
     * Validate runtime definition against schema requirements.
     *
     * @param array<string, mixed> $data Parsed YAML data
     * @return string[] Array of validation error messages
     */
    protected function validate(array $data): array
    {
        $errors = [];

        $required = ['language', 'platform'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                $errors[] = "Missing required field: {$field}";
            }
        }

        return $errors;
    }
}
