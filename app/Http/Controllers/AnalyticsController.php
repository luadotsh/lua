<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\CalculateStat;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AnalyticsController extends Controller
{
    public function __construct(
        public CalculateStat $stat
    ) {}

    public function index(Request $request)
    {
        $start = Carbon::createFromFormat('Y-m-d', $request->start ? $request->start : now()->subDays(30)->format('Y-m-d'))->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $request->end ? $request->end : now()->format('Y-m-d'))->endOfDay();

        return Inertia::render('Analytics/Index',[
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
        ]);
    }

    public function statistics(Request $request)
    {
        $request->validate([
            'start' => ['required', 'max:255', 'date_format:Y-m-d'],
            'end' => ['required', 'max:255', 'date_format:Y-m-d'],
            'metric' => [
                'required',
                'max:255',
                Rule::in([
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
                    'cities'
                ])
            ],
            'group' => ['required', 'max:255', 'in:minute,hour,day,month'],
            'timezone' => ['required', 'max:255'],
        ]);

        $workspace = auth()->user()->currentWorkspace;

        $timezone = $request->timezone;

        $start = Carbon::createFromFormat('Y-m-d', $request->start, $timezone)->startOfDay()->setTimezone('UTC');
        $end = Carbon::createFromFormat('Y-m-d', $request->end, $timezone)->endOfDay()->setTimezone('UTC');

        switch ($request->metric) {

            case 'events':
                $data = $this->stat->events($workspace, $timezone, $start, $end, $request->group);
                break;

            case 'clicks':
                $data = $this->stat->clicks($workspace, $timezone, $start, $end, $request->group);
                break;

            case  'qrScans':
                $data = $this->stat->qrScans($workspace, $timezone, $start, $end, $request->group);
                break;

            case 'links':
                $data = $this->stat->linkStats($workspace->id, $start, $end);
                break;

            case 'referers':
                $data = $this->stat->refererStats($workspace->id, $start, $end);
                break;

            case 'utm-sources':
                $data = $this->stat->utmSourceStats($workspace->id, $start, $end);
                break;

            case 'utm-mediums':
                $data = $this->stat->utmMediumStats($workspace->id, $start, $end);
                break;

            case 'utm-campaigns':
                $data = $this->stat->utmCampaignStats($workspace->id, $start, $end);
                break;

            case 'utm-contents':
                $data = $this->stat->utmContentStats($workspace->id, $start, $end);
                break;

            case 'utm-terms':
                $data = $this->stat->utmTermStats($workspace->id, $start, $end);
                break;

            case 'browsers':
                $data = $this->stat->browserStats($workspace->id, $start, $end);
                break;

            case 'os':
                $data = $this->stat->osStats($workspace->id, $start, $end);
                break;

            case 'devices':
                $data = $this->stat->deviceStats($workspace->id, $start, $end);
                break;

            case 'languages':
                $data = $this->stat->languageStats($workspace->id, $start, $end);
                break;

            case 'countries':
                $data = $this->stat->countryStats($workspace->id, $start, $end);
                break;

            case 'regions':
                $data = $this->stat->regionStats($workspace->id, $start, $end);
                break;

            case 'cities':
                $data = $this->stat->cityStats($workspace->id, $start, $end);
                break;

            default:
                $data = [];
                break;
        }

        return response()->json($data);
    }
}
