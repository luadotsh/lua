<?php

declare(strict_types=1);

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DestroySessionsRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // Signing every other device out is destructive, so it is confirmed
        // with the password — or, for a social-only account, the email.
        return $this->user()->password
            ? ['password' => ['required', 'current_password']]
            : ['email_confirmation' => ['required', Rule::in([$this->user()->email])]];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email_confirmation.in' => 'That does not match your email address.',
        ];
    }
}
