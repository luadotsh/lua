<?php

declare(strict_types=1);

namespace App\Enums\LinkStat;

enum Event: string
{
    case CLICK = 'click';
    case QR_SCAN = 'qr-scan';
}
