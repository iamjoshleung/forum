<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar_path'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email'
    ];

    protected $casts = [
        'confirmed' => 'boolean'
    ];

    /**
     * 
     * 
     * @return 
     */
    public function getRouteKeyName()
    {
        return 'name';
    }

    public function threads()
    {
        return $this->hasMany(Thread::class)->latest();
    }

    /**
     * 
     * 
     * @return 
     */
    public function activity()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * 
     * 
     * @return 
     */
    public function confirm() {
        $this->confirmed = true;
        $this->confirmation_token = null;
        $this->save();
    }

    /**
     * 
     * 
     * @return 
     */
    public function visitedThreadCacheKey($thread)
    {
        return sprintf("users.%s.visits.%s", $this->id, $thread->id);
    }

    /**
     * 
     * 
     * @return 
     */
    public function readThread($thread)
    {
        cache()->forever(
            $this->visitedThreadCacheKey($thread),
            Carbon::now()
        );
    }

    /**
     * Fetch a user's latest reply
     * 
     * @return 
     */
    public function lastReply() {
        return $this->hasOne(Reply::class)->latest();
    }

    /**
     * Return user's avatar if exists
     * 
     * @return 
     */
    public function getAvatarPathAttribute($avatar) {
        return asset('storage/' . ($avatar ?: 'avatars/default.jpg'));
    }

    /**
     * 
     * 
     * @return 
     */
    public function isAdmin() {
        return in_array($this->name, ['JoshuaLeung']);
    }
}

