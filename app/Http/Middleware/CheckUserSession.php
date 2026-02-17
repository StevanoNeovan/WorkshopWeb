<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserSession
{
    /**
     * Handle an incoming request.
     * Mengecek apakah user sudah login melalui session dan auth guard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek dengan Laravel auth guard (middleware 'auth' utama)
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek session user_data; jika belum ada, buat baru
        if (!session()->has('user_data')) {
            session([
                'user_data' => [
                    'id'    => Auth::user()->id,
                    'name'  => Auth::user()->name,
                    'email' => Auth::user()->email,
                ]
            ]);
        }

        return $next($request);
    }
}