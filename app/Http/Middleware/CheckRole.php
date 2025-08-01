<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // YETKİSİ YOKSA, ROLÜNE GÖRE DOĞRU EVİNE GÖNDER
        switch ($user->role) {
            case 'admin':
            case 'itfaiye':
                return redirect()->route('index');
            case 'bakanlik':
                return redirect()->route('bakanlik');
            default: // 'user' ve diğerleri
                return redirect()->route('dashboard');
        }
    }
}