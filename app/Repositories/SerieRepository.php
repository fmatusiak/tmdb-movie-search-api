<?php

namespace App\Repositories;

use App\Models\Serie;

class SerieRepository extends Repository
{

    public function __construct(Serie $serie)
    {
        parent::__construct($serie);
    }
}
