<?php

namespace App\Services;

use App\Exceptions\TMDBApiException;
use App\TMDBApiLanguage;
use Exception;

class GenreService
{
    private TMDBApiService $TMDBApiService;

    public function __construct(TMDBApiService $TMDBApiService)
    {
        $this->TMDBApiService = $TMDBApiService;
    }

    /**
     * @throws TMDBApiException
     * @throws Exception
     */
    public function fetchGenresFromTMDBApi(TMDBApiLanguage $apiLanguage): array
    {
        return $this->TMDBApiService->fetchGenres($apiLanguage);
    }
}
