<?php

namespace App;

use App\Exceptions\TMDBApiLanguageNotSupportedException;

enum TMDBApiLanguage: string
{
    case English = 'en';
    case Polish = 'pl';
    case Deutsch = 'de';

    public static function isValid(string $language): void
    {
        if (!TMDBApiLanguage::tryFrom($language)) {
            throw new TMDBApiLanguageNotSupportedException("Language '{$language}' not supported");
        }
    }
}
