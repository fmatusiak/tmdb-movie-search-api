<?php

namespace App\Services;

use App\Exceptions\TMDBApiException;
use App\Repositories\MovieRepository;
use App\TMDBApiLanguage;
use Mockery\Exception;

class MovieService extends TMDBContent
{
    private TMDBApiService $TMDBApiService;

    public function __construct(TMDBApiService $TMDBApiService, MovieRepository $movieRepository)
    {
        parent::__construct($movieRepository);
        $this->TMDBApiService = $TMDBApiService;
    }

    /**
     * @throws TMDBApiException
     */
    public function fetchTopRatedMoviesFromTMDBApi(TMDBApiLanguage $apiLanguage, int $totalMoviesToFetch = 50): array
    {
        try {
            return $this->TMDBApiService->fetchContent('topRatedMovies', $apiLanguage, $totalMoviesToFetch);
        } catch (TMDBApiException $e) {
            throw new TMDBApiException('TMDB API error fetch movies', $e->getCode(), $e);
        } catch (Exception $e) {
            throw new Exception('An unexpected error occurred while fetch movies', $e->getCode(), $e);
        }
    }

    public function createOrUpdateTMDBData(array $movieData, string $language): void
    {
        try {
            $this->createOrUpdateTMDB($movieData, $language);
        } catch (Exception $e) {
            throw new Exception('An error occurred while creating or updating movie from TMDB data', $e->getCode(), $e);
        }
    }

    public function fetchAndSaveTranslations(array $movieData, array $languages): void
    {
        try {
            $translations = $this->TMDBApiService->fetchTranslations('movieDetails', $movieData['id'], $languages);

            $this->saveTranslations($movieData, $translations);
        } catch (Exception $e) {
            throw new Exception('An unexpected error occurred while fetch and save translations for movie', $e->getCode(), $e);
        }
    }
}
