<?php

namespace App\Http\Controllers;

use App\Thread;
use Illuminate\Http\Request;

class ThreadSubscriptionController extends Controller
{
    /**
     * 
     * 
     * @return 
     */
    public function store($channelId, Thread $thread) {
        return $thread->subscribe();
    }

    /**
     * 
     * 
     * @return 
     */
    public function destroy($channelId, Thread $thread) {
        if( $thread->isSubscribedTo ) {
            return $thread->unsubscribe();
        }

        return response([], 404);
    }
}
