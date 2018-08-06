<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $guarded = [];

    public function subject() {
        return $this->morphTo();
    }

    /**
     * 
     * 
     * @return 
     */
    public static function feed($user, $take = 50) {
        return static::where('user_id', $user->id)
            ->with('subject')
            ->latest()
            ->take($take)
            ->get()
            ->groupBy(function($item, $key) {
                return $item->created_at->format('Y-m-d');
            });
    }
}
