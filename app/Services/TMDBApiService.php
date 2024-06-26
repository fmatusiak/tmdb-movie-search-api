<?php

namespace App\Services;

use App\Exceptions\TMDBApiException;
use App\TMDBApiLanguage;
use Exception;
use Illuminate\Support\Arr;

class TMDBApiService
{
    private TMDBApi $TMDBApi;

    public function __construct(TMDBApi $TMDBApi)
    {
        $this->TMDBApi = $TMDBApi;
    }

    /**
     * @throws TMDBApiException
     * @throws Exception
     */
    public function fetchContent(string $apiMethod, TMDBApiLanguage $apiLanguage, int $totalToFetch = 50): array
    {
        $this->TMDBApi->setLanguage($apiLanguage);

        $countFetch = 0;
        $page = 1;
        $allResults = [];

        while ($countFetch < $totalToFetch) {
            $responseData = $this->TMDBApi->{$apiMethod}($page);

            if (!isset($responseData['results'])) {
                throw new TMDBApiException('No results content found');
            }

            $results = $responseData['results'];

            foreach ($results as $result) {
                if (isset($result['name']) || isset($result['title'])) {
                    $allResults[] = $result;

                    $countFetch++;

                    if ($countFetch >= $totalToFetch) {
                        break;
                    }
                }
            }

            $totalPages = $responseData['total_pages'];

            if ($page >= $totalPages) {
                throw new TMDBApiException('No more pages available for content');
            }

            $page++;
        }

        return $allResults;
    }

    /**
     * @throws TMDBApiException
     * @throws Exception
     */
    public function fetchGenres(TMDBApiLanguage $apiLanguage): array
    {
        try {
            $this->TMDBApi->setLanguage($apiLanguage);

            $moviesGenres = Arr::get($this->TMDBApi->movieGenres(), 'genres');
            $seriesGenres = Arr::get($this->TMDBApi->seriesGenres(), 'genres');

            return collect($moviesGenres)
                ->merge($seriesGenres)
                ->unique('id')
                ->all();
        } catch (TMDBApiException $e) {
            throw new TMDBApiException('Error fetch genres', 0, $e);
        } catch (Exception $e) {
            throw new Exception('An unexpected error occurred while fetch genres', 0, $e);
        }
    }

    /**
     * @throws Exception
     */
    public function fetchTranslations(string $method, int $externalId, array $languages): array
    {
        try {
            $translations = [];

            foreach ($languages as $language) {
                $this->TMDBApi->setLanguage($language);
                $currentLanguage = $this->TMDBApi->getLanguage();

                $translations[$currentLanguage] = $this->TMDBApi->{$method}($externalId);
            }

            return $translations;
        } catch (TMDBApiException $e) {
            throw new TMDBApiException('Error fetch translations', 0, $e);
        } catch (Exception $e) {
            throw new Exception('An unexpected error occurred while fetching translations', 0, $e);
        }
    }
}
