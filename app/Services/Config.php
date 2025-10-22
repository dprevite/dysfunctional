<?php

namespace App\Services;

use App\Actions\Scan\FunctionScanner;
use App\Actions\Scan\RuntimeScanner;
use App\Data\Config\FunctionConfig;
use App\Data\Config\RuntimeConfig;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Uri;

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

    /**
     * Find a function based on the current route
     */
    public function functionMatchingRoute(string $method, Uri $uri): ?FunctionConfig
    {
        $requestPath = preg_replace('/^\/?run/', '', $uri->path());
        $method      = strtoupper($method);

        $functions = $this->functions();

        foreach ($functions as $function) {
            if (strtoupper($function->method) === $method && $this->matchesRoute($function->route, $requestPath)) {
                return $function;
            }
        }

        return null;
    }

    /**
     * Check if a route pattern matches the request path
     */
    protected function matchesRoute(string $routePattern, string $requestPath): bool
    {
        // Remove leading/trailing slashes for comparison
        $routePattern = trim($routePattern, '/');
        $requestPath  = trim($requestPath, '/');

        // Exact match
        if ($routePattern === $requestPath) {
            return true;
        }

        // Convert route parameters like {id} or {param} to regex pattern
        // This handles Laravel-style route parameters
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePattern);

        // Handle optional parameters like {param?}
        $pattern = str_replace('?}', '}?', $pattern);
        $pattern = preg_replace('/\{[^}]+\}\?/', '([^/]*)', $pattern);

        // Handle wildcard routes (e.g., admin/*)
        $pattern = str_replace('*', '.*', $pattern);

        // Build the final regex pattern
        $pattern = '#^' . $pattern . '$#';

        // Test the pattern against the request path
        return (bool) preg_match($pattern, $requestPath);
    }
}
