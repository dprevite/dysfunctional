<?php

declare(strict_types=1);

namespace App\Actions\Scan;

use App\Data\Config\Config;
use App\Data\Config\RuntimeConfig;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

/**
 * Scan and process runtime config definitions.
 */
class RuntimeScanner extends Scanner
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
     * @throws FileNotFoundException
     */
    protected function process(string $filePath, string $basePath): ?Config
    {
        $data = Yaml::parse(File::get($filePath));

        return RuntimeConfig::from(
            array_merge(
                [
                    'file' => $filePath,
                    'path' => str_replace($basePath . '/', '', dirname($filePath)),
                    'validationErrors' => $this->validate($data),
                ],
                $data
            )
        );
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
