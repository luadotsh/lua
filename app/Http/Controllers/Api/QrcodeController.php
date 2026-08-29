<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\Link\GenerateQrCode;
use App\Http\Requests\Qrcode\ShowRequest;
use App\Models\Link;

class QrcodeController extends Controller
{
    public function __invoke($id, ShowRequest $request)
    {
        $link = Link::findOrFail($id);

        $qrCode = GenerateQrCode::execute($link, [
            'color' => $request->query('color'),
        ]);

        if ($request->query('download') == true) {
            return response()->streamDownload(
                function () use ($qrCode): void {
                    echo $qrCode;
                },
                'qr-code.png',
                ['Content-Type' => 'image/png'],
            );
        }

        return response($qrCode)->header('Content-Type', 'image/png');
    }
}
