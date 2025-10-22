<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RequestFormatter
{
    /**
     * Default truncation limit for request body
     */
    protected int $bodyLimit;

    /**
     * Default truncation limit for individual headers
     */
    protected int $headerLimit;

    public function __construct(int $bodyLimit = 1000, int $headerLimit = 200)
    {
        $this->bodyLimit   = $bodyLimit;
        $this->headerLimit = $headerLimit;
    }

    /**
     * Format a request object as plain text
     */
    public function format(Request $request, ?int $bodyLimit = null): string
    {
        $bodyLimit = $bodyLimit ?? $this->bodyLimit;

        $output = [];

        // Request line
        $output[] = sprintf(
            '%s %s %s',
            $request->method(),
            $request->fullUrl(),
            $request->server('SERVER_PROTOCOL', 'HTTP/1.1')
        );
        $output[] = '';

        // Headers
        $output[] = '=== HEADERS ===';
        foreach ($request->headers->all() as $name => $values) {
            $name = $this->normalizeHeaderName($name);
            foreach ($values as $value) {
                // Truncate long header values
                if (strlen($value) > $this->headerLimit) {
                    $value = Str::limit($value, $this->headerLimit);
                }
                $output[] = "$name: $value";
            }
        }
        $output[] = '';

        // Query parameters
        if ($request->query()) {
            $output[] = '=== QUERY PARAMETERS ===';
            foreach ($request->query() as $key => $value) {
                $output[] = $this->formatParameter($key, $value);
            }
            $output[] = '';
        }

        // Request body
        $body = $this->getRequestBody($request);
        if (! empty($body)) {
            $output[] = '=== BODY ===';
            $output[] = $this->formatBody($body, $bodyLimit);
            $output[] = '';
        }

        // Request metadata
        $output[] = '=== METADATA ===';
        $output[] = 'IP: ' . $request->header('X-Real-Ip', $request->ip());
        $output[] = 'User Agent: ' . Str::limit($request->userAgent() ?? 'N/A', 200);
        $output[] = 'Content Type: ' . ($request->header('Content-Type') ?? 'N/A');
        $output[] = 'Content Length: ' . ($request->header('Content-Length') ?? 'N/A');

        return implode("\n", $output);
    }

    /**
     * Get the request body content
     */
    protected function getRequestBody(Request $request): string
    {
        // Try to get JSON content first
        if ($request->isJson()) {
            $json = $request->json()->all();

            return json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        // Try form data
        if ($request->all()) {
            return json_encode($request->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        // Fall back to raw content
        return $request->getContent();
    }

    /**
     * Format and truncate body content
     */
    protected function formatBody(string $body, int $limit): string
    {
        if (strlen($body) <= $limit) {
            return $body;
        }

        $truncated      = substr($body, 0, $limit);
        $originalLength = strlen($body);
        $truncatedBytes = $originalLength - $limit;

        return $truncated . "\n\n[... TRUNCATED {$truncatedBytes} bytes of {$originalLength} total ...]";
    }

    /**
     * Format a parameter (handles arrays and objects)
     *
     * @param  mixed  $value
     */
    protected function formatParameter(string $key, $value, int $depth = 0): string
    {
        $indent = str_repeat('  ', $depth);

        if (is_array($value)) {
            $lines = ["$indent$key:"];
            foreach ($value as $k => $v) {
                $lines[] = $this->formatParameter($k, $v, $depth + 1);
            }

            return implode("\n", $lines);
        }

        if (is_object($value)) {
            return "$indent$key: " . json_encode($value);
        }

        if (is_bool($value)) {
            return "$indent$key: " . ($value ? 'true' : 'false');
        }

        if (is_null($value)) {
            return "$indent$key: null";
        }

        return "$indent$key: $value";
    }

    /**
     * Normalize header names to Title-Case
     */
    protected function normalizeHeaderName(string $name): string
    {
        return str_replace(' ', '-', ucwords(str_replace('-', ' ', strtolower($name))));
    }
}
