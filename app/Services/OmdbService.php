<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class OmdbService
{
    /**
     * Guzzle HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * OMDb API key.
     *
     * @var string
     */
    protected $apiKey;

    /**
     * OMDb API base URL.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Create a new OmdbService instance.
     */
    public function __construct()
    {
        $this->apiKey  = config('services.omdb.api_key');
        $this->baseUrl = config('services.omdb.base_url');
        $this->client  = new Client([
            'base_uri' => $this->baseUrl,
            'timeout'  => 10.0,
        ]);
    }

    /**
     * Search movies by query string with optional filters.
     *
     * @param  string       $query   Movie title to search for
     * @param  string|null  $type    Filter by type: movie, series, episode
     * @param  string|null  $year    Filter by year
     * @param  int          $page    Page number for pagination
     * @return array
     */
    public function searchMovies(string $query, ?string $type = null, ?string $year = null, int $page = 1): array
    {
        try {
            $params = [
                'query' => [
                    'apikey' => $this->apiKey,
                    's'      => $query,
                    'page'   => $page,
                ],
            ];

            if ($type) {
                $params['query']['type'] = $type;
            }

            if ($year) {
                $params['query']['y'] = $year;
            }

            $response = $this->client->get('/', $params);
            $data     = json_decode($response->getBody()->getContents(), true);

            if (isset($data['Response']) && $data['Response'] === 'True') {
                return [
                    'success'      => true,
                    'movies'       => $data['Search'],
                    'totalResults' => (int) $data['totalResults'],
                ];
            }

            return [
                'success' => false,
                'message' => $data['Error'] ?? __('movies.error_not_found'),
                'movies'  => [],
            ];
        } catch (RequestException $e) {
            return [
                'success' => false,
                'message' => __('movies.error_network'),
                'movies'  => [],
            ];
        }
    }

    /**
     * Get a single movie's full details by IMDb ID.
     *
     * @param  string  $imdbId  IMDb movie identifier (e.g. tt3896198)
     * @return array
     */
    public function getMovieDetail(string $imdbId): array
    {
        try {
            $response = $this->client->get('/', [
                'query' => [
                    'apikey' => $this->apiKey,
                    'i'      => $imdbId,
                    'plot'   => 'full',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['Response']) && $data['Response'] === 'True') {
                return [
                    'success' => true,
                    'movie'   => $data,
                ];
            }

            return [
                'success' => false,
                'message' => $data['Error'] ?? __('movies.error_not_found'),
            ];
        } catch (RequestException $e) {
            return [
                'success' => false,
                'message' => __('movies.error_network'),
            ];
        }
    }
}
