<?php

declare(strict_types=1);

namespace App\Http\Requests\Link;

use App\Actions\Link\CreateLink;
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
     * The action already falls back to the main domain, and the MCP tool fills
     * it in before validating. Doing it here too means "just a url" is the
     * minimum on every surface, rather than only on two of them.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'domain' => $this->input('domain') ?: config('domains.main'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // The API middleware merges the token's workspace onto the request;
        // on the web it is the one the user has selected.
        $workspace = $this->workspace ?? $this->user()?->currentWorkspace;

        return CreateLink::rules($workspace, $this->all(), null);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return CreateLink::messages();
    }
}
