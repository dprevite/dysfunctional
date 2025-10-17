<?php

namespace PlexQuality;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use RuntimeException;

class TMDBClient
{
    private Client $client;
    private string $accessToken;
    private string $cacheDir;
    private LoggerInterface $logger;

    public function __construct(string $accessToken, string $cacheDir = null)
    {
        $this->accessToken = $accessToken;
        $this->cacheDir = $cacheDir ?? __DIR__ . '/../storage/cache/tmdb';
        $this->logger = Logger::getInstance();

        $this->logger->debug('TMDBClient initialized', [
            'cache_dir' => $this->cacheDir,
            '__DIR__' => __DIR__,
            'resolved_path' => realpath($this->cacheDir) ?: 'NOT RESOLVED',
        ]);

        $this->ensureCacheDirectoryExists();

        $this->client = new Client([
            'base_uri' => 'https://api.themoviedb.org/3/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Accept' => 'application/json',
            ],
            'verify' => true,
        ]);
    }

    /**
     * Ensure cache directory exists
     */
    private function ensureCacheDirectoryExists(): void
    {
        if (!is_dir($this->cacheDir)) {
            $this->logger->debug('Creating cache directory', ['path' => $this->cacheDir]);

            $result = @mkdir($this->cacheDir, 0755, true);

            if (!$result) {
                $error = error_get_last();
                $this->logger->warning('Failed to create cache directory', [
                    'path' => $this->cacheDir,
                    'error' => $error['message'] ?? 'Unknown error',
                ]);
            } else {
                $this->logger->info('Cache directory created successfully');
            }
        } else {
            $this->logger->debug('Cache directory already exists');
        }
    }

    /**
     * Get cache file path for a movie
     */
    private function getMovieCacheFile(int $movieId): string
    {
        return $this->cacheDir . "/movie_{$movieId}.json";
    }

    /**
     * Get cache file path for a TV show
     */
    private function getTVShowCacheFile(int $showId): string
    {
        return $this->cacheDir . "/tv_{$showId}.json";
    }

    /**
     * Read from cache file
     */
    private function readFromCache(string $cacheFile): ?array
    {
        if (!file_exists($cacheFile)) {
            return null;
        }

        $content = file_get_contents($cacheFile);

        if ($content === false) {
            return null;
        }

        $data = json_decode($content, true);

        return $data ?: null;
    }

    /**
     * Write to cache file
     */
    private function writeToCache(string $cacheFile, array $data): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $result = @file_put_contents($cacheFile, $json);

        if ($result === false) {
            $error = error_get_last();
            $this->logger->warning('Failed to write cache file', [
                'file' => $cacheFile,
                'error' => $error['message'] ?? 'Unknown error',
                'cache_dir' => $this->cacheDir,
                'dir_exists' => is_dir($this->cacheDir),
                'dir_writable' => is_writable($this->cacheDir),
            ]);
        } else {
            $this->logger->info('Cache file written successfully', [
                'file' => $cacheFile,
                'bytes' => $result,
            ]);
        }
    }

    /**
     * Get movie details from TMDB by movie ID
     *
     * @throws RuntimeException
     */
    public function getMovieDetails(int $movieId): array
    {
        $cacheFile = $this->getMovieCacheFile($movieId);
        $cached = $this->readFromCache($cacheFile);

        if ($cached !== null) {
            $this->logger->debug('Using cached data for movie', [
                'movie_id' => $movieId,
                'cache_file' => $cacheFile,
            ]);
            return $cached;
        }

        $this->logger->info('Fetching movie from TMDB API (cache miss)', ['movie_id' => $movieId]);
        $data = $this->fetchMovieFromAPI($movieId);
        $this->writeToCache($cacheFile, $data);

        return $data;
    }

    /**
     * Fetch movie details from TMDB API
     *
     * @throws RuntimeException
     */
    private function fetchMovieFromAPI(int $movieId): array
    {
        try {
            $response = $this->client->get("movie/{$movieId}");

            $data = json_decode($response->getBody()->getContents(), true);

            if ($data === null) {
                throw new RuntimeException("Failed to decode JSON response for movie {$movieId}");
            }

            return $data;
        } catch (GuzzleException $e) {
            $this->logger->error('Failed to fetch movie from TMDB', [
                'movie_id' => $movieId,
                'error' => $e->getMessage(),
            ]);
            throw new RuntimeException("Failed to fetch movie {$movieId} from TMDB: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get TV show details from TMDB by show ID
     *
     * @throws RuntimeException
     */
    public function getTVShowDetails(int $showId): array
    {
        $cacheFile = $this->getTVShowCacheFile($showId);
        $cached = $this->readFromCache($cacheFile);

        if ($cached !== null) {
            $this->logger->debug('Using cached data for TV show', [
                'show_id' => $showId,
                'cache_file' => $cacheFile,
            ]);
            return $cached;
        }

        $this->logger->info('Fetching TV show from TMDB API (cache miss)', ['show_id' => $showId]);
        $data = $this->fetchTVShowFromAPI($showId);
        $this->writeToCache($cacheFile, $data);

        return $data;
    }

    /**
     * Fetch TV show details from TMDB API
     *
     * @throws RuntimeException
     */
    private function fetchTVShowFromAPI(int $showId): array
    {
        try {
            $response = $this->client->get("tv/{$showId}");

            $data = json_decode($response->getBody()->getContents(), true);

            if ($data === null) {
                throw new RuntimeException("Failed to decode JSON response for TV show {$showId}");
            }

            return $data;
        } catch (GuzzleException $e) {
            $this->logger->error('Failed to fetch TV show from TMDB', [
                'show_id' => $showId,
                'error' => $e->getMessage(),
            ]);
            throw new RuntimeException("Failed to fetch TV show {$showId} from TMDB: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Extract the title from TMDB movie details
     *
     * @throws RuntimeException
     */
    public function getMovieTitle(int $movieId): string
    {
        $details = $this->getMovieDetails($movieId);

        if (!isset($details['title'])) {
            throw new RuntimeException("Movie {$movieId} has no title field");
        }

        return $details['title'];
    }

    /**
     * Extract the title from TMDB TV show details
     *
     * @throws RuntimeException
     */
    public function getTVShowTitle(int $showId): string
    {
        $details = $this->getTVShowDetails($showId);

        if (!isset($details['name'])) {
            throw new RuntimeException("TV show {$showId} has no name field");
        }

        return $details['name'];
    }
}
