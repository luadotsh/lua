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
            // all_with_bc, not the plain `timezone` rule: browsers still send
            // deprecated IANA aliases — Indian clients report Asia/Calcutta,
            // not Asia/Kolkata — and the plain rule rejects them. Unvalidated
            // it was worse: the value is passed straight to `at time zone ?`,
            // so anything the zone database does not know raises an SQL error
            // on PostgreSQL, and returns NULL on MySQL, which zeroes the chart
            // with no error at all.
            'timezone' => ['required', 'string', 'max:255', 'timezone:all_with_bc'],
        ];
    }
}
