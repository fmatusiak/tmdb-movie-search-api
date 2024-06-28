<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $external_id
 * @property array $title
 * @property array $overview
 * @property float $vote_average
 * @property int $vote_count
 * @property float $popularity
 * @property string release_date
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
    ];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'genre_movie');
    }

    public function translate(array $languages): Movie
    {
        return self::translateForLanguages($this, $languages);
    }

    private function translateForLanguages(Movie $movie, array $languages): self
    {
        $translatedMovie = new self($movie->toArray());

        if ($movie->id) {
            $translatedMovie->id = $movie->id;
        }

        if ($movie->title) {
            $translatedMovie->title = $movie->getTranslations('title', $languages);
        }

        if ($movie->overview) {
            $translatedMovie->overview = $movie->getTranslations('overview', $languages);
        }

        $translatedMovie->genres = $movie->genres->map(function ($genre) use ($languages) {
            return $genre->translate($languages);
        });

        return $translatedMovie;
    }

}
