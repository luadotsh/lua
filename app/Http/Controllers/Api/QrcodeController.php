<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use SimpleSoftwareIO\QrCode\Generator;

use Spatie\Color\Hex;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use App\Models\Link;

class QrcodeController extends Controller
{
    public function __invoke($id, Request $request)
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
            // ->merge('/public/images/links/qr-base.png')
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
