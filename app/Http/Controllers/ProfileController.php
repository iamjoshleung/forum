<?php

namespace App\Http\Controllers;

use App\User;
use App\Activity;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * 
     * 
     * @return 
     */
    public function show(User $user) {

        // return $user->activity()->with('subject')->get();

        // return $this->getActivity($user);

        return view('profiles.show', [
            'profileUser' => $user,
            'activities' => Activity::feed($user)
        ]);
    }
}
