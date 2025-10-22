#!/usr/bin/env php
<?php
/**
 * Script to find Plex media with quality indicators in titles
 * and fetch the correct titles from TMDB.
 */

require __DIR__ . '/vendor/autoload.php';

use PlexQuality\JsonFormatter;
use PlexQuality\Logger;
use PlexQuality\PlexClient;
use PlexQuality\TitleFixer;
use PlexQuality\TMDBClient;

define('PLEX_URL', getenv('PLEX_URL'));
define('PLEX_TOKEN', getenv('PLEX_TOKEN'));
define('TMDB_API_KEY', getenv('TMDB_API_KEY'));
define('IGNORED_LIBRARIES', getenv('IGNORED_LIBRARIES'));
define('UPDATE_TITLES', filter_var(getenv('UPDATE_TITLES'), FILTER_VALIDATE_BOOLEAN));
define('DRY_RUN', getenv('DRY_RUN') !== 'false');

$formatter = new JsonFormatter();
$logger = Logger::getInstance();

try {
    $logger->info('Starting Plex Title Fixer');
    validateEnvironment();

    $plexClient = new PlexClient(PLEX_URL, PLEX_TOKEN);
    $tmdbClient = new TMDBClient(TMDB_API_KEY);
    $ignoredLibraries = parseIgnoredLibraries(IGNORED_LIBRARIES);
    $fixer = new TitleFixer($plexClient, $tmdbClient, $ignoredLibraries);

    $results = $fixer->findItemsWithQualityIndicators();

    $logger->info('Found items with quality indicators', [
        'count' => count($results),
    ]);

    // Save formatted results to cache
    $cacheFile = __DIR__ . '/storage/cache/results.json';
    file_put_contents($cacheFile, $formatter->format($results));

    $logger->info('Results cached', ['file' => $cacheFile]);

    // Update titles if requested
    if (UPDATE_TITLES) {
        $logger->info('Starting title updates', [
            'dry_run' => DRY_RUN,
            'items_to_process' => count($results),
        ]);

        $updateSummary = $fixer->updateTitles($results, DRY_RUN);

        $logger->info('Title update summary', $updateSummary);

        // Output update summary
        echo "\n\n=== UPDATE SUMMARY ===\n";
        echo $formatter->format($updateSummary);
        echo "\n";
    }

    // Output results
    echo $formatter->format($results);

    $logger->info('Plex Title Fixer completed successfully');
    exit(0);
} catch (Throwable $e) {
    $logger->error('An error occurred', [
        'exception' => get_class($e),
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);

    fwrite(STDERR, $formatter->formatError('An error occurred', $e) . "\n");
    exit(1);
}

/**
 * Validate required environment variables
 *
 * @throws RuntimeException
 */
function validateEnvironment(): void
{
    if (empty(PLEX_URL)) {
        throw new RuntimeException('Missing required environment variable: PLEX_URL');
    }

    if (empty(PLEX_TOKEN)) {
        throw new RuntimeException('Missing required environment variable: PLEX_TOKEN');
    }

    if (empty(TMDB_API_KEY)) {
        throw new RuntimeException('Missing required environment variable: TMDB_API_KEY');
    }
}

/**
 * Parse ignored libraries from environment variable
 */
function parseIgnoredLibraries(?string $ignoredLibrariesEnv): array
{
    $defaultIgnored = ['Tunarr'];

    if (empty($ignoredLibrariesEnv)) {
        return $defaultIgnored;
    }

    $parsed = array_map('trim', explode(',', $ignoredLibrariesEnv));

    return array_merge($defaultIgnored, $parsed);
}
