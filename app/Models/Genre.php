<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function translate(array $languages): Genre
    {
        return self::translateForLanguages($this, $languages);
    }

    private function translateForLanguages(Genre $genre, array $languages): self
    {
        $translatedGenre = new self($genre->toArray());

        if ($genre->id) {
            $translatedGenre->id = $genre->id;
        }

        $translatedGenre->name = $genre->getTranslations('name', $languages);

        return $translatedGenre;
    }

    public function movies(): BelongsToMany
    {
        return $this->BelongsToMany(Movie::class, 'genre_movie');
    }

    public function series(): BelongsToMany
    {
        return $this->BelongsToMany(Serie::class, 'genre_serie');
    }
}
