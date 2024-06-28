<?php

namespace App\Repositories;

use App\Models\Serie;
use Exception;
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
            $serie->release_date = Arr::get($TMDBSerieData, 'release_date');

            if ($name = Arr::get($TMDBSerieData, 'title')) {
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
                if ($title = Arr::get($value, 'title')) {
                    $serie->setTranslation('title', $language, $title);
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
