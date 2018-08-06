<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Trending;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * 
     * 
     * @return 
     */
    public function show(Trending $trending) {

        if( request()->wantsJson() ) {
            return Thread::search(request('q'))->paginate(25);;
        }

        return view('threads.saerch', [
            'trendings' => $trending->get()
        ]);
    }
}
