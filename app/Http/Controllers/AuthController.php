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

            session([
                'user_id'        => Auth::id(),
                'user_name'      => Auth::user()->name,
                'user_role'      => Auth::user()->role?->slug,
                'last_activity'  => now()->timestamp,
            ]);

            return redirect()->route('dashboard');
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
    }

    public function redirectToGoogle()
    {
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

        // Cari berdasarkan google_id dulu, fallback ke email (untuk user lama yang daftar manual)
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if (! $user) {
            // Sesuaikan slug role default di sini dengan yang ada di tabel roles kamu
            $defaultRole = Role::where('slug', 'user')->first();

            $user = User::create([
                'name'              => $googleUser->getName(),
                'email'             => $googleUser->getEmail(),
                'google_id'         => $googleUser->getId(),
                'password'          => bcrypt(Str::random(24)), // tidak pernah dipakai untuk login
                'role_id'           => $defaultRole?->id,
                'email_verified_at' => now(),
            ]);
        } elseif (! $user->google_id) {
            // User lama yang sebelumnya daftar manual, sekarang login pakai Google dengan email yang sama
            $user->update(['google_id' => $googleUser->getId()]);
        }

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        session([
            'user_id'        => Auth::id(),
            'user_name'      => Auth::user()->name,
            'user_role'      => Auth::user()->role?->slug,
            'last_activity'  => now()->timestamp,
        ]);

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