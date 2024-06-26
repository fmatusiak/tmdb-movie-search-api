<?php

namespace App\Services;

use App\Exceptions\TMDBApiException;
use App\TMDBApiLanguage;

class TMDBApi
{
    private string $baseUrl = 'https://api.themoviedb.org/3';
    private TMDBClient $client;
    private string $language;

    public function __construct(TMDBClient $client, TMDBApiLanguage $TMDBApiLanguage = TMDBApiLanguage::English)
    {
        $this->client = $client;
        $this->setLanguage($TMDBApiLanguage);
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(TMDBApiLanguage $TMDBApiLanguage): void
    {
        $languageValue = $TMDBApiLanguage->value;

        TMDBApiLanguage::isValid($languageValue);

        $this->language = $languageValue;
    }

    /**
     * @throws TMDBApiException
     */
    public function movieGenres(): array
    {
        $movieGenresUrl = $this->baseUrl . '/genre/movie/list';

        $queryParams = $this->client->prepareQueryParams($this->language);

        return $this->client->getRequest($movieGenresUrl, $queryParams);
    }

    /**
     * @throws TMDBApiException
     */
    public function seriesGenres(): array
    {
        $seriesGenresUrl = $this->baseUrl . '/genre/tv/list';

        $queryParams = $this->client->prepareQueryParams($this->language);

        return $this->client->getRequest($seriesGenresUrl, $queryParams);
    }

    /**
     * @throws TMDBApiException
     */
    public function topRatedMovies(int $page = 1): array
    {
        $popularMoviesUrl = $this->baseUrl . '/movie/top_rated';

        $queryParams = $this->client->prepareQueryParams($this->language, $page);

        return $this->client->getRequest($popularMoviesUrl, $queryParams);
    }

    /**
     * @throws TMDBApiException
     */
    public function topRatedSeries(int $page = 1): array
    {
        $popularSeriesUrl = $this->baseUrl . '/tv/top_rated';

        $queryParams = $this->client->prepareQueryParams($this->language, $page);

        return $this->client->getRequest($popularSeriesUrl, $queryParams);
    }

    /**
     * @throws TMDBApiException
     */
    public function movieDetails(int $movieId): array
    {
        $movieDetailUrl = $this->baseUrl . '/movie/' . $movieId;

        $queryParams = $this->client->prepareQueryParams($this->language);

        return $this->client->getRequest($movieDetailUrl, $queryParams);
    }

    /**
     * @throws TMDBApiException
     */
    public function serieDetails(int $serieId): array
    {
        $seriesDetailUrl = $this->baseUrl . '/tv/' . $serieId;

        $queryParams = $this->client->prepareQueryParams($this->language);

        return $this->client->getRequest($seriesDetailUrl, $queryParams);
    }
}
