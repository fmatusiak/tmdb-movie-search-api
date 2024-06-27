<?php

namespace App\Repositories;

use App\Models\Genre;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class GenreRepository extends Repository
{
    public function __construct(Genre $genre)
    {
        parent::__construct($genre);
    }

    public function paginate(int $perPage = 15, array $filters = [], array $columns = ['*'], array $languages = []): LengthAwarePaginator
    {
        $query = $this->model::query();

        if ($name = Arr::get($filters, 'name')) {
            $query = $query->where(function ($query) use ($name, $languages) {
                foreach ($languages as $language) {
                    $query->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$language}')) LIKE ?", ["%{$name}%"]);
                }
            });
        }

        if ($externalId = Arr::get($filters, 'external_id')) {
            $query = $query->where('external_id', $externalId);
        }

        $pagination = $query->paginate($perPage, $columns);

        $items = $pagination->getCollection()->map(function ($genre) use ($languages) {
            return $genre->translate($languages);
        });

        $pagination->setCollection($items);

        return $pagination;
    }

    public function createOrUpdateFromTMDBData(array $genreData, string $language): Genre
    {
        $genre = $this->firstOrCreate([
            'external_id' => $genreData['id'],
        ]);

        $currentTranslation = $genre->getTranslation('name', $language, false);

        if (!$currentTranslation || $currentTranslation !== $genreData['name']) {
            $genre->setTranslation('name', $language, $genreData['name']);

            $genre->save();
        }

        return $genre;
    }
}
