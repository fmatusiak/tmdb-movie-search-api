<?php

namespace App\Repositories;

use App\Models\Genre;

interface GenreRepositoryInterface
{
    public function createOrUpdateGenreFromTMDBData(array $genreData, string $language): Genre;
}
