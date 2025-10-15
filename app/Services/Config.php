<?php

namespace App\Services;

use App\Actions\Scan\FunctionScanner;
use App\Actions\Scan\RuntimeScanner;
use App\Data\Config\FunctionConfig;
use App\Data\Config\RuntimeConfig;
use Illuminate\Support\Facades\Cache;

class Config
{
    public function __construct(
        protected FunctionScanner $functionScanner,
        protected RuntimeScanner $runtimeScanner
    ) {}

    /**
     * Get all available functions
     */
    public function functions(): array
    {
        return Cache::remember('config.functions', now()->addMinutes(60), function () {
            return collect($this->functionScanner->scan())
                ->map(fn ($function) => $function) // FunctionConfig::from($function))
                ->all();
        });
    }

    /**
     * Get all available runtimes
     */
    public function runtimes(): array
    {
        return Cache::remember('config.runtimes', now()->addMinutes(60), function () {
            return collect($this->runtimeScanner->scan())
                ->map(fn ($runtime) => RuntimeConfig::from($runtime))
                ->all();
        });
    }

    /**
     * Get a runtime by its path
     */
    public function runtime(string $path): ?RuntimeConfig
    {
        return collect($this->runtimes())->firstWhere('path', $path);
    }

    /**
     * Get a function by its path
     */
    public function function(string $path): ?FunctionConfig
    {
        return collect($this->functions())->firstWhere('path', $path);
    }
}
