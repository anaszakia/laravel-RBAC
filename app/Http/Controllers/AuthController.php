<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('dashboard');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Reload user dengan relasi passkeys untuk memastikan data terbaru
            $user = Auth::user()->loadMissing('passkeys');

            session([
                'user_id'        => Auth::id(),
                'user_name'      => $user->name,
                'user_role'      => $user->role?->slug,
                'last_activity'  => now()->timestamp,
            ]);

            // Selalu tunjukkan prompt passkey jika user belum punya passkey
            if ($user->passkeys()->doesntExist()) {
                return redirect()->route('dashboard')->with('show_passkey_prompt', true);
            }

            return redirect()->route('dashboard');
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
    }

    public function redirectToGoogle(Request $request)
    {
        // Save intent so callback knows whether to auto-register or only login
        $intent = $request->query('intent', 'login');
        session(['oauth_intent' => $intent]);

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            Log::error('Google login gagal: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['email' => 'Login dengan Google gagal, coba lagi.']);
        }

        // Retrieve intent (login or register) and then remove it from session
        $intent = session()->pull('oauth_intent', 'login');

        // Cari berdasarkan google_id dulu, fallback ke email (untuk user lama yang daftar manual)
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        // Pastikan role default 'user' ada (jika belum, buat)
        $defaultRole = Role::firstOrCreate(
            ['slug' => 'user'],
            ['name' => 'User']
        );

        if (! $user) {
            // If the flow was initiated from the login page, do not auto-register
            if ($intent !== 'register') {
                return redirect()->route('login')->withErrors(['email' => 'Akun belum terdaftar. Silakan daftar terlebih dahulu.']);
            }
            $user = User::create([
                'name'              => $googleUser->getName(),
                'email'             => $googleUser->getEmail(),
                'google_id'         => $googleUser->getId(),
                'password'          => bcrypt(Str::random(24)), // tidak pernah dipakai untuk login
                'role_id'           => $defaultRole->id,
                'email_verified_at' => now(),
            ]);

            // Pastikan relasi pivot juga terhubung
            $user->roles()->syncWithoutDetaching([$defaultRole->id]);
        } else {
            $updates = [];
            if (! $user->google_id) {
                // User lama yang sebelumnya daftar manual, sekarang login pakai Google dengan email yang sama
                $updates['google_id'] = $googleUser->getId();
            }

            if (! $user->role_id) {
                // Pastikan user punya role default
                $updates['role_id'] = $defaultRole->id;
            }

            if (! empty($updates)) {
                $user->update($updates);
            }

            // Pastikan relasi pivot juga terhubung
            if (! $user->roles()->where('roles.id', $defaultRole->id)->exists()) {
                $user->roles()->syncWithoutDetaching([$defaultRole->id]);
            }
        }

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        // Reload user dengan relasi passkeys untuk memastikan data terbaru
        $user = Auth::user()->loadMissing('passkeys');

        session([
            'user_id'        => Auth::id(),
            'user_name'      => $user->name,
            'user_role'      => $user->role?->slug,
            'last_activity'  => now()->timestamp,
        ]);

        // Selalu tunjukkan prompt passkey jika user belum punya passkey
        if ($user->passkeys()->doesntExist()) {
            return redirect()->route('dashboard')->with('show_passkey_prompt', true);
        }

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('info', 'Anda telah logout.');
    }
}
