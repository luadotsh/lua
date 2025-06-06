<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\InviteController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\VerifyEmailController;

/**
 * Auth
 */
Route::group(
    [
        'middleware' => [
            'guest'
        ],
    ],
    function () {

        Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
        Route::post('/register', [RegisteredUserController::class, 'store']);

        Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [AuthenticatedSessionController::class, 'store']);

        Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
        Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

        Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
        Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');

        // invites...
        Route::get('/invites/{id}', [InviteController::class, 'show'])->name('auth.invites.show');
        Route::post('/invites/{id}', [InviteController::class, 'accept'])->name('auth.invites.accept');

        // Google login...
        Route::get('/google/login', [GoogleController::class, 'redirectToProvider'])->name('auth.google');
        Route::get('/google/callback', [GoogleController::class, 'handleProviderCallback'])->name('auth.google.callback');
    }
);

/**
 * Auth
 */
Route::group(
    [
        'middleware' => [
            'auth'
        ],
    ],
    function () {
        Route::get('/verify-email', EmailVerificationPromptController::class)->name('verification.notice');
        Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)->name('verification.verify')->middleware(['signed', 'throttle:6,1']);
        Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->name('verification.send')->middleware('throttle:6,1');
        Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
        Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store']);
        Route::put('/password', [PasswordController::class, 'update'])->name('password.update');
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    }
);
