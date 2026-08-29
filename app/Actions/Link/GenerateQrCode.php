<?php

declare(strict_types=1);

namespace App\Actions\Link;

use App\Models\Link;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\Color\Hex;

class GenerateQrCode
{
    /**
     * Returns the PNG bytes for a link's QR code. The encoded URL carries
     * ?qr=1 so scans are counted separately from ordinary clicks.
     *
     * Note the colour is applied as the background with a white foreground,
     * which is how this has always behaved.
     *
     * @param  array{color?: string|null, size?: int|null}  $options
     */
    public static function execute(Link $link, array $options = []): string
    {
        $hex = data_get($options, 'color') ?: '#000000';
        $rgb = Hex::fromString($hex)->toRgb();
        $size = (int) (data_get($options, 'size') ?: 256);

        return (string) QrCode::getFacadeRoot()
            ->size($size)
            ->format('png')
            ->backgroundColor($rgb->red(), $rgb->green(), $rgb->blue(), 100)
            ->color(255, 255, 255, 100)
            ->errorCorrection('M')
            ->generate("{$link->link}?qr=1");
    }
}
