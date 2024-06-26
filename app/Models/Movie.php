<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $external_id
 * @property string $title
 * @property string $overview
 * @property float $vote_average
 * @property int $vote_count
 * @property float $popularity
 * @property Carbon release_date
 */
class Movie extends Model
{
    use HasTranslations;

    protected $translatable = [
        'title',
        'overview',
    ];

    protected $fillable = [
        'external_id',
        'vote_average',
        'vote_count',
        'popularity',
        'release_date'
    ];

    protected $casts = [
        'vote_average' => 'float',
        'popularity' => 'float',
        'release_date' => 'date'
    ];
}
