<?php

declare(strict_types=1);

namespace App\Actions\Scan;

use App\Data\Config\FunctionConfig;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

/**
 * Scan and process function config definitions.
 */
class FunctionScanner extends Scanner
{
    public function __construct(
        protected bool $validate = false
    ) {}

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
     */
    protected function process(string $filePath, string $basePath): ?FunctionConfig
    {
        $data = Yaml::parse(File::get($filePath));

        $functionData = $data['function'] ?? [];

        unset($data['function']);

        return FunctionConfig::from(
            array_merge(
                [
                    'path' => str_replace($basePath . '/', '', dirname($filePath)),
                    'validationErrors' => $this->validate($data),
                ],
                $data,
                $functionData
            )
        );
    }

    /**
     * Validate function definition against schema requirements.
     *
     * @param  array<string, mixed>  $data  Parsed YAML data
     * @return string[] Array of validation error messages
     */
    protected function validate(array $data): array
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

        return $errors;
    }
}
