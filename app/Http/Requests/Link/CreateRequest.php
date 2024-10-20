<?php

declare(strict_types=1);

namespace App\Http\Requests\Link;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'key' => [
                'required',
                'string',
                'max:255',
                'min:2',
                Rule::unique('links')->where('domain', $this->domain)->ignore($this->route('id')),
            ],
            'domain' => [
                'required',
                'string',
                'max:255',
                'min:2',
                Rule::unique('links')->where('key', $this->key)->ignore($this->route('id')),
            ],
            'url' => ['required', 'url', 'max:255', 'min:2'],

            'utm_source' => Rule::when(
                fn() => $this->utm_source,
                ['required', 'string', 'max:255', 'min:2']
            ),
            'utm_medium' => Rule::when(
                fn() => $this->utm_medium,
                ['required', 'string', 'max:255', 'min:2']
            ),
            'utm_campaign' => Rule::when(
                fn() => $this->utm_campaign,
                ['required', 'string', 'max:255', 'min:2']
            ),
            'utm_term' => Rule::when(
                fn() => $this->utm_term,
                ['required', 'string', 'max:255', 'min:2']
            ),
            'utm_content' => Rule::when(
                fn() => $this->utm_content,
                ['required', 'string', 'max:255', 'min:2']
            ),
            'tags' => ['array'],
            'external_id' => [
                'nullable',
                'string',
                'max:255',
                'min:2',
                Rule::unique('links')->where('workspace_id', $this->workspace_id)->ignore($this->route('id')),
            ],
            'password' => Rule::when(
                fn() => $this->utm_source,
                ['required', 'string', 'max:255', 'min:6']
            ),
        ];
    }
}
