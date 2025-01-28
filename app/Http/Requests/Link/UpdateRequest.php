<?php

declare(strict_types=1);

namespace App\Http\Requests\Link;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
        $workspaceDomains = $this
            ->workspace
            ?->domains
            ?->pluck('domain')
            ?->toArray() ?? [];

        $domains = array_merge(
            $workspaceDomains,
            config('domains.available')
        );

        return [
            'key' => Rule::when(
                fn() => $this->key,
                [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[a-z0-9-]+$/', // Only lowercase letters, numbers and hyphens
                    Rule::unique('links')->where('domain', $this->domain)->ignore($this->route('id')),
                ]
            ),

            'domain' => [
                'required',
                'string',
                'max:255',
                'min:2',
                Rule::in($domains),
                Rule::unique('links')->where('key', $this->key)->ignore($this->route('id')),
            ],
            'url' => ['required', 'url', 'max:255', 'min:2'],
            'ios' => ['nullable', 'url', 'max:255', 'min:2'],
            'android' => ['nullable', 'url', 'max:255', 'min:2'],
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
            'external_id' => Rule::when(
                fn() => $this->external_id,
                [
                    'nullable',
                    'string',
                    'max:255',
                    'min:2',
                    Rule::unique('links')->where('workspace_id', $this->workspace_id)->ignore($this->route('id')),
                ]
            ),
            'password' => Rule::when(
                fn() => $this->password,
                ['required', 'string', 'max:255', 'min:6']
            ),
            'expires_at' => Rule::when(
                fn() => $this->expires_at || $this->expired_redirect_url,
                ['required', 'date']
            ),
            'expired_redirect_url' => Rule::when(
                fn() => $this->expired_redirect_url,
                ['nullable', 'url', 'max:255', 'min:2']
            ),
        ];
    }
}
