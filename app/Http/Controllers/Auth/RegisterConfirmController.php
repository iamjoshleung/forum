<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegisterConfirmController extends Controller
{
    /**
     * 
     * 
     * @return 
     */
    public function index()
    {
        $user = User::where('confirmation_token', request()->query('token'))
            ->first();


        if (!$user) {
            return redirect(route('threads'))
                ->with('flash', 'Invalid token.');
        }

        $user->confirm();

        return redirect(route('threads'))
            ->with('flash', 'Your validity have been confirmed');
    }
}
