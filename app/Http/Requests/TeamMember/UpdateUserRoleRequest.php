<?php

declare(strict_types=1);

namespace App\Http\Requests\TeamMember;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rules\Enum;
use App\Enums\User\Role as UserRole;

class UpdateUserRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'role' => [new Enum(UserRole::class)],
        ];
    }
}
