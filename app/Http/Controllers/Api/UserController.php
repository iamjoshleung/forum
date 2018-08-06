<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * 
     * 
     * @return 
     */
    public function index() {
        $search = request('q');
        // dd($search);
        return User::where('name', 'like', "%{$search}%")
            ->take('5')
            ->pluck('name');
    }
}
