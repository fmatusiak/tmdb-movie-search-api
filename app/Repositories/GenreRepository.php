<?php

namespace App\Repositories;

use App\Models\Genre;

class GenreRepository extends Repository implements GenreRepositoryInterface
{
    public function __construct(Genre $genre)
    {
        parent::__construct($genre);
    }

    public function createOrUpdateGenreFromTMDBData(array $genreData, string $language): Genre
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
