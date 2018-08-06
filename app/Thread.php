<?php

namespace App;

use Laravel\Scout\Searchable;
use App\Filters\ThreadFilters;
use App\Traits\RecordsActivity;
use App\Events\ThreadHasNewReply;
use App\Events\ThreadReceivedNewReply;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Stevebauman\Purify\Facades\Purify;

class Thread extends Model
{
    use RecordsActivity, Searchable;

    protected $guarded = [];

    protected $with = ['channel', 'creator'];

    protected $appends = ['isSubscribedTo'];

    protected $casts = [
        'locked' => 'boolean'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function($thread) {
            $thread->replies->each->delete();
        });

        static::created(function($thread) {
            $thread->update([ 'slug' => $thread->title ]);
        });
    }

    /**
     * Many Relationship
     *
     * A thread can have many replies
     * @return object
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * A thread belongs to a creator
     * 
     * @return App\User
     */
    public function creator() {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A thread can add a reply
     * 
     * @param array $reply
     * @return Illuminate\Database\Eloquent\Model
     */
    public function addReply(array $reply) {
        $reply = $this->replies()->create($reply);

        event(new ThreadReceivedNewReply($reply));

        return $reply;
    }

    /**
     * A thread belongs to a channel
     * 
     * @return App\Channel
     */
    public function channel() {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Create a string path
     * 
     * @return 
     */
    public function path() {
        return "/threads/{$this->channel->slug}/{$this->slug}";
    }

    /**
     * Scope a query to filter threads by some constraints
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\ThreadFiltesr $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilters($query, ThreadFilters $filters) {
        return $filters->apply($query);
    }

    /**
     * Subscribe a thread by a user
     * 
     * @return 
     */
    public function subscribe($userId = null) {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);

        return $this;
    }

    /**
     * Unsubscribe a thread by a user
     * 
     * @return 
     */
    public function unsubscribe($userId = null) {
        return $this->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();
    }

    /**
     * Return all subscriptions of this thread
     * 
     * @return 
     */
    public function subscriptions() {
        return $this->hasMany(ThreadSubscription::class);
    }

    /**
     * Whether the authenticated user is subscribed to the thread
     * 
     * @return bool
     */
    public function getIsSubscribedToAttribute() {
        return $this->subscriptions()->where('user_id', auth()->id())->exists();
    }

    /**
     * 
     * 
     * @return 
     */
    public function hasUpdatesFor($user) {
        $key = $user->visitedThreadCacheKey($this);
        return $this->updated_at > cache($key);
    }

    /**
     * 
     * 
     * @return 
     */
    public function visits() {
        return $this->visits;
    }

    /**
     * 
     * 
     * @return 
     */
    public function getRouteKeyName() {
        return 'slug';
    }

    /**
     * 
     * 
     * @return 
     */
    public function setSlugAttribute($value) {
        $slug = str_slug($value);

        if( static::whereSlug($slug)->exists() ) {
            $slug = "{$slug}-" . $this->id;
        }

        return $this->attributes['slug'] = $slug;
    }

    /**
     * 
     * 
     * @return 
     */
    public function markBestReply(Reply $reply) {
        $this->update(['best_reply_id' => $reply->id]);
    }

    public function toSearchableArray()
    {
        return $this->toArray() + ['path' => $this->path()];
    }

    /**
     * 
     * 
     * @return 
     */
    public function getBodyAttribute($body) {
        return Purify::clean($body);
    }
}
