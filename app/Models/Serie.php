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
    ];

    protected $casts = [
        'vote_average' => 'float',
        'popularity' => 'float',
    ];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'genre_serie');
    }

    public function translate(array $languages): Serie
    {
        return self::translateForLanguages($this, $languages);
    }

    private function translateForLanguages(Serie $serie, array $languages): self
    {
        $translatedSerie = new self($serie->toArray());

        if ($serie->id) {
            $translatedSerie->id = $serie->id;
        }

        if ($serie->title) {
            $translatedSerie->title = $serie->getTranslations('title', $languages);
        }

        if ($serie->overview) {
            $translatedSerie->overview = $serie->getTranslations('overview', $languages);
        }

        $translatedSerie->genres = $serie->genres->map(function ($genre) use ($languages) {
            return $genre->translate($languages);
        });

        return $translatedSerie;
    }
}
