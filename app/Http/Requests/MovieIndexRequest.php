<?php

namespace App\Http\Requests;

use App\LanguageHelper;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MovieIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $availableSortColumns = ['id', 'title', 'external_id', 'vote_average', 'vote_count', 'popularity', 'release_date'];
        $availableSortDirections = ['asc', 'desc'];

        return [
            'perPage' => 'integer|min:1',
            'page' => 'integer|min:1',
            'sortBy' => 'string|in:' . implode(',', $availableSortColumns),
            'sortDirection' => 'string|in:' . implode(',', $availableSortDirections),
            'column' => 'string',
            'filters' => 'array',
            'filters.title' => 'string',
            'filters.external_id' => 'string',
            'filters.genre_id' => 'numeric',
            'filters.from_vote_average' => 'numeric',
            'filters.to_vote_average' => 'numeric',
            'filters.from_vote_count' => 'numeric',
            'filters.to_vote_count' => 'numeric',
            'filters.from_popularity' => 'numeric',
            'filters.to_popularity' => 'numeric',
            'filters.from_release_date' => 'date',
            'filters.to_release_date' => 'date',
            'api_language' => [
                'string',
                function ($attribute, $value, $fail) {
                    LanguageHelper::validateLanguage($value, $fail);
                },
            ],
            'language' => [
                'string',
                function ($attribute, $value, $fail) {
                    $languages = explode(',', $value);

                    foreach ($languages as $language) {
                        LanguageHelper::validateLanguage($language, $fail);
                    }
                },
            ],
        ];
    }
}
