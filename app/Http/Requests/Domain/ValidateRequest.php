<?php

declare(strict_types=1);

namespace App\Http\Requests\Domain;

use Illuminate\Foundation\Http\FormRequest;

class ValidateRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'domain' => ['required', 'max:255'],
        ];
    }
}
