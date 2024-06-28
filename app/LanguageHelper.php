<?php

namespace App;

use App\Exceptions\TMDBApiLanguageNotSupportedException;

class LanguageHelper
{
    public static function validateLanguage($language, $fail): void
    {
        try {
            if (!$language) {
                throw new TMDBApiLanguageNotSupportedException();
            }

            TMDBApiLanguage::isValid($language);
        } catch (TMDBApiLanguageNotSupportedException $e) {
            $fail($e->getMessage());
        }
    }
}
