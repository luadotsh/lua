<?php

declare(strict_types=1);

namespace App\Http\Requests\Invite;

use App\Enums\User\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class InviteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                // Scoped to the workspace: a global unique meant inviting
                // someone who had a pending invite anywhere else failed, which
                // both blocked a legitimate invite and told you they had one.
                Rule::unique('invites')->where(
                    'workspace_id',
                    $this->user()?->current_workspace_id,
                ),
            ],
            // Required: without it the null went into a NOT NULL column, and on
            // the existing-user branch into the membership pivot, as a 500.
            'role' => ['required', new Enum(Role::class)],
        ];
    }
}
