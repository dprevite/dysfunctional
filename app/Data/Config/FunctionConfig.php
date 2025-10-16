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
        public readonly ?array $environment = null,
        public readonly ?array $schedule = null,
    )
    {
    }

    public function runtime(): RuntimeConfig
    {
        $config = app(ConfigService::class);

        return $config->runtime($this->runtime);
    }

    public function getEntrypoint(): ?string
    {
        return $this->getBasePath() . '/' . $this->entrypoint;
    }

    public function getBasePath(): string
    {
        $path = storage_path('config/functions/' . $this->path);

        if (str_starts_with($path, '/app')) {
            $path = substr($path, 4);
        }

        return $path;
    }

    public function getRawYaml(): ?string
    {
        return file_get_contents(
            base_path($this->getBasePath() . '/function.yml')
        );
    }
}
