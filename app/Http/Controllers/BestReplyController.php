<?php

namespace App\Http\Controllers;

use App\Reply;
use Illuminate\Http\Request;

class BestReplyController extends Controller
{
    /**
     * 
     * 
     * @return 
     */
    public function store(Reply $reply) {
        $this->authorize('update', $reply->thread);
        $reply->thread->markBestReply($reply);
    }
}
