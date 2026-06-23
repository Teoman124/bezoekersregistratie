<?php

use App\Http\Controllers\Auth\ConfirmationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegistrationController::class, 'create'])->name('register');
    Route::post('register', [RegistrationController::class, 'store']);

    Route::middleware('set.locale')->group(function () {
        Route::get('visitor/register', [RegistrationController::class, 'createVisitor'])->name('visitor.register');
        Route::post('visitor/register', [RegistrationController::class, 'storeVisitor'])->name('visitor.register.store');
    });

    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);

    Route::middleware('set.locale')->group(function () {
        Route::get('visitor/login', [LoginController::class, 'createVisitor'])->name('visitor.login');
        Route::post('visitor/login', [LoginController::class, 'storeVisitor'])->name('visitor.login.store');
    });

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware(['auth', 'set.locale'])->group(function () {
    Route::get('visitor/company-info', [RegistrationController::class, 'createCompanyInfo'])->name('visitor.company-info');
    Route::post('visitor/company-info', [RegistrationController::class, 'storeCompanyInfo'])->name('visitor.company-info.store');
    Route::post('visitor/company-info/skip', [RegistrationController::class, 'skipCompanyInfo'])->name('visitor.company-info.skip');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::post('verify-email', [VerificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.store');
    Route::get('verify-email/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    Route::get('confirm-password', [ConfirmationController::class, 'create'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmationController::class, 'store'])->name('confirmation.store');

    Route::match(['get', 'post'], 'logout', [LoginController::class, 'destroy'])->name('logout');
});
