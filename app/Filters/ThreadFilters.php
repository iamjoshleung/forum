<?php

namespace App\Filters;

use App\User;
use App\Filters\Filters;
use Illuminate\Http\Request;

class ThreadFilters extends Filters {

    /**
     * Registered filters to operate upon.
     *
     * @var array
     */
    protected $filters = ['by', 'popular', 'unanwsered'];

    /**
     * Filter threads by username
     * 
     * @param  string $username
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function by($value) {
        $user = User::where('name', $value)->firstOrFail();

        return $this->builder->where('user_id', $user->id);
    }

    /**
     * Filter threads by popularity (replies count)
     * 
     * @return $this
     */
    public function popular() {
        $this->builder->getQuery()->orders = [];
        return $this->builder->orderBy('replies_count', 'desc');
    }

    /**
     * filter threads by unanwsered 
     * 
     * @return 
     */
    public function unanwsered() {
        return $this->builder->where('replies_count', 0);
    }
}