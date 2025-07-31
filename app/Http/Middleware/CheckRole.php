<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Gelen isteği işle.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Kullanıcı giriş yapmamışsa, direkt login sayfasına yönlendir.
        if (!Auth::check()) {
            return redirect('login');
        }

        // Kullanıcının rolünü al.
        $userRole = Auth::user()->role;

        // Kullanıcının rolü, izin verilen roller listesinde var mı diye kontrol et.
        if (in_array($userRole, $roles)) {
            // Yetkisi varsa, isteğin devam etmesine izin ver.
            return $next($request);
        }

        // Yetkisi yoksa, ana sayfasına (gönüllü paneline) yönlendir.
        return redirect('/dashboard');
    }
}