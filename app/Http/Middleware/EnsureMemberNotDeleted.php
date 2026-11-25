<?php

namespace App\Http\Middleware;

use App\Models\Member;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureMemberNotDeleted
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && Member::onlyTrashed()->where('user_id', $user->id)->exists()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('platform.login')
                ->withErrors(['email' => 'Akun ini sudah dinonaktifkan. Hubungi pengurus.']);
        }

        return $next($request);
    }
}
