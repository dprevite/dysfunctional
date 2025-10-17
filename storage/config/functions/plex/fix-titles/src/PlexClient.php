<?php

namespace PlexQuality;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

class PlexClient
{
    private Client $client;
    private string $baseUrl;
    private string $token;

    public function __construct(string $baseUrl, string $token)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->token = $token;

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'X-Plex-Token' => $this->token,
                'Accept' => 'application/json',
            ],
            'verify' => true,
        ]);
    }

    /**
     * Make a request to the Plex API
     *
     * @throws RuntimeException
     */
    private function makeRequest(string $endpoint, array $params = []): array
    {
        try {
            $response = $this->client->get($endpoint, [
                'query' => $params,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if ($data === null) {
                throw new RuntimeException("Failed to decode JSON response from {$endpoint}");
            }

            return $data;
        } catch (GuzzleException $e) {
            throw new RuntimeException("Failed to request {$endpoint}: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get all library sections from the Plex server
     *
     * @throws RuntimeException
     */
    public function getLibrarySections(): array
    {
        $data = $this->makeRequest('/library/sections');

        return $data['MediaContainer']['Directory'] ?? [];
    }

    /**
     * Get all items from a specific library section
     *
     * @throws RuntimeException
     */
    public function getSectionItems(string $sectionId): array
    {
        $data = $this->makeRequest("/library/sections/{$sectionId}/all");

        return $data['MediaContainer']['Metadata'] ?? [];
    }

    /**
     * Get detailed metadata for a specific item
     *
     * @throws RuntimeException
     */
    public function getItemMetadata(string $ratingKey): array
    {
        $data = $this->makeRequest("/library/metadata/{$ratingKey}");

        $metadata = $data['MediaContainer']['Metadata'] ?? [];

        if (empty($metadata)) {
            throw new RuntimeException("No metadata found for rating key {$ratingKey}");
        }

        return $metadata[0];
    }

    /**
     * Extract TMDB ID from item's Guid array
     */
    public function extractTMDBId(array $item): ?int
    {
        $guids = $item['Guid'] ?? [];

        foreach ($guids as $guid) {
            $id = $guid['id'] ?? '';

            if (str_starts_with($id, 'tmdb://')) {
                $tmdbId = substr($id, 7);
                return (int) $tmdbId;
            }
        }

        return null;
    }

    /**
     * Find quality indicators in a title and return them
     */
    public function findQualityInTitle(string $title): array
    {
        $patterns = [
            '/\b(1080p|2160p|720p|480p|4K|8K|UHD)\b/i',
            '/\b(WEBRip|WebRip|WEB-Rip|WEB)\b/i',
            '/\b(BluRay|Blu-Ray|BDRip|BD-Rip|BRRip)\b/i',
            '/\b(DVDRip|DVD-Rip|DVDR)\b/i',
            '/\b(HEVC|H\.265|x265|H265)\b/i',
            '/\b(x264|H\.264|H264)\b/i',
            '/\b(HDRip|HD-Rip)\b/i',
            '/\b(CAM|CAMRip|HDCAM)\b/i',
            '/\b(REMUX|Remux)\b/i',
        ];

        $matches = [];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $title, $found)) {
                $matches = array_merge($matches, $found[1]);
            }
        }

        return array_unique($matches);
    }
}
