<?php

namespace PlexQuality;

use RuntimeException;

class TitleFixer
{
    private PlexClient $plexClient;
    private TMDBClient $tmdbClient;
    private array $ignoredLibraries;

    public function __construct(
        PlexClient $plexClient,
        TMDBClient $tmdbClient,
        array $ignoredLibraries = ['Tunarr']
    ) {
        $this->plexClient = $plexClient;
        $this->tmdbClient = $tmdbClient;
        $this->ignoredLibraries = $ignoredLibraries;
    }

    /**
     * Find all media items with quality indicators in titles
     *
     * @throws RuntimeException
     */
    public function findItemsWithQualityIndicators(): array
    {
        $sections = $this->plexClient->getLibrarySections();
        $results = [];

        foreach ($sections as $section) {
            if (!$this->shouldProcessSection($section)) {
                continue;
            }

            $sectionResults = $this->processSectionItems($section);
            $results = array_merge($results, $sectionResults);
        }

        return $results;
    }

    /**
     * Determine if a section should be processed
     */
    private function shouldProcessSection(array $section): bool
    {
        $sectionTitle = $section['title'] ?? '';
        $sectionType = $section['type'] ?? 'unknown';

        if (in_array($sectionTitle, $this->ignoredLibraries)) {
            return false;
        }

        return in_array($sectionType, ['movie', 'show']);
    }

    /**
     * Process all items in a section
     *
     * @throws RuntimeException
     */
    private function processSectionItems(array $section): array
    {
        $sectionId = $section['key'];
        $sectionTitle = $section['title'] ?? 'Unknown';
        $sectionType = $section['type'];

        $items = $this->plexClient->getSectionItems($sectionId);
        $results = [];

        foreach ($items as $item) {
            $result = $this->processItem($item, $sectionId, $sectionTitle, $sectionType);

            if ($result !== null) {
                $results[] = $result;
            }
        }

        return $results;
    }

    /**
     * Process a single item and return result if it has quality indicators
     *
     * @throws RuntimeException
     */
    private function processItem(array $item, string $sectionId, string $sectionTitle, string $sectionType): ?array
    {
        $title = $item['title'] ?? '';
        $qualityIndicators = $this->plexClient->findQualityInTitle($title);

        if (empty($qualityIndicators)) {
            return null;
        }

        $ratingKey = $item['ratingKey'] ?? null;

        if ($ratingKey === null) {
            return null;
        }

        return $this->buildItemResult($item, $ratingKey, $sectionId, $sectionTitle, $sectionType, $qualityIndicators);
    }

    /**
     * Build the result array for an item
     *
     * @throws RuntimeException
     */
    private function buildItemResult(
        array $item,
        string $ratingKey,
        string $sectionId,
        string $sectionTitle,
        string $sectionType,
        array $qualityIndicators
    ): array {
        $metadata = $this->plexClient->getItemMetadata($ratingKey);
        $tmdbId = $this->plexClient->extractTMDBId($metadata);

        return [
            'plex_id' => $ratingKey,
            'plex_library_id' => $sectionId,
            'plex_section_name' => $sectionTitle,
            'section_type' => $sectionType,
            'media_type' => $item['type'] ?? 'unknown',
            'current_title' => $item['title'] ?? '',
            'correct_title' => $this->fetchCorrectTitle($tmdbId, $sectionType),
            'year' => $item['year'] ?? null,
            'quality_indicators' => array_values($qualityIndicators),
            'tmdb_id' => $tmdbId,
        ];
    }

    /**
     * Fetch the correct title from TMDB
     *
     * @throws RuntimeException
     */
    private function fetchCorrectTitle(?int $tmdbId, string $sectionType): ?string
    {
        if ($tmdbId === null) {
            return null;
        }

        if ($sectionType === 'movie') {
            return $this->tmdbClient->getMovieTitle($tmdbId);
        }

        if ($sectionType === 'show') {
            return $this->tmdbClient->getTVShowTitle($tmdbId);
        }

        return null;
    }
}
