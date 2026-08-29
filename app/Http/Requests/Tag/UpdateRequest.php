<?php

declare(strict_types=1);

namespace App\Http\Requests\Tag;

use App\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'color' => ['required', 'string', 'regex:'.Tag::COLOR_PATTERN],
        ];
    }
}
