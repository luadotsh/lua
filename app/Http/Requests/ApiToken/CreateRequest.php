<?php

declare(strict_types=1);

namespace App\Http\Requests\ApiToken;

use App\Actions\ApiKey\CreateApiKey;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'expires_at' => CreateApiKey::expiresAtRules(),
        ];
    }
}
