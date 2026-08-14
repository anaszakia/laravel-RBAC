<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use Laravel\Passkeys\Http\Controllers\PasskeyConfirmationController;
use Laravel\Passkeys\Http\Controllers\PasskeyLoginController;
use Laravel\Passkeys\Http\Controllers\PasskeyRegistrationController;


// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', fn() => view('auth.register'))->name('register');
Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');
Route::post('/forgot-password', fn() => back()->with('status', 'Link dikirim!'))->name('password.email');
Route::get('/reset-password/{token?}', fn($token = '') => view('auth.reset-password', compact('token')))->name('password.reset');
Route::post('/reset-password', fn() => redirect()->route('login'))->name('password.update');
Route::get('/otp-verification', fn() => view('auth.otp-verification'))->name('otp.verification');
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Passkeys
Route::get('/passkeys/login/options', [PasskeyLoginController::class, 'index'])
    ->middleware('guest:web')
    ->name('passkey.login-options');
Route::post('/passkeys/login', [PasskeyLoginController::class, 'store'])
    ->middleware('guest:web')
    ->name('passkey.login');

Route::middleware(['auth:web', 'auth.custom'])->group(function () {
    Route::get('/passkeys/confirm/options', [PasskeyConfirmationController::class, 'index'])
        ->name('passkey.confirm-options');
    Route::post('/passkeys/confirm', [PasskeyConfirmationController::class, 'store'])
        ->name('passkey.confirm');
    Route::get('/user/passkeys/options', [PasskeyRegistrationController::class, 'index'])
        ->name('passkey.registration-options');
    Route::post('/user/passkeys', [PasskeyRegistrationController::class, 'store'])
        ->name('passkey.store');
    Route::delete('/user/passkeys/{passkey}', [PasskeyRegistrationController::class, 'destroy'])
        ->name('passkey.destroy');
});

// Protected routes
Route::middleware(['auth.custom', 'auto.logout'])->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Menu management - hanya admin
    Route::middleware(['role:superadmin'])->group(function () {
        Route::resource('/menus', MenuController::class);
        Route::resource('/roles', RoleController::class);
        Route::resource('/permissions', PermissionController::class);
});

// Keep-alive untuk reset session timeout
Route::post('/keep-alive', function () {
    session(['last_activity' => now()->timestamp]);
    return response()->json(['status' => 'ok']);
})->middleware(['auth.custom'])->name('keep.alive');

    // Users dengan permission per aksi
        Route::get('/users', [UserController::class, 'index'])
            ->name('users.index')
            ->middleware('permission:users.view');

        Route::get('/users/create', [UserController::class, 'create'])
            ->name('users.create')
            ->middleware('permission:users.create');

        Route::post('/users', [UserController::class, 'store'])
            ->name('users.store')
            ->middleware('permission:users.create');

        Route::get('/users/{user}', [UserController::class, 'show'])
            ->name('users.show')
            ->middleware('permission:users.view');

        Route::get('/users/{user}/edit', [UserController::class, 'edit'])
            ->name('users.edit')
            ->middleware('permission:users.edit');

        Route::put('/users/{user}', [UserController::class, 'update'])
            ->name('users.update')
            ->middleware('permission:users.edit');

        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->name('users.destroy')
            ->middleware('permission:users.delete');
    });
