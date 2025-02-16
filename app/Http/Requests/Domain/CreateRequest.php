<?php

declare(strict_types=1);

namespace App\Http\Requests\Domain;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'domain' => [
                'required',
                'regex:/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i', // no schema
                Rule::unique('domains')
                ->whereNull('deleted_at'),
                Rule::notIn(config('domains.available'))
            ],
            'not_found_url' => ['nullable', 'max:255', 'url'],
            'expired_url' => ['nullable', 'max:255', 'url'],
        ];
    }

    public function messages()
    {
        return [
            'domain.regex' => 'The domain format is invalid, do not include schema at the beginning of the domain.',
            'domain.unique' => 'The domain has already been taken.',
            'domain.not_in' => 'The domain is not allowed.',
        ];
    }
}
