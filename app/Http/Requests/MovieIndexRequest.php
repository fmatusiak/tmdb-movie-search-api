<?php

namespace App\Http\Requests;

use App\Exceptions\TMDBApiLanguageNotSupportedException;
use App\TMDBApiLanguage;
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
        return [
            'perPage' => 'integer|min:1',
            'columns' => 'string',
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
            'language' => [
                'string',
                function ($attribute, $value, $fail) {
                    $languages = explode(',', $value);

                    foreach ($languages as $language) {
                        try {
                            TMDBApiLanguage::isValid($language);
                        } catch (TMDBApiLanguageNotSupportedException $e) {
                            $fail($e->getMessage());
                        }
                    }
                },
            ],
        ];
    }
}
