<?php

declare(strict_types=1);

namespace App\Data\Config;

use App\Services\Config as ConfigService;

class FunctionConfig extends Config
{
    public function __construct(
        public readonly string $path,
        public readonly string $name,
        public readonly string $description,
        public readonly string $method,
        public readonly string $route,
        public readonly string $runtime,
        public readonly string $entrypoint,
        public readonly ?array $validationErrors = null,
        public readonly ?array $docker = null,
    )
    {
    }

    public function runtime(): RuntimeConfig
    {
        $config = app(ConfigService::class);

        return $config->runtime('language/php/serversideup/8.4/cli');
    }

    public function getEntrypoint(): ?string
    {
        $path = storage_path('config/functions/' . $this->path . '/' . $this->entrypoint);

        if (!file_exists($path)) {
            return null;
        }

        if (str_starts_with($path, '/app')) {
            $path = substr($path, 4);
        }

        return $path;
    }
}
