<?php

declare(strict_types=1);

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Someone who signed up through a social provider has no password
            // to confirm yet, so they are setting a first one.
            'current_password' => $this->user()->password
                ? ['required', 'current_password']
                : ['nullable'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
