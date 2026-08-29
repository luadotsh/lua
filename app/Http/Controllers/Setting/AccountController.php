<?php

declare(strict_types=1);

namespace App\Http\Controllers\Setting;

use App\Actions\Account\DeleteAvatar;
use App\Actions\Account\UpdateAccount;
use App\Http\Requests\Account\UpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Setting/Account/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(UpdateRequest $request)
    {
        UpdateAccount::execute($request->user(), $request->validated());

        session()->flash('flash.banner', 'Account updated');
        session()->flash('flash.bannerStyle', 'success');

        return back();
    }

    public function deletePhoto(Request $request)
    {
        DeleteAvatar::execute($request->user());

        session()->flash('flash.banner', 'Photo deleted');
        session()->flash('flash.bannerStyle', 'success');

        return Inertia::location(route('setting.account.edit'));
    }
}
