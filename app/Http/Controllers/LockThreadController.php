<?php

namespace App\Http\Controllers;

use App\Thread;
use Illuminate\Http\Request;

class LockThreadController extends Controller
{
    /**
     * 
     * 
     * @return 
     */
    public function store(Thread $thread) {
        $thread->update([ 'locked' => true ]);

        return response([], 204);
    }

    public function destroy(Thread $thread) {
        $thread->update([ 'locked' => false ]);

        return response([], 204);
    }
}
