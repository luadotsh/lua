<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Http\Requests\Link\CreateRequest;
use App\Http\Requests\Link\UpdateRequest;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use SimpleSoftwareIO\QrCode\Generator;

use Spatie\Color\Hex;

use App\Models\Link;

class LinkController extends Controller
{
    public function index(Request $request)
    {
        $links = Link::where('workspace_id', $request->workspace->id)
            ->with('tags')
            ->latest()
            ->paginate(config('app.pagination.default'));

        return response()->json($links);
    }

    public function store(CreateRequest $request)
    {
        Gate::authorize('create-link', $request->workspace);

        $link = Link::create([
            'workspace_id' => $request->workspace->id,
            'domain' => $request->domain,
            'key' => $request->key,
            'url' => $request->url,
            'link' => "https://{$request->domain}/{$request->key}",
            'utm_source' => $request->utm_source,
            'utm_medium' => $request->utm_medium,
            'utm_campaign' => $request->utm_campaign,
            'utm_term' => $request->utm_term,
            'utm_content' => $request->utm_content,
        ]);

        // update tags
        $link->tags()->sync($request->tags);

        return response()->json($link);
    }

    public function update($id, UpdateRequest $request)
    {
        $link = Link::where('workspace_id', $request->workspace->id)->where('id', $id)->first();
        if (!$link) {
            return response()->json(['message' => 'Link not found'], 404);
        }

        $link->update([
            'domain' => $request->domain,
            'key' => $request->key,
            'url' => $request->url,
            'link' => "https://{$request->domain}/{$request->key}",
            'utm_source' => $request->utm_source,
            'utm_medium' => $request->utm_medium,
            'utm_campaign' => $request->utm_campaign,
            'utm_term' => $request->utm_term,
            'utm_content' => $request->utm_content,
        ]);

        // update tags
        $link->tags()->sync($request->tags);

        return response()->json($link);
    }

    public function destroy($id, Request $request)
    {
        $link = Link::where('workspace_id', $request->workspace->id)->where('id', $id)->first();
        if(!$link) {
            return response()->json(['message' => 'Link not found'], 404);
        }

        $link->delete();

        return response()->json(['message' => 'Link deleted']);
    }

    public function qrCode(Request $request, $id)
    {
        $request->validate([
            'download' => ['nullable', 'boolean'],
            'color' => ['nullable', 'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i'],
        ]);

        $link = Link::findOrFail($id);

        $qrCodeGenerator = QrCode::getFacadeRoot();

        $rgb =  Hex::fromString($request->query('color') ? $request->query('color') : '#000000')->toRgb();
        $bgColor = [$rgb->red(), $rgb->green(), $rgb->blue(), 100];

        $qrCode = $qrCodeGenerator
            ->size(256)
            ->format('png')
            ->backgroundColor(...$bgColor)
            ->color(255, 255, 255, 100)
            // ->merge('/public/images/user/avatar.jpg')
            ->errorCorrection('M')
            ->generate("{$link->link}?qr=1");

        // download qr code
        if ($request->query('download') == true) {
            return response()->streamDownload(
                function () use ($qrCode): void {
                    /** @var string $qrCode */
                    echo $qrCode;
                },
                'qr-code.png',
                ['Content-Type' => 'image/png']
            );
        }

        // render qr code
        return response($qrCode)
            ->header('Content-Type', 'image/png');
    }
}
