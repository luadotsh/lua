<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\LinkStat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $workspace = auth()->user()->currentWorkspace;

        $start = Carbon::createFromFormat('Y-m-d', $request->start ? $request->start : now()->subDays(30)->format('Y-m-d'))->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $request->end ? $request->end : now()->format('Y-m-d'))->endOfDay();

        $query = LinkStat::where('workspace_id', $workspace->id)
            ->with('link:id,link')
            ->whereBetween('created_at', [$start, $end])
            ->latest();

        $links = $query->paginate(config('app.pagination.default'))->withQueryString();

        return Inertia::render('Event/Index', [
            /**
             * Wrapped in `Inertia::scroll()` so the frontend `<InfiniteScroll>`
             * can ask for the next page and have it appended. The pagination
             * itself is unchanged — this only marks the prop as one the client
             * may extend.
             */
            'table' => Inertia::scroll(fn () => $links),
            'hasData' => LinkStat::where('workspace_id', $workspace->id)->exists(),
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
        ]);
    }
}
