<?php

namespace App\Repositories;

use App\Models\Movie;

class MovieRepository extends Repository
{
    public function __construct(Movie $movie)
    {
        parent::__construct($movie);
    }

}
