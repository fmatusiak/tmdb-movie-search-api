<?php

namespace App;

use App\Exceptions\TMDBApiLanguageNotSupportedException;
use Illuminate\Support\Facades\Lang;

enum TMDBApiLanguage: string
{
    case English = 'en';
    case Polish = 'pl';
    case Deutsch = 'de';

    public static function isValid(string $language): void
    {
        if (!TMDBApiLanguage::tryFrom($language)) {
            throw new TMDBApiLanguageNotSupportedException(Lang::get('messages.language_not_supported', ['language' => $language]));
        }
    }
}
