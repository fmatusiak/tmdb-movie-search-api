<?php

namespace App\Services;

use App\Exceptions\TMDBApiException;
use App\TMDBApiLanguage;
use Mockery\Exception;

class GenreService
{
    private TMDBApiService $TMDBApiService;

    public function __construct(TMDBApiService $TMDBApiService)
    {
        $this->TMDBApiService = $TMDBApiService;
    }

    /**
     * @throws TMDBApiException
     */
    public function fetchGenresFromTMDBApi(TMDBApiLanguage $apiLanguage): array
    {
        try {
            return $this->TMDBApiService->fetchGenres($apiLanguage);
        } catch (TMDBApiException $e) {
            throw new TMDBApiException('TMDB API error fetch genres', $e->getCode(), $e);
        } catch (Exception $e) {
            throw new Exception('An unexpected error occurred while fetch genres', $e->getCode(), $e);
        }
    }
}
