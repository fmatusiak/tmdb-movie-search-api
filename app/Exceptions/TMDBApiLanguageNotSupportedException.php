<?php

namespace App\Exceptions;

use InvalidArgumentException;
use Throwable;

class TMDBApiLanguageNotSupportedException extends InvalidArgumentException
{
    public function __construct($message = 'Language not supported for TMDB API', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
