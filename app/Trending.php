<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Trending
{
    /**
     * Get trending threads
     * 
     * @return array
     */
    public function get()
    {
        return array_map('json_decode', Redis::zrevrange($this->cachedKey(), 0, 4));
    }

    /**
     * Increase thread score by 1
     * 
     * @param App\Thread $thread
     * 
     * @return array
     */
    public function push(Thread $thread)
    {
        Redis::zincrby($this->cachedKey(), 1, json_encode([
            'title' => $thread->title,
            'path' => $thread->path()
        ]));
    }

    /**
     * Return cached key
     * 
     * @return string
     */
    public function cachedKey()
    {
        return app()->environment('testing') ? 'testing_trending_threads' : 'trending_threads';
    }

    /**
     * Reset data associated with the cached key
     * 
     * @return void
     */
    public function reset()
    {
        Redis::del($this->cachedKey());
    }
}