<?php

namespace App\Traits;

use App\Favourite;

trait Favouritable {


    /**
     * 
     * 
     * @return 
     */
    static public function bootFavouritable() {
        static::deleting(function($model) {
            $model->favourites->each->delete();
        });
    }

    /**
     * MorphMany relationship for favourites
     * 
     * @return object
     */
    public function favourites() {
        return $this->morphMany(Favourite::class, 'favourited');
    }

    /**
     * 
     * 
     * @return 
     */
    public function favourite() {
        $attributes = ['user_id' => auth()->id()];
        if( ! $this->favourites()->where($attributes)->exists() ) {
            return $this->favourites()->create($attributes);
        }
    }

    public function unfavourite() {
        $attributes = ['user_id' => auth()->id()];

        return $this->favourites()->where($attributes)->get()->each->delete();
        // if( ! $this->favourites()->where($attributes)->exists() ) {
        //     return $this->favourites()->create($attributes);
        // }
    }

    /**
     * 
     * 
     * @return 
     */
    public function isFavourited() {
        // return auth()->check() && $this->owner->id === auth()->id();
        return !! $this->favourites->where('user_id', auth()->id())->count();
    }

    /**
     * 
     * 
     * @return 
     */
    public function getIsFavouritedAttribute() {
        return $this->isFavourited();
    }

    public function getFavouritesCountAttribute() {
        return $this->favourites->count();
    }
}