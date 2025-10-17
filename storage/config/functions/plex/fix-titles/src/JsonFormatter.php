<?php

namespace PlexQuality;

class JsonFormatter
{
    /**
     * Format results as JSON
     */
    public function format(array $results): string
    {
        $output = [
            'success' => true,
            'total_items_with_issues' => count($results),
            'items' => $results,
        ];

        return json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Format error as JSON
     */
    public function formatError(string $message, ?\Throwable $exception = null): string
    {
        $output = [
            'success' => false,
            'error' => $message,
        ];

        if ($exception !== null) {
            $output['exception'] = [
                'type' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
        }

        return json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
