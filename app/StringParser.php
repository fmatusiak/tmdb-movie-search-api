<?php

namespace App;

class StringParser
{
    public static function parseStringToArray($string, string $separator = ','): array
    {
        if (is_string($string)) {
            return explode($separator, $string);
        }

        return $string;
    }
}
