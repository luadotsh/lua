<?php

declare(strict_types=1);

namespace App\Http\Requests\Analytics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StatisticsRequest extends FormRequest
{
    /**
     * Every metric the analytics endpoint knows how to answer. Adding a chart
     * means adding it here as well as in the controller's switch.
     *
     * @var list<string>
     */
    public const array METRICS = [
        'events',
        'clicks',
        'qrScans',
        'links',
        'referers',
        'utm-sources',
        'utm-mediums',
        'utm-campaigns',
        'utm-contents',
        'utm-terms',
        'browsers',
        'os',
        'devices',
        'languages',
        'countries',
        'regions',
        'cities',
    ];

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'start' => ['required', 'max:255', 'date_format:Y-m-d'],
            'end' => ['required', 'max:255', 'date_format:Y-m-d'],
            'metric' => ['required', 'max:255', Rule::in(self::METRICS)],
            'group' => ['required', 'max:255', 'in:minute,hour,day,month'],
            'timezone' => ['required', 'max:255'],
        ];
    }
}
