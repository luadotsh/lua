<?php

declare(strict_types=1);

namespace App\Http\Requests\Tool;

use Illuminate\Foundation\Http\FormRequest;

class CheckLinkRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // The action rejects private addresses and non-http schemes on
            // every hop; this only keeps obvious rubbish out of it.
            'url' => ['required', 'string', 'max:2048', 'url:http,https'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'url.url' => 'That does not look like a web address.',
        ];
    }
}
