<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SocialiteController extends Controller
{
    /**
     * Redirect ke halaman Google OAuth.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Callback dari Google setelah autentikasi berhasil.
     * Setelah login → generate OTP → kirim email → redirect ke halaman OTP.
     */
    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $existingUser = User::where('email', $googleUser->getEmail())->first();

        if ($existingUser) {
            // User sudah ada, update google_id jika belum ada
            if (!$existingUser->google_id) {
                $existingUser->update(['google_id' => $googleUser->getId()]);
            }
            $user = $existingUser;
        } else {
            // Buat user baru
            $user = User::create([
                'google_id' => $googleUser->getId(),
                'name'      => $googleUser->getName(),
                'email'     => $googleUser->getEmail(),
                'password'  => bcrypt(Str::random(16)),
            ]);
        }
        

        // Generate OTP 6 karakter & simpan ke DB
        $otp = strtoupper(Str::random(6));
        $user->update(['otp' => $otp]);

        // Simpan user id ke session sementara (belum login penuh)
        session(['otp_user_id' => $user->id]);

        // Kirim OTP ke email
        Mail::raw(
            "Kode OTP login Koleksi Buku Anda: {$otp}\n\nKode berlaku selama 10 menit.",
            function ($message) use ($user, $otp) {
                $message->to($user->email)
                        ->subject('Kode OTP Login - Koleksi Buku');
            }
        );

        return redirect()->route('otp.form');
    }

    /**
     * Tampilkan form input OTP.
     */
    public function otpForm()
    {
        // Pastikan ada session otp_user_id
        if (!session('otp_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.otp');
    }

    /**
     * Verifikasi OTP yang diinputkan user.
     */
    public function otpVerify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size'     => 'Kode OTP harus 6 karakter.',
        ]);

        $userId = session('otp_user_id');

        if (!$userId) {
            return redirect()->route('login')
                ->with('error', 'Sesi habis, silakan login ulang.');
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'User tidak ditemukan.');
        }

        // Verifikasi OTP (case-insensitive)
        if (strtoupper($request->otp) !== strtoupper($user->otp)) {
            return back()->withErrors(['otp' => 'Kode OTP salah, silakan coba lagi.']);
        }

        // OTP benar → hapus OTP dari DB → buat sesi login → redirect ke home
        $user->update(['otp' => null]);
        session()->forget('otp_user_id');

        Auth::login($user);

        return redirect()->intended('/');
    }

    /**
     * Logout.
     */
    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/login');
    }
}