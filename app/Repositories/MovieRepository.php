<?php

namespace App\Repositories;

use App\Models\Movie;
use Carbon\Carbon;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class MovieRepository extends Repository
{
    private GenreRepository $genreRepository;

    public function __construct(Movie $movie, GenreRepository $genreRepository)
    {
        parent::__construct($movie);

        $this->genreRepository = $genreRepository;
    }

    public function paginate(int $perPage = 15, array $filters = [], array $columns = ['*'], array $languages = []): LengthAwarePaginator
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

        if ($fromReleaseDate = Arr::get($filters, 'from_release_date')) {
            $fromReleaseDate = Carbon::parse($fromReleaseDate)->format('Y-m-d');

            $query = $query->whereDate('release_date', '>=', $fromReleaseDate);
        }

        if ($toReleaseDate = Arr::get($filters, 'to_release_date')) {
            $toReleaseDate = Carbon::parse($toReleaseDate)->format('Y-m-d');

            $query = $query->whereDate('release_date', '<=', $toReleaseDate);
        }

        $pagination = $query->paginate($perPage, $columns);

        $items = $pagination->getCollection()->map(function ($movie) use ($languages) {
            return $movie->translate($languages);
        });

        $pagination->setCollection($items);

        return $pagination;
    }

    /**
     * @throws Exception
     */
    public function createOrUpdateTMDBMovie(array $TMDBMovieData, string $defaultLanguage): Movie
    {
        try {
            DB::beginTransaction();

            $movie = $this->firstOrCreate([
                'external_id' => $TMDBMovieData['id']
            ]);

            $movie->vote_average = Arr::get($TMDBMovieData, 'vote_average', 0);
            $movie->vote_count = Arr::get($TMDBMovieData, 'vote_count', 0);
            $movie->popularity = Arr::get($TMDBMovieData, 'popularity', 0);
            $movie->release_date = Arr::get($TMDBMovieData, 'release_date');

            if ($title = Arr::get($TMDBMovieData, 'title')) {
                $movie->setTranslation('title', $defaultLanguage, $title);
            }

            if ($overview = Arr::get($TMDBMovieData, 'overview')) {
                $movie->setTranslation('overview', $defaultLanguage, $overview);
            }

            $genreExternalIds = Arr::get($TMDBMovieData, 'genre_ids', []);
            $genreIds = $this->genreRepository->getGenreIdsByExternalIds($genreExternalIds);

            $movie->save();

            $movie->genres()->sync($genreIds);

            DB::commit();

            return $movie;
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception('An error occurred while creating or updating movie from TMDB data', 0, $e);
        }
    }

    /**
     * @throws Exception
     */
    public function saveTMDBMovieTranslations(array $TMDBMovieData, array $translations): void
    {
        try {
            $movie = $this->whereFirst([
                'external_id' => $TMDBMovieData['id']
            ]);

            if (!$movie) {
                return;
            }

            foreach ($translations as $language => $value) {
                if ($title = Arr::get($value, 'title')) {
                    $movie->setTranslation('title', $language, $title);
                }

                if ($overview = Arr::get($value, 'overview')) {
                    $movie->setTranslation('overview', $language, $overview);
                }
            }

            $movie->save();
        } catch (Exception $e) {
            throw new Exception('An unexpected error occurred while fetch and save translations for movie', 0, $e);
        }
    }
}
