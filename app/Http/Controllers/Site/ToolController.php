<?php

declare(strict_types=1);

namespace App\Http\Controllers\Site;

use App\Actions\Tool\FollowRedirects;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tool\CheckLinkRequest;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Free tools. Each one is a real thing that works without an account, which is
 * the only reason a stranger has to arrive on one, and each is adjacent to
 * what the product does rather than a keyword with a page attached.
 */
class ToolController extends Controller
{
    /**
     * @var list<array{slug: string, name: string, description: string, route: string}>
     */
    private const TOOLS = [
        [
            'slug' => 'utm-builder',
            'name' => 'UTM builder',
            'description' => 'Tag a link so the click reads back as a placement rather than as "Direct". Runs in the browser; nothing is sent anywhere.',
            'route' => 'site.tools.utm-builder',
        ],
        [
            'slug' => 'qr-generator',
            'name' => 'QR code generator',
            'description' => 'A QR code for any URL, downloadable as PNG or SVG. Generated in the browser, so the address never leaves your machine.',
            'route' => 'site.tools.qr-generator',
        ],
        [
            'slug' => 'link-checker',
            'name' => 'Redirect checker',
            'description' => 'Follow a short link through every hop and see where it actually ends up, and what status each step returned.',
            'route' => 'site.tools.link-checker',
        ],
    ];

    public function index(): Response
    {
        return Inertia::render('Site/Tools/Index', [
            'tools' => collect(self::TOOLS)
                ->map(fn (array $tool): array => [...$tool, 'url' => route($tool['route'])])
                ->all(),
            'seo' => [
                'title' => 'Free link tools',
                'description' => 'A UTM builder, a QR code generator and a redirect checker. No account, no sign-up, and the first two never send your URL anywhere.',
            ],
        ]);
    }

    public function utmBuilder(): Response
    {
        return Inertia::render('Site/Tools/UtmBuilder', [
            'seo' => [
                'title' => 'UTM builder',
                'description' => 'Build a tagged URL with utm_source, utm_medium, utm_campaign, utm_term and utm_content. Runs entirely in your browser.',
            ],
        ]);
    }

    public function qrGenerator(): Response
    {
        return Inertia::render('Site/Tools/QrGenerator', [
            'seo' => [
                'title' => 'QR code generator',
                'description' => 'Turn any URL into a QR code and download it as PNG or SVG. Generated in your browser, so the address is never sent to us.',
            ],
        ]);
    }

    public function linkChecker(): Response
    {
        return Inertia::render('Site/Tools/LinkChecker', [
            'seo' => [
                'title' => 'Redirect checker',
                'description' => 'See every hop a short link takes and where it really ends up, with the status code each step returned.',
            ],
        ]);
    }

    /**
     * The one tool that has to reach the network, so it is the one that is
     * rate limited and guarded. See FollowRedirects for why.
     */
    public function check(CheckLinkRequest $request): JsonResponse
    {
        return response()->json(FollowRedirects::execute($request->string('url')->toString()));
    }
}
