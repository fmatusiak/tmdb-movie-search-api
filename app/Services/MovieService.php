<?php

namespace App\Services;

use App\Exceptions\TMDBApiException;
use App\Models\Movie;
use App\Repositories\MovieRepository;
use App\TMDBApiLanguage;
use Exception;

class MovieService
{
    private MovieRepository $movieRepository;
    private TMDBApiService $TMDBApiService;

    public function __construct(TMDBApiService $TMDBApiService, MovieRepository $movieRepository)
    {
        $this->movieRepository = $movieRepository;
        $this->TMDBApiService = $TMDBApiService;
    }

    /**
     * @throws TMDBApiException
     * @throws Exception
     */
    public function fetchTopRatedMoviesFromTMDBApi(TMDBApiLanguage $apiLanguage, int $totalMoviesToFetch = 50): array
    {
        try {
            return $this->TMDBApiService->fetchContent('topRatedMovies', $apiLanguage, $totalMoviesToFetch);
        } catch (TMDBApiException $e) {
            throw new TMDBApiException('Error fetch top rated movies', 0, $e);
        } catch (Exception $e) {
            throw new Exception('An unexpected error occurred while fetch top rated movies', 0, $e);
        }
    }

    /**
     * @throws Exception
     */
    public function createOrUpdateTMDBData(array $TMDBMovieData, string $language): Movie
    {
        return $this->movieRepository->createOrUpdateTMDBMovie($TMDBMovieData, $language);
    }

    /**
     * @throws Exception
     */
    public function fetchAndSaveTMDBTranslations(array $TMDBMovieData, array $languages): void
    {
        try {
            $translations = $this->TMDBApiService->fetchTranslations('movieDetails', $TMDBMovieData['id'], $languages);
            $this->movieRepository->saveTMDBMovieTranslations($TMDBMovieData, $translations);
        } catch (TMDBApiException $e) {
            throw new TMDBApiException('Error fetch translations for movie', 0, $e);
        } catch (Exception $e) {
            throw new Exception('An unexpected error occurred while fetching and saving TMDB movie translations', 0, $e);
        }
    }
}
