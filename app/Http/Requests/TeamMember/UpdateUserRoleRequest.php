<?php

declare(strict_types=1);

namespace App\Http\Requests\TeamMember;

use App\Enums\User\Role as UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateUserRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'role' => [new Enum(UserRole::class)],
        ];
    }
}
