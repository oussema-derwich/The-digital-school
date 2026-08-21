<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Require2FA
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->two_factor_enabled && !session('2fa_verified')) {
            return response()->json([
                'status' => 'error',
                'message' => '2FA verification required',
                'requires_2fa' => true
            ], 403);
        }

        return $next($request);
    }
}