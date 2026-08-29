<?php

declare(strict_types=1);

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'media' => ['required', 'image', 'max:2048'],
            // The owner is derived from the collection, so the client never
            // names the model it is uploading to.
            'collection' => ['required', Rule::in(['avatar', 'logo'])],
        ];
    }
}
