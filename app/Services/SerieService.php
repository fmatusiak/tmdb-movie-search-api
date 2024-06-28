<?php

namespace App\Services;

use App\Exceptions\TMDBApiException;
use App\Models\Serie;
use App\Repositories\SerieRepository;
use App\TMDBApiLanguage;
use Exception;

class SerieService
{
    private SerieRepository $serieRepository;
    private TMDBApiService $TMDBApiService;

    public function __construct(SerieRepository $serieRepository, TMDBApiService $TMDBApiService)
    {
        $this->serieRepository = $serieRepository;
        $this->TMDBApiService = $TMDBApiService;
    }

    /**
     * @throws TMDBApiException
     * @throws Exception
     */
    public function fetchTopRatedFromTMDBApi(TMDBApiLanguage $apiLanguage, int $totalMoviesToFetch = 50): array
    {
        try {
            return $this->TMDBApiService->fetchContent('topRatedSeries', $apiLanguage, $totalMoviesToFetch);
        } catch (TMDBApiException $e) {
            throw new TMDBApiException('Error fetch top rated series', 0, $e);
        } catch (Exception $e) {
            throw new Exception('An unexpected error occurred while fetch series', 0, $e);
        }
    }

    /**
     * @throws Exception
     */
    public function createOrUpdateTMDBData(array $TMDBSerieData, string $language): Serie
    {
        return $this->serieRepository->createOrUpdateTMDBSerie($TMDBSerieData, $language);
    }

    /**
     * @throws Exception
     */
    public function fetchAndSaveTMDBTranslations(array $TMDBSerieData, array $languages): void
    {
        try {
            $translations = $this->TMDBApiService->fetchTranslations('serieDetails', $TMDBSerieData['id'], $languages);
            $this->serieRepository->saveTMDBSerieTranslations($TMDBSerieData, $translations);
        } catch (TMDBApiException $e) {
            throw new TMDBApiException('Error fetch translations for serie', 0, $e);
        } catch (Exception $e) {
            throw new Exception('An unexpected error occurred while fetch translations for serie', 0, $e);
        }
    }
}
