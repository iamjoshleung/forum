<?php

namespace App;

use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Model;

class ThreadSubscription extends Model
{
    protected $guarded = [];

    /**
     * relationship between ThreadSubscription & User
     * 
     * @return 
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * relationship between ThreadSubscription & Thread
     * 
     * @return 
     */
    public function thread() {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Notify associated user that the thread was updated
     * 
     * @return 
     */
    public function notify($reply) {
        return $this->user->notify(new ThreadWasUpdated($this->thread, $reply));
    }
}
