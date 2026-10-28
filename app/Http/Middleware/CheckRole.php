<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Memeriksa apakah role user yang sedang login sesuai dengan role yang diizinkan.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Pastikan user sudah login
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // 2. Periksa apakah akun user berstatus 'aktif'
        if ($request->user()->status_akun !== 'aktif') {
            auth()->logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda dinonaktifkan. Hubungi administrator.',
            ]);
        }

        // 3. Periksa apakah role user ada di dalam daftar role yang diizinkan
        if (! in_array($request->user()->role, $roles)) {
            // Jika satpam mencoba buka halaman admin, kembalikan ke dashboard satpam
            if ($request->user()->role === 'satpam') {
                return redirect()->route('satpam.dashboard')->with('error', 'Anda tidak memiliki wewenang untuk membuka halaman tersebut.');
            }

            // Jika admin mencoba buka halaman satpam, kembalikan ke dashboard admin
            if ($request->user()->role === 'admin_kantor') {
                return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki wewenang untuk membuka halaman tersebut.');
            }

            abort(403, 'Akses Ditolak: Anda tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}
