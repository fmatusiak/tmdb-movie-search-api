<?php

namespace App\Repositories;

use App\Models\Serie;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SerieRepository extends Repository
{
    private GenreRepository $genreRepository;

    public function __construct(Serie $serie, GenreRepository $genreRepository)
    {
        parent::__construct($serie);
        $this->genreRepository = $genreRepository;
    }

    public function paginate(
        int    $perPage = 15,
        array  $filters = [],
        array  $columns = ['*'],
        array  $languages = [],
        string $sortBy = 'title',
        string $sortDirection = 'asc'): LengthAwarePaginator
    {
        $query = $this->model::query();

        if ($title = Arr::get($filters, 'title')) {
            $query = $query->where(function ($query) use ($title, $languages) {
                foreach ($languages as $language) {
                    $query->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$language}')) LIKE ?", ["%{$title}%"]);
                }
            });
        }

        if ($externalIds = Arr::get($filters, 'external_id')) {
            $externalIds = explode(',', $externalIds);

            $query = $query->whereIn('external_id', $externalIds);
        }

        if ($genreIds = Arr::get($filters, 'genre_id')) {
            $genreIds = explode(',', $genreIds);

            $query = $query->whereHas('genres', function ($query) use ($genreIds) {
                $query->whereIn('genres.id', $genreIds);
            });
        }

        if ($fromVoteAverage = Arr::get($filters, 'from_vote_average')) {
            $query = $query->where('vote_average', '>=', $fromVoteAverage);
        }

        if ($toVoteAverage = Arr::get($filters, 'to_vote_average')) {
            $query = $query->where('vote_average', '<=', $toVoteAverage);
        }

        if ($fromVoteCount = Arr::get($filters, 'from_vote_count')) {
            $query = $query->where('vote_count', '>=', $fromVoteCount);
        }

        if ($toVoteCount = Arr::get($filters, 'to_vote_count')) {
            $query = $query->where('vote_count', '<=', $toVoteCount);
        }

        if ($fromPopularity = Arr::get($filters, 'from_popularity')) {
            $query = $query->where('popularity', '>=', $fromPopularity);
        }

        if ($toPopularity = Arr::get($filters, 'to_popularity')) {
            $query = $query->where('popularity', '<=', $toPopularity);
        }

        if ($sortBy === 'title' && in_array($sortDirection, ['asc', 'desc'])) {
            if (isset($languages[0])) {
                $query = $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$languages[0]}')) {$sortDirection}");
            }
        } elseif (in_array($sortDirection, ['asc', 'desc'])) {
            $query = $query->orderBy($sortBy, $sortDirection);
        }

        $pagination = $query->paginate($perPage, $columns);

        $items = $pagination->getCollection()->map(function ($serie) use ($languages) {
            return $serie->translate($languages);
        });

        $pagination->setCollection($items);

        return $pagination;
    }

    /**
     * @throws Exception
     */
    public function createOrUpdateTMDBSerie(array $TMDBSerieData, string $defaultLanguage): Serie
    {
        try {
            DB::beginTransaction();

            $serie = $this->firstOrCreate([
                'external_id' => $TMDBSerieData['id']
            ]);

            $serie->vote_average = Arr::get($TMDBSerieData, 'vote_average', 0);
            $serie->vote_count = Arr::get($TMDBSerieData, 'vote_count', 0);
            $serie->popularity = Arr::get($TMDBSerieData, 'popularity', 0);

            if ($name = Arr::get($TMDBSerieData, 'name')) {
                $serie->setTranslation('title', $defaultLanguage, $name);
            }

            if ($overview = Arr::get($TMDBSerieData, 'overview')) {
                $serie->setTranslation('overview', $defaultLanguage, $overview);
            }

            $genreExternalIds = Arr::get($TMDBSerieData, 'genre_ids', []);
            $genreIds = $this->genreRepository->getGenreIdsByExternalIds($genreExternalIds);

            $serie->save();

            $serie->genres()->sync($genreIds);

            DB::commit();

            return $serie;
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception('An error occurred while creating or updating serie from TMDB data', 0, $e);
        }
    }

    /**
     * @throws Exception
     */
    public function saveTMDBSerieTranslations(array $TMDBSerieData, array $translations): void
    {
        try {
            $serie = $this->whereFirst([
                'external_id' => $TMDBSerieData['id']
            ]);

            if (!$serie) {
                return;
            }

            foreach ($translations as $language => $value) {
                if ($name = Arr::get($value, 'name')) {
                    $serie->setTranslation('title', $language, $name);
                }

                if ($overview = Arr::get($value, 'overview')) {
                    $serie->setTranslation('overview', $language, $overview);
                }
            }

            $serie->save();
        } catch (Exception $e) {
            throw new Exception('An unexpected error occurred while fetch and save translations for serie', 0, $e);
        }
    }
}
