<?php

declare(strict_types=1);

namespace App\Http\Requests\Analytics;

use Illuminate\Foundation\Http\FormRequest;

class StatisticsRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'start' => ['required', 'max:255', 'date_format:Y-m-d'],
            'end' => ['required', 'max:255', 'date_format:Y-m-d'],
            'group' => ['required', 'max:255', 'in:minute,hour,day,month'],
            'timezone' => ['required', 'max:255'],
        ];
    }
}
