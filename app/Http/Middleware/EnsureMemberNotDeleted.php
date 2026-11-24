<?php

namespace App\Http\Middleware;

use App\Models\Member;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureMemberNotDeleted
{
    public function handle(Request , Closure )
    {
         = ->user();

        if ( && Member::onlyTrashed()->where('user_id', ->id)->exists()) {
            Auth::logout();
            ->session()->invalidate();
            ->session()->regenerateToken();

            return redirect()->route('platform.login')
                ->withErrors(['email' => 'Akun ini sudah dinonaktifkan. Hubungi pengurus.']);
        }

        return ();
    }
}
