<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class ResponseFormatter
{
    /**
     * Default truncation limit for response body
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
     * Format a response object as plain text
     */
    public function format(BaseResponse $response, ?int $bodyLimit = null): string
    {
        $bodyLimit = $bodyLimit ?? $this->bodyLimit;

        $output = [];

        // Status line
        $output[] = sprintf(
            'HTTP/%s %d %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            BaseResponse::$statusTexts[$response->getStatusCode()] ?? 'Unknown'
        );
        $output[] = '';

        // Headers
        $output[] = '=== HEADERS ===';
        foreach ($response->headers->all() as $name => $values) {
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

        // Response body
        $body = $this->getResponseBody($response);
        if (! empty($body)) {
            $output[] = '=== BODY ===';
            $output[] = $this->formatBody($body, $bodyLimit);
            $output[] = '';
        }

        // Response metadata
        $output[] = '=== METADATA ===';
        $output[] = 'Response Type: ' . $this->getResponseType($response);
        $output[] = 'Content Type: ' . ($response->headers->get('Content-Type') ?? 'N/A');
        $output[] = 'Content Length: ' . ($response->headers->get('Content-Length') ?? strlen($body));
        $output[] = 'Cache Control: ' . ($response->headers->get('Cache-Control') ?? 'N/A');

        // Additional info for redirects
        if ($response instanceof RedirectResponse) {
            $output[] = 'Redirect Target: ' . $response->getTargetUrl();
        }

        // Cookies if present
        $cookies = $response->headers->getCookies();
        if (! empty($cookies)) {
            $output[] = '';
            $output[] = '=== COOKIES ===';
            foreach ($cookies as $cookie) {
                $output[] = sprintf(
                    '%s: %s (expires: %s, path: %s, domain: %s, secure: %s, httpOnly: %s)',
                    $cookie->getName(),
                    Str::limit($cookie->getValue(), 100),
                    $cookie->getExpiresTime() ? date('Y-m-d H:i:s', $cookie->getExpiresTime()) : 'session',
                    $cookie->getPath(),
                    $cookie->getDomain() ?: 'N/A',
                    $cookie->isSecure() ? 'yes' : 'no',
                    $cookie->isHttpOnly() ? 'yes' : 'no'
                );
            }
        }

        return implode("\n", $output);
    }

    /**
     * Get the response body content
     */
    protected function getResponseBody(BaseResponse $response): string
    {
        if ($response instanceof JsonResponse) {
            return json_encode($response->getData(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        return $response->getContent();
    }

    /**
     * Format and truncate body content
     */
    protected function formatBody(string $body, int $limit): string
    {
        // Try to pretty print JSON if it's valid JSON
        $decoded = json_decode($body);
        if (json_last_error() === JSON_ERROR_NONE) {
            $body = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        if (strlen($body) <= $limit) {
            return $body;
        }

        $truncated      = substr($body, 0, $limit);
        $originalLength = strlen($body);
        $truncatedBytes = $originalLength - $limit;

        return $truncated . "\n\n[... TRUNCATED {$truncatedBytes} bytes of {$originalLength} total ...]";
    }

    /**
     * Get the response type as a string
     */
    protected function getResponseType(BaseResponse $response): string
    {
        if ($response instanceof JsonResponse) {
            return 'JsonResponse';
        }

        if ($response instanceof RedirectResponse) {
            return 'RedirectResponse';
        }

        if ($response instanceof Response) {
            return 'Response';
        }

        return get_class($response);
    }

    /**
     * Normalize header names to Title-Case
     */
    protected function normalizeHeaderName(string $name): string
    {
        return str_replace(' ', '-', ucwords(str_replace('-', ' ', strtolower($name))));
    }
}
