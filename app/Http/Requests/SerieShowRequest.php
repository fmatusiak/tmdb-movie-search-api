<?php

namespace App\Http\Requests;

use App\Exceptions\TMDBApiLanguageNotSupportedException;
use App\TMDBApiLanguage;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SerieShowRequest extends FormRequest
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
