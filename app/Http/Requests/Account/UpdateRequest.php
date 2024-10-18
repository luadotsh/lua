<?php

declare(strict_types=1);

namespace App\Http\Requests\Account;

use App\Models\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'email' => ['email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'current_password' => ['nullable', 'string', 'current_password:web'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults(), Rule::requiredIf($this->current_password ? true : false)],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'current_password.current_password' => __('The provided password does not match your current password.'),
        ];
    }
}
