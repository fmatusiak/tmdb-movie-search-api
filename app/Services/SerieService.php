<?php

namespace App\Services;

use App\Exceptions\TMDBApiException;
use App\Repositories\SerieRepository;
use App\TMDBApiLanguage;
use Mockery\Exception;

class SerieService extends TMDBContent
{
    private TMDBApiService $TMDBApiService;

    public function __construct(SerieRepository $serieRepository, TMDBApiService $TMDBApiService)
    {
        parent::__construct($serieRepository);
        $this->TMDBApiService = $TMDBApiService;
    }

    /**
     * @throws TMDBApiException
     */
    public function fetchTopRatedFromTMDBApi(TMDBApiLanguage $apiLanguage, int $totalMoviesToFetch = 50): array
    {
        try {
            return $this->TMDBApiService->fetchContent('topRatedSeries', $apiLanguage, $totalMoviesToFetch);
        } catch (TMDBApiException $e) {
            throw new TMDBApiException('TMDB API error fetch series', $e->getCode(), $e);
        } catch (Exception $e) {
            throw new Exception('An unexpected error occurred while fetch series', $e->getCode(), $e);
        }
    }

    /**
     * @throws TMDBApiException
     */
    public function fetchAndSaveTranslations(array $movieData, array $languages): void
    {
        try {
            $translations = $this->TMDBApiService->fetchTranslations('serieDetails', $movieData['id'], $languages);

            $this->saveTranslations($movieData, $translations);
        } catch (Exception $e) {
            throw new Exception('An unexpected error occurred while fetch translations for serie', $e->getCode(), $e);
        }
    }
}
