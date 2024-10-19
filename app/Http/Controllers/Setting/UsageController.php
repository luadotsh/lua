<?php

namespace App\Http\Controllers\Setting;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Enums\Domain\Status;

use App\Models\Link;
use App\Models\LinkStat;

class UsageController extends Controller
{
    public function index()
    {


        return Inertia::render('Setting/Usage/Index', [

        ]);
    }
}
