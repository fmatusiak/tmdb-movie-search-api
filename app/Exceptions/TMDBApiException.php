<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class TMDBApiException extends Exception
{
    public function __construct($message = 'TMDB API error', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
