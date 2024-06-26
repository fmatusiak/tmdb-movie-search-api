<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $external_id
 * @property array $name
 */
class Genre extends Model
{
    use HasTranslations;

    protected $fillable = [
        'external_id',
    ];

    protected $translatable = [
        'name'
    ];
}
