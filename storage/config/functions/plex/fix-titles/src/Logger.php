<?php

namespace PlexQuality;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger as MonologLogger;

class Logger
{
    private static ?MonologLogger $instance = null;

    /**
     * Get the logger instance
     */
    public static function getInstance(): MonologLogger
    {
        if (self::$instance === null) {
            self::$instance = self::createLogger();
        }

        return self::$instance;
    }

    /**
     * Create the logger instance
     */
    private static function createLogger(): MonologLogger
    {
        $logger = new MonologLogger('plex-title-fixer');

        // Try primary log location
        $logPath = __DIR__ . '/../storage/logs/app.log';

        if (!self::ensureLogPathWritable($logPath)) {
            // Fallback to /tmp if primary location fails
            $logPath = '/tmp/plex-title-fixer.log';
            throw new \RuntimeException("Primary log path is not writable. Falling back to /tmp. Please check permissions.");
        }

        // Add stream handler for file logging
        $logger->pushHandler(
            new StreamHandler($logPath, Level::Debug)
        );

        return $logger;
    }

    /**
     * Ensure log path is writable
     */
    private static function ensureLogPathWritable(string $logPath): bool
    {
        $logDir = dirname($logPath);

        // Try to create directory if it doesn't exist
        if (!is_dir($logDir)) {
            $created = @mkdir($logDir, 0777, true);

            if (!$created) {
                return false;
            }

            // Try to make it writable
            @chmod($logDir, 0777);
        }

        // Check if directory is writable
        if (!is_writable($logDir)) {
            return false;
        }

        // If log file exists, check if it's writable
        if (file_exists($logPath) && !is_writable($logPath)) {
            return false;
        }

        // Try to touch the file to ensure we can write to it
        $touched = @touch($logPath);
        if (!$touched) {
            return false;
        }

        // Try to make file writable
        @chmod($logPath, 0666);

        return true;
    }

    /**
     * Reset the logger instance (useful for testing)
     */
    public static function reset(): void
    {
        self::$instance = null;
    }
}

