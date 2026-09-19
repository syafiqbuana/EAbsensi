<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use App\Models\TpqProfile; 

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();

        $tpq = TpqProfile::find($user->profile->tpq_profile_id);

        return redirect('/' . $tpq->slug . '/dashboard');
    }
}