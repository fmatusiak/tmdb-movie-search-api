<?php

namespace App\Repositories;

use App\Models\Movie;
use Exception;
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
