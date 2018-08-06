<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Favouritable;
use App\Traits\RecordsActivity;

class Reply extends Model
{
    use Favouritable, RecordsActivity;

    protected $guarded = [];

    protected $with = ['favourites', 'owner'];

    protected $appends = ['favouritesCount', 'isFavourited', 'isBest'];

    protected static function boot() {
        parent::boot();
        
        static::created(function($reply) {
            $reply->thread->increment('replies_count');
        });

        static::deleted(function($reply) {
            $reply->thread->decrement('replies_count');
        });
    }

    /**
     * Belongs to relationship
     * A reply belongs to a user
     * @return object
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 
     * 
     * @return 
     */
    public function thread() {
        return $this->belongsTo(Thread::class);
    }

    public function path() {
        return $this->thread->path() . "#reply-{$this->id}";
    }

    /**
     * Whether the reply was just published (within a minute)
     * 
     * @return bool
     */
    public function wasJustPublished() {
        return $this->created_at->gt(now()->subMinute());
    }

    /**
     * 
     * 
     * @return 
     */
    public function mentionedUsers() {
        preg_match_all('/\@([\w\-]+)/', $this->body, $matches);

        return $matches[1];
    }

    /**
     * 
     * 
     * @return 
     */
    public function setBodyAttribute($body) {
        $this->attributes['body'] = preg_replace('/@([\w\-]+)/', '<a href="/profiles/$1">$0</a>', $body);
    }

    /**
     * 
     * 
     * @return 
     */
    public function isBest() {
        // dd($this->thread->best_reply_id);
        return $this->thread->best_reply_id == $this->id;
    }

    /**
     * 
     * 
     * @return 
     */
    public function getIsBestAttribute() {
        return $this->isBest();
    }

    public function getBodyAttribute($body) {
        return \Purify::clean($body);
    }
 }

