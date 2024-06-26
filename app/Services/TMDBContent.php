<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Mockery\Exception;

class TMDBContent
{
    private object $repository;

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    public function createOrUpdateTMDB(array $data, string $defaultLanguage): void
    {
        try {
            $content = $this->repository->firstOrCreate([
                'external_id' => $data['id']
            ]);

            $content->vote_average = Arr::get($data, 'vote_average', 0);
            $content->vote_count = Arr::get($data, 'vote_count', 0);
            $content->popularity = Arr::get($data, 'popularity', 0);
            $content->release_date = Arr::get($data, 'release_date');

            if ($title = Arr::get($data, 'title')) {
                $content->setTranslation('title', $defaultLanguage, $title);
            }

            if ($title = Arr::get($data, 'name')) {
                $content->setTranslation('title', $defaultLanguage, $title);
            }

            if ($overview = Arr::get($data, 'overview')) {
                $content->setTranslation('overview', $defaultLanguage, $overview);
            }

            $content->save();
        } catch (Exception $e) {
            throw new Exception('An error occurred while creating or updating serie from TMDB data', $e->getCode(), $e);
        }
    }

    protected function saveTranslations(array $data, array $translations): void
    {
        $content = $this->repository->whereFirst(['external_id' => $data['id']]);

        if (!$content) {
            return;
        }

        foreach ($translations as $language => $value) {
            if ($title = Arr::get($value, 'title')) {
                $content->setTranslation('title', $language, $title);
            }

            if ($title = Arr::get($value, 'name')) {
                $content->setTranslation('title', $language, $title);
            }

            if ($overview = Arr::get($value, 'overview')) {
                $content->setTranslation('overview', $language, $overview);
            }
        }

        $content->save();
    }
}
