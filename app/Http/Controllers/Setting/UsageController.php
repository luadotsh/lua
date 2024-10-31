<?php

declare(strict_types=1);

namespace App\Http\Controllers\Setting;

use Inertia\Inertia;

class UsageController extends Controller
{
    public function index()
    {
        return Inertia::render('Setting/Usage/Index');
    }
}
