<?php

declare(strict_types=1);

namespace App\Data\Config;

use Illuminate\Support\Str;

class RuntimeConfig extends Config
{
    public function __construct(
        public readonly string $path,
        public readonly string $file,
        public readonly string $language,
        public readonly string $version,
        public readonly string $platform,
        public readonly ?array $build = null,
    )
    {
    }

    public function getDockerFile(): string
    {
        return file_get_contents(storage_path("config/runtimes/{$this->path}/Dockerfile"));
    }

    public function getDockerImageTag(): string
    {
        return sprintf(
            'dysfunctional-%s-%s-%s:latest',
            Str::slug($this->platform),
            Str::slug($this->language),
            Str::slug($this->version)
        );
    }
}
