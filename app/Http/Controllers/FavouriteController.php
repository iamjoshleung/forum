<?php

namespace App\Http\Controllers;

use App\Reply;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{

    /**
     * 
     * 
     * @return 
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * 
     * 
     * @return 
     */
    public function store(Reply $reply) {
        $reply->favourite();

        return back();
    }

    /**
     * 
     * 
     * @return 
     */
    public function destroy(Reply $reply) {
        $reply->unfavourite();
    }
}
