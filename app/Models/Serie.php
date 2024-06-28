<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
class Serie extends Model
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
        'release_date' => 'date'
    ];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'genre_serie');
    }

}
